@extends('layouts.radio_layout')
@php use App\Classes\permission; @endphp

@section('meta')
  <title>{{ __('Create Playlist') }} | Glory Servant</title>
@endsection

@section('content')
<main class="main-wrapper">
  <div class="main-content">
    <div class="page-header d-flex justify-content-between align-items-center mb-3">
      <h4 class="page-title mb-0">{{ __('Create Playlist') }}</h4>
      <a href="{{ route('program.playlists.index') }}" class="btn btn-light btn-sm">
        <i class="material-icons-outlined">arrow_back</i> {{ __('Back') }}
      </a>
    </div>

    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ route('program.playlists.store') }}" class="row g-3">
          @csrf
          <div class="col-12 col-md-6">
            <label class="form-label">{{ __('Name') }}</label>
            <input name="name" class="form-control" required />
          </div>
          <div class="col-12">
            <button class="btn btn-primary">
              <i class="material-icons-outlined">save</i> {{ __('Create') }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</main>
@endsection