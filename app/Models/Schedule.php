<?php
// app/Models/Schedule.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'tbl_people_schedules';

    // Allow mass assignment for all the columns we insert:
    protected $fillable = [
        'reference','employee',
        'start_time','end_time','max_start_time','max_end_time',
        'datefrom','dateto',
        'campus','department','ministry',
        'is_active','archive','notes',
        'hours','restday',
    ];
}