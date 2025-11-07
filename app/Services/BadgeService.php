<?php

namespace App\Services;

use App\Models\CreativeBadge;
use App\Models\CreativeTaskEvent;
use DB;

class BadgeService
{
    public function checkAndAwardBadges($peopleId)
    {
        $badges = CreativeBadge::all();
        
        foreach ($badges as $badge) {
            if ($this->hasEarnedBadge($peopleId, $badge)) {
                $this->awardBadge($peopleId, $badge->id);
            }
        }
    }

    private function hasEarnedBadge($peopleId, $badge)
    {
        $criteria = $badge->criteria;
        
        return match($badge->code) {
            'sprinter' => $this->checkSprinter($peopleId),
            'closer' => $this->checkCloser($peopleId),
            'loyal' => $this->checkLoyal($peopleId),
            'creative_pro' => $this->checkCreativePro($peopleId),
            default => false
        };
    }

    private function checkSprinter($peopleId)
    {
        $count = CreativeTaskEvent::where('people_id', $peopleId)
            ->where('event', 'completed')
            ->where('occurred_at', '>=', now()->subDays(7))
            ->count();
        return $count >= 5;
    }

    private function checkCloser($peopleId)
    {
        $count = CreativeTaskEvent::where('people_id', $peopleId)
            ->where('event', 'completed')
            ->whereYear('occurred_at', now()->year)
            ->whereMonth('occurred_at', now()->month)
            ->count();
        return $count >= 20;
    }

    private function checkLoyal($peopleId)
    {
        $totalMinutes = 0;
        $records = DB::table('tbl_people_attendance')
            ->where('reference', $peopleId)
            ->whereYear('date', now()->year)
            ->whereMonth('date', now()->month)
            ->whereNotNull('totalhours')
            ->where('totalhours', '!=', '')
            ->get();
            
        foreach ($records as $record) {
            if (preg_match('/(\d+):(\d+)/', $record->totalhours, $matches)) {
                $totalMinutes += ($matches[1] * 60) + $matches[2];
            }
        }
        
        return $totalMinutes >= 2400; // 40 hours
    }

    private function checkCreativePro($peopleId)
    {
        $count = CreativeTaskEvent::where('people_id', $peopleId)
            ->where('event', 'completed')
            ->count();
        return $count >= 100;
    }

    private function awardBadge($peopleId, $badgeId)
    {
        $inserted = DB::table('tbl_creative_user_badges')->insertOrIgnore([
            'people_id' => $peopleId,
            'badge_id' => $badgeId,
            'awarded_at' => now(),
            'earned_at' => now(),
        ]);
        
        // Send notification if badge was newly awarded
        if ($inserted) {
            $person = \App\Models\Person::find($peopleId);
            $badge = CreativeBadge::find($badgeId);
            
            if ($person && $badge) {
                // Email notification
                if ($person->emailaddress) {
                    \Mail::to($person->emailaddress)->send(new \App\Mail\CreativeBadgeEarned($badge, $person));
                }
                
                // In-app notification
                $user = \App\Models\User::where('reference', $peopleId)->first();
                if ($user) {
                    \DB::table('notifications')->insert([
                        'type' => 'creative_badge_earned',
                        'title' => '🏆 Badge Earned!',
                        'body' => "You earned the {$badge->name} badge!",
                        'subject_table' => 'tbl_creative_badges',
                        'subject_id' => $badgeId,
                        'url' => '/personal/creative/dashboard',
                        'icon' => 'medal',
                        'created_at' => now()
                    ]);
                    
                    \DB::table('notification_targets')->insert([
                        'notification_id' => \DB::getPdo()->lastInsertId(),
                        'user_id' => $user->id
                    ]);
                }
            }
        }
    }
}
