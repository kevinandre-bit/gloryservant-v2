<?php
// app/Models/PlaylistItem.php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PlaylistItem extends Model
{
    protected $fillable = ['playlist_id','track_id','position','notes'];

    public function playlist() { return $this->belongsTo(Playlist::class); }
    public function track()    { return $this->belongsTo(Track::class); }
}