<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    DB::statement("CREATE TABLE IF NOT EXISTS focus_task_sessions (
      id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      task_id CHAR(36) NOT NULL,
      focus_date DATE NOT NULL,
      notes TEXT NULL,
      created_at TIMESTAMP NULL,
      updated_at TIMESTAMP NULL,
      INDEX idx_task_id (task_id),
      INDEX idx_focus_date (focus_date),
      UNIQUE KEY unique_task_date (task_id, focus_date)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    
    echo "Table created successfully!";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
