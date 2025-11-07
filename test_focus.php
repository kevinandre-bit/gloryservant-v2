<?php
// Quick test script - access via browser: /test_focus.php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get a task
$task = \App\Models\Task::first();
if (!$task) {
    die('No tasks found');
}

echo "Task ID: {$task->id}<br>";
echo "Task Title: {$task->title}<br>";

// Try to create a session
try {
    $session = $task->sessions()->create([
        'focus_date' => '2025-01-15'
    ]);
    echo "Session created: {$session->id}<br>";
    echo "Focus date: {$session->focus_date}<br>";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "<br>";
    echo "Trace: " . $e->getTraceAsString();
}

// Check all sessions
echo "<br><br>All sessions for this task:<br>";
foreach ($task->sessions as $s) {
    echo "- {$s->focus_date}<br>";
}
