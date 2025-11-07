<?php

namespace App\Services;

class InsightGenerator
{
    /**
     * Build short, manager-friendly insights from the dashboard dataset.
     * Expected $dataset keys (all optional): attendancePct[], avgScore[], teams{ team => ['score'=>.., 'att'=>..] }, members[], activeMembers, targetActive
     *
     * @param array $dataset
     * @return array  Each item: ['icon' => 'bi bi-...', 'tone' => 'success|warning|danger|info|secondary|primary', 'text' => '<html>...']
     */
    public function fromDashboardDataset(array $dataset)
    {
        $insights = array();

        // ---- 1) Week-over-week attendance delta (last 2 points in attendancePct) ----
        $att = (isset($dataset['attendancePct']) && is_array($dataset['attendancePct'])) ? $dataset['attendancePct'] : array();
        $n   = count($att);
        if ($n >= 2) {
            $prev = (float)$att[$n - 2];
            $curr = (float)$att[$n - 1];
            $delta = ($prev == 0.0) ? 0.0 : (($curr - $prev) / $prev) * 100.0;

            $tone = ($delta >= 0) ? 'success' : 'danger';
            $icon = ($delta >= 0) ? 'bi bi-arrow-up-right' : 'bi bi-arrow-down-right';
            $text = 'Attendance ' . (($delta >= 0) ? 'increased' : 'decreased') . ' '
                . number_format(abs($delta), 1) . '% week-over-week.';

            $insights[] = array('icon' => $icon, 'tone' => $tone, 'text' => $text);
        }

        // ---- 2) Top team by composite score ----
        if (isset($dataset['teams']) && is_array($dataset['teams']) && count($dataset['teams']) > 0) {
            $bestTeam = null;
            $bestScore = null;
            foreach ($dataset['teams'] as $teamName => $vals) {
                if (is_array($vals) && isset($vals['score'])) {
                    $sc = (float)$vals['score'];
                    if ($bestScore === null || $sc > $bestScore) {
                        $bestScore = $sc;
                        $bestTeam  = $teamName;
                    }
                }
            }
            if ($bestTeam !== null) {
                $insights[] = array(
                    'icon' => 'bi bi-award',
                    'tone' => 'primary',
                    'text' => 'Top team: <strong>' . htmlspecialchars($bestTeam, ENT_QUOTES, 'UTF-8') . '</strong> (' . number_format($bestScore, 1) . ').'
                );
            }
        }

        // ---- 3) At-risk count: attendance < 60 OR score < 60 ----
        $riskCount = 0;
        if (isset($dataset['members']) && is_array($dataset['members'])) {
            foreach ($dataset['members'] as $m) {
                $attv = isset($m['att']) ? (float)$m['att'] : 100.0;
                $scv  = isset($m['score']) ? (float)$m['score'] : 100.0;
                if ($attv < 60.0 || $scv < 60.0) {
                    $riskCount++;
                }
            }
        }
        if ($riskCount > 0) {
            $insights[] = array(
                'icon' => 'bi bi-exclamation-triangle',
                'tone' => 'warning',
                'text' => $riskCount . ' member(s) at risk (attendance &lt; 60% or score &lt; 60).'
            );
        }

        // ---- 4) Momentum over last 3 points in avgScore ----
        if (isset($dataset['avgScore']) && is_array($dataset['avgScore']) && count($dataset['avgScore']) >= 3) {
            $c = count($dataset['avgScore']);
            $first = (float)$dataset['avgScore'][$c - 3];
            $last  = (float)$dataset['avgScore'][$c - 1];
            $diff  = $last - $first;

            $insights[] = array(
                'icon' => ($diff >= 0) ? 'bi bi-graph-up' : 'bi bi-graph-down',
                'tone' => ($diff >= 0) ? 'success' : 'danger',
                'text' => 'Performance momentum ' . (($diff >= 0) ? 'up' : 'down') . ' ' . number_format(abs($diff), 1) . ' pts over last 3 periods.'
            );
        }

        // ---- 5) Active members vs target ----
        if (isset($dataset['activeMembers']) && isset($dataset['targetActive']) && (int)$dataset['targetActive'] > 0) {
            $am   = (int)$dataset['activeMembers'];
            $tgt  = (int)$dataset['targetActive'];
            $pct  = ($tgt > 0) ? ($am / $tgt) * 100.0 : 0.0;
            $tone = ($pct >= 100.0) ? 'success' : (($pct >= 80.0) ? 'info' : 'secondary');

            $insights[] = array(
                'icon' => 'bi bi-people',
                'tone' => $tone,
                'text' => 'Active members: ' . $am . ' (' . number_format($pct, 0) . '% of target ' . $tgt . ').'
            );
        }

        return $insights;
    }
}