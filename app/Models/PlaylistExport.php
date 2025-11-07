<?php
// app/Models/PlaylistExport.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PlaylistExport extends Model
{
    protected $fillable = ['playlist_id','filename','meta'];
    protected $casts = ['meta' => 'array'];
}