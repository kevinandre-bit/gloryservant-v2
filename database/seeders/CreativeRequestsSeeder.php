<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CreativeRequestsSeeder extends Seeder
{
    public function run()
    {
        $peopleIds = DB::table('tbl_people')->pluck('id')->toArray();
        if (empty($peopleIds)) {
            $this->command->error('No people found in database');
            return;
        }

        $requests = [
            ['type' => 'graphic', 'title' => 'Easter Sunday Service Flyer', 'ministry' => 'Worship', 'priority' => 'urgent', 'campus' => ['Miami', 'Orlando']],
            ['type' => 'video', 'title' => 'Youth Ministry Promo Video', 'ministry' => 'Youth', 'priority' => 'high', 'campus' => ['Boston']],
            ['type' => 'graphic', 'title' => 'Small Groups Registration Banner', 'ministry' => 'Small Groups', 'priority' => 'normal', 'campus' => ['New York']],
            ['type' => 'video', 'title' => 'Testimony - Maria Rodriguez', 'ministry' => 'Media', 'priority' => 'normal', 'campus' => ['Miami']],
            ['type' => 'graphic', 'title' => 'Christmas Concert Social Media Post', 'ministry' => 'Worship', 'priority' => 'high', 'campus' => ['Santiago']],
            ['type' => 'graphic', 'title' => 'Baptism Class Information Card', 'ministry' => 'Discipleship', 'priority' => 'low', 'campus' => ['Montreal']],
            ['type' => 'video', 'title' => 'Sunday Service Highlights Reel', 'ministry' => 'Media', 'priority' => 'normal', 'campus' => ['Miami', 'Boston']],
            ['type' => 'graphic', 'title' => 'Volunteer Appreciation Event Poster', 'ministry' => 'Volunteers', 'priority' => 'normal', 'campus' => ['West Palm Beach']],
            ['type' => 'video', 'title' => 'Missions Trip Recap Video', 'ministry' => 'Missions', 'priority' => 'low', 'campus' => ['Orlando']],
            ['type' => 'graphic', 'title' => 'Kids Ministry Summer Camp Flyer', 'ministry' => 'Kids', 'priority' => 'urgent', 'campus' => ['Cap Haitian']],
            ['type' => 'graphic', 'title' => 'Prayer Night Instagram Story', 'ministry' => 'Prayer', 'priority' => 'high', 'campus' => ['Port au Prince']],
            ['type' => 'video', 'title' => 'Pastor Message - Faith Series', 'ministry' => 'Pastoral', 'priority' => 'urgent', 'campus' => ['Miami']],
            ['type' => 'graphic', 'title' => 'Womens Conference Registration Banner', 'ministry' => 'Women', 'priority' => 'high', 'campus' => ['Boston', 'New York']],
            ['type' => 'video', 'title' => 'Worship Team Behind the Scenes', 'ministry' => 'Worship', 'priority' => 'low', 'campus' => ['Santiago']],
            ['type' => 'graphic', 'title' => 'Mens Breakfast Event Flyer', 'ministry' => 'Men', 'priority' => 'normal', 'campus' => ['Orlando']],
            ['type' => 'graphic', 'title' => 'Bible Study Group Invitation Card', 'ministry' => 'Small Groups', 'priority' => 'normal', 'campus' => ['Montreal']],
            ['type' => 'video', 'title' => 'New Members Welcome Video', 'ministry' => 'Connections', 'priority' => 'high', 'campus' => ['Miami']],
            ['type' => 'graphic', 'title' => 'Food Drive Awareness Poster', 'ministry' => 'Outreach', 'priority' => 'urgent', 'campus' => ['West Palm Beach']],
            ['type' => 'video', 'title' => 'Youth Camp Highlights 2025', 'ministry' => 'Youth', 'priority' => 'normal', 'campus' => ['Boston', 'Orlando']],
            ['type' => 'graphic', 'title' => 'Sunday Service Announcement Slide', 'ministry' => 'Communications', 'priority' => 'high', 'campus' => ['New York']],
        ];

        foreach ($requests as $index => $req) {
            $personId = $peopleIds[array_rand($peopleIds)];
            $person = DB::table('tbl_people')->where('id', $personId)->first();
            
            $formData = $req['type'] === 'graphic' ? [
                'graphic_project_type' => ['new'],
                'graphic_output_type' => 'digital',
                'graphic_projected' => 'yes',
                'graphic_text_content' => 'Event details and call to action',
                'graphic_logos' => ['Tabernacle of Glory English', 'Social Media'],
                'graphic_sizes' => ['Screen: 1920x1080', 'Social Square (1:1): 1080x1080'],
            ] : [
                'video_project_type' => ['new'],
                'video_platforms' => ['YouTube', 'Instagram', 'Facebook'],
                'video_orientation' => '16:9',
                'video_resolution' => '1080p',
                'video_music' => 'worship',
                'video_subtitles' => 'yes',
            ];

            DB::table('tbl_creative_requests')->insert([
                'title' => $req['title'],
                'description' => 'Request for ' . $req['title'] . ' to support ' . $req['ministry'] . ' ministry activities.',
                'request_type' => $req['type'],
                'priority' => $req['priority'],
                'status' => ['pending', 'in_progress', 'completed'][array_rand(['pending', 'in_progress', 'completed'])],
                'requester_people_id' => $personId,
                'requester_name' => $person->firstname . ' ' . $person->lastname,
                'requester_ministry' => $req['ministry'],
                'requester_email' => strtolower($person->firstname) . '@tg.org',
                'desired_due_at' => Carbon::now()->addDays(rand(3, 30)),
                'admin_approved' => true,
                'form_data' => json_encode(array_merge($formData, ['campus' => $req['campus']])),
                'created_at' => Carbon::now()->subDays(rand(0, 15)),
                'updated_at' => Carbon::now()->subDays(rand(0, 5)),
            ]);
        }

        $this->command->info('Created 20 creative requests successfully!');
    }
}
