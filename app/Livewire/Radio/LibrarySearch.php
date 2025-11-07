<?php

namespace App\Http\Livewire\Radio;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Track;
use App\Models\Playlist;
use Illuminate\Database\Eloquent\Builder;

class LibrarySearch extends Component
{
    use WithPagination;

    public $playlistId;
    public $q = '';
    public $category = null;
    public $year = null;
    public $perPage = 15;
    public $preventDuplicates = true;

    protected $updatesQueryString = ['q','category','year','page'];
    protected $listeners = ['refreshLibrary' => '$refresh'];

    public function mount($playlistId)
    {
        $this->playlistId = (int) $playlistId;
    }

    public function updating($name, $value)
    {
        if (in_array($name, ['q','category','year'])) {
            $this->resetPage();
        }
    }

    public function add($trackId)
    {
        $playlist = Playlist::findOrFail($this->playlistId);

        if ($this->preventDuplicates &&
            $playlist->items()->where('track_id', $trackId)->exists()) {
            $this->dispatchBrowserEvent('toast', ['variant'=>'warning','message'=>__('Track already in playlist.')]);
            return;
        }

        $next = ($playlist->items()->max('position') ?? -1) + 1;
        $playlist->items()->create(['track_id'=>$trackId, 'position'=>$next]);

        // Notify the other component (v2 syntax)
        $this->emitTo('radio.playlist-items', 'itemAdded');

        $this->dispatchBrowserEvent('toast', ['variant'=>'success','message'=>__('Added to playlist.')]);
    }

    public function render()
    {
        $q = Track::query();

        if ($this->q !== '') {
            $needle = '%'.str_replace(' ','%',$this->q).'%';
            $q->where(function (Builder $b) use ($needle) {
                $b->where('filename','like',$needle)
                  ->orWhere('performer','like',$needle)
                  ->orWhere('category','like',$needle)
                  ->orWhere('theme','like',$needle)
                  ->orWhere('relative_path','like',$needle)
                  ->orWhere('year','like',$needle);
            });
        }
        

        if ($this->category) $q->where('category', $this->category);
        if ($this->year)     $q->where('year', $this->year);

        $tracks = $q->orderBy('filename')->paginate($this->perPage);

        $categories = Track::query()->select('category')->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
        $years      = Track::query()->select('year')->whereNotNull('year')->distinct()->orderByDesc('year')->pluck('year');

        return view('livewire.radio.library-search', compact('tracks','categories','years'));
    }
}