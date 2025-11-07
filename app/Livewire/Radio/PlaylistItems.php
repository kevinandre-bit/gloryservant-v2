<?php

namespace App\Http\Livewire\Radio;

use Livewire\Component;
use App\Models\Playlist;
use App\Models\PlaylistItem;
use App\Support\Time;
use Carbon\CarbonImmutable;

class PlaylistItems extends Component
{
    public $playlistId;
    public $startAt;
    public $timezone;
    public $items = [];
    public $totalSeconds = 0;

    protected $listeners = ['itemAdded' => 'reloadItems'];

    public function mount($playlistId, $startAt = null)
    {
        $this->playlistId = (int) $playlistId;
        $this->timezone = config('app.timezone', 'UTC');
        $this->startAt = $startAt ?: now()->format('Y-m-d\TH:i');
        $this->reloadItems();
    }

    public function reloadItems()
    {
        $playlist = Playlist::with(['items.track'])->findOrFail($this->playlistId);

        $this->items = $playlist->items->map(function($it){
            return [
                'itemId'    => $it->id,
                'id'        => $it->track->id,
                'title'     => $it->track->filename ?: '—',
                'path'      => ($it->track->relative_path ? $it->track->relative_path.'/' : '') . ($it->track->filename ?: ''),
                'performer' => $it->track->performer,
                'category'  => $it->track->category,
                'theme'     => $it->track->theme,
                'year'      => $it->track->year,
                'duration'  => (int) $it->track->duration_seconds,
                'position'  => (int) $it->position,
            ];
        })->values()->all();

        $this->recalc();
    }

    public function updatedStartAt()
    {
        // re-render to update schedule
    }

    public function recalc()
    {
        $this->totalSeconds = array_sum(array_column($this->items, 'duration'));
    }

    public function remove($itemId)
    {
        PlaylistItem::where('id',$itemId)->where('playlist_id',$this->playlistId)->delete();
        $this->reloadItems();
        $this->dispatchBrowserEvent('toast', ['variant'=>'success','message'=>__('Removed.')]);
    }

    public function moveUp($index)
    {
        if ($index <= 0) return;
        $this->swapPositions($index, $index-1);
    }

    public function moveDown($index)
    {
        if ($index >= count($this->items)-1) return;
        $this->swapPositions($index, $index+1);
    }

    protected function swapPositions($a, $b)
    {
        $tmp = $this->items[$a];
        $this->items[$a] = $this->items[$b];
        $this->items[$b] = $tmp;
        $this->persistOrder();
    }

    public function persistOrder()
    {
        foreach ($this->items as $idx => &$item) $item['position'] = $idx;

        foreach ($this->items as $it) {
            PlaylistItem::where('id', $it['itemId'])->update(['position' => $it['position']]);
        }

        $this->recalc();
    }

    protected function getSchedule()
    {
        $start = CarbonImmutable::parse($this->startAt, $this->timezone);
        $rows = array_map(function($it){ return ['id'=>$it['itemId'], 'duration'=>$it['duration']]; }, $this->items);
        return Time::schedule($start, $rows);
    }

   public function render()
{
    $schedule = $this->getSchedule();
    $start    = \Illuminate\Support\Carbon::parse($this->startAt, $this->timezone);
    $end      = (clone $start)->addSeconds($this->totalSeconds);

    return view('livewire.radio.playlist-items', [
        'items'        => $this->items, // <-- pass it explicitly
        'schedule'     => $schedule,
        'totalHms'     => \App\Support\Time::formatHms($this->totalSeconds),
        'startDisplay' => $start->format('Y-m-d H:i'),
        'endDisplay'   => $end->format('Y-m-d H:i'),
    ]);
}
}