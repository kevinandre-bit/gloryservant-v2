@extends('layouts.radio_layout')
@section('meta')
  <title>{{ __('Upload Library CSV') }} | Glory Servant</title>
@endsection

@section('content')
<main class="main-wrapper">
  <div class="main-content">
    <div class="page-header mb-3">
      <h4 class="page-title">{{ __('Upload Latest Library File') }}</h4>
    </div>

    <div class="card">
      <div class="card-body">
        <form action="{{ route('program.library.upload.post') }}" method="POST" enctype="multipart/form-data">
          @csrf
          <div class="mb-3">
            <label class="form-label">{{ __('Select Library File (.csv or .json)') }}</label>
            <input type="file" name="library_file" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-primary">
            <i class="material-icons-outlined">upload_file</i> {{ __('Upload & Import') }}
          </button>
        </form>
      </div>
    </div>
  </div>
</main>
@endsection