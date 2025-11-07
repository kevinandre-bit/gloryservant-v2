@extends('layouts.admin')

@section('meta')
    <title>Monthly Digital Gift | Glory Servant</title>
@endsection

@section('head-scripts')
    <style>
        .gift-section-title {
            letter-spacing: .18em;
            text-transform: uppercase;
            font-size: .75rem;
            color: #64748b;
        }

        .gift-section + .gift-section {
            border-top: 1px solid rgba(148, 163, 184, .2);
            padding-top: 1.5rem;
            margin-top: 1.5rem;
        }

        .gift-help {
            font-size: .82rem;
            color: #94a3b8;
        }
    </style>
@endsection

@section('content')
<main class="main-wrapper">
    <div class="main-content">
        <div class="page-breadcrumb d-flex align-items-center justify-content-between mb-3">
            <div>
                <h4 class="mb-1">Monthly Digital Gift</h4>
                <p class="text-secondary mb-0">Refresh each month to keep the public landing page current.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="material-icons-outlined me-2 align-middle">check_circle</i>
                {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card rounded-4 border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.monthly-digital-gift.store') }}" class="row g-4">
                    @csrf
                    @php
                        $meditation = old('meditation_paragraphs', optional($gift)->meditation_paragraphs ?? []);
                        $meditation = array_pad($meditation, 3, '');
                    @endphp

                    <div class="col-12 gift-section">
                        <div class="gift-section-title mb-2">General</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Month</label>
                                <input type="date" name="month" class="form-control @error('month') is-invalid @enderror"
                                       value="{{ old('month', optional(optional($gift)->month)->format('Y-m-d')) }}" required>
                                @error('month')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Theme Heading</label>
                                <input type="text" name="theme_heading" class="form-control @error('theme_heading') is-invalid @enderror"
                                       value="{{ old('theme_heading', optional($gift)->theme_heading) }}" required>
                                @error('theme_heading')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Welcome Subtext</label>
                                <textarea name="welcome_subtext" rows="3" class="form-control @error('welcome_subtext') is-invalid @enderror">{{ old('welcome_subtext', optional($gift)->welcome_subtext) }}</textarea>
                                @error('welcome_subtext')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-12 gift-section">
                        <div class="gift-section-title mb-2">Sermon Audio</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Sermon Title</label>
                                <input type="text" name="sermon_title" class="form-control @error('sermon_title') is-invalid @enderror"
                                       value="{{ old('sermon_title', optional($gift)->sermon_title) }}" required>
                                @error('sermon_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Sermon Date</label>
                                <input type="date" name="sermon_date" class="form-control @error('sermon_date') is-invalid @enderror"
                                       value="{{ old('sermon_date', optional(optional($gift)->sermon_date)->format('Y-m-d')) }}">
                                @error('sermon_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea name="sermon_description" rows="3" class="form-control @error('sermon_description') is-invalid @enderror">{{ old('sermon_description', optional($gift)->sermon_description) }}</textarea>
                                @error('sermon_description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Audio URL</label>
                                <input type="url" name="sermon_audio_url" class="form-control @error('sermon_audio_url') is-invalid @enderror"
                                       value="{{ old('sermon_audio_url', optional($gift)->sermon_audio_url) }}" required>
                                @error('sermon_audio_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-12 gift-section">
                        <div class="gift-section-title mb-2">Worship Audio</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Worship Title</label>
                                <input type="text" name="worship_title" class="form-control @error('worship_title') is-invalid @enderror"
                                       value="{{ old('worship_title', optional($gift)->worship_title) }}" required>
                                @error('worship_title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Leader / Artist</label>
                                <input type="text" name="worship_leader" class="form-control @error('worship_leader') is-invalid @enderror"
                                       value="{{ old('worship_leader', optional($gift)->worship_leader) }}">
                                @error('worship_leader')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Theme Note</label>
                                <input type="text" name="worship_theme_note" class="form-control @error('worship_theme_note') is-invalid @enderror"
                                       value="{{ old('worship_theme_note', optional($gift)->worship_theme_note) }}">
                                @error('worship_theme_note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Audio URL</label>
                                <input type="url" name="worship_audio_url" class="form-control @error('worship_audio_url') is-invalid @enderror"
                                       value="{{ old('worship_audio_url', optional($gift)->worship_audio_url) }}" required>
                                @error('worship_audio_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-12 gift-section">
                        <div class="gift-section-title mb-2">Written Testimony</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Testimony Type</label>
                                <input type="text" name="testimony_type" class="form-control @error('testimony_type') is-invalid @enderror"
                                       value="{{ old('testimony_type', optional($gift)->testimony_type) }}">
                                @error('testimony_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Image URL (optional)</label>
                                <input type="text" name="testimony_image_path" class="form-control @error('testimony_image_path') is-invalid @enderror"
                                       value="{{ old('testimony_image_path', optional($gift)->testimony_image_path) }}">
                                @error('testimony_image_path')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Story</label>
                                <textarea name="testimony_body" rows="4" class="form-control @error('testimony_body') is-invalid @enderror" required>{{ old('testimony_body', optional($gift)->testimony_body) }}</textarea>
                                @error('testimony_body')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-12 gift-section">
                        <div class="gift-section-title mb-2">Verse of the Month</div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Reference</label>
                                <input type="text" name="verse_reference" class="form-control @error('verse_reference') is-invalid @enderror"
                                       value="{{ old('verse_reference', optional($gift)->verse_reference) }}" required>
                                @error('verse_reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Verse Text</label>
                                <textarea name="verse_text" rows="2" class="form-control @error('verse_text') is-invalid @enderror" required>{{ old('verse_text', optional($gift)->verse_text) }}</textarea>
                                @error('verse_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Reflection</label>
                                <textarea name="verse_reflection" rows="3" class="form-control @error('verse_reflection') is-invalid @enderror">{{ old('verse_reflection', optional($gift)->verse_reflection) }}</textarea>
                                @error('verse_reflection')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-12 gift-section">
                        <div class="gift-section-title mb-2">Short Meditation</div>
                        <p class="gift-help mb-3">Up to three short paragraphs. Leave blank to omit.</p>
                        <div class="row g-3">
                            @foreach($meditation as $index => $paragraph)
                                <div class="col-12">
                                    <label class="form-label">Paragraph {{ $index + 1 }}</label>
                                    <textarea name="meditation_paragraphs[]" rows="3" class="form-control @error('meditation_paragraphs.' . $index) is-invalid @enderror">{{ old('meditation_paragraphs.' . $index, $paragraph) }}</textarea>
                                    @error('meditation_paragraphs.' . $index)<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-12 gift-section">
                        <div class="gift-section-title mb-2">Artwork & Wallpapers</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Artwork Image URL</label>
                                <input type="text" name="artwork_image_path" class="form-control @error('artwork_image_path') is-invalid @enderror"
                                       value="{{ old('artwork_image_path', optional($gift)->artwork_image_path) }}">
                                @error('artwork_image_path')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Artwork Caption</label>
                                <input type="text" name="artwork_caption" class="form-control @error('artwork_caption') is-invalid @enderror"
                                       value="{{ old('artwork_caption', optional($gift)->artwork_caption) }}">
                                @error('artwork_caption')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Wallpaper URL</label>
                                <input type="url" name="wallpaper_phone_url" class="form-control @error('wallpaper_phone_url') is-invalid @enderror"
                                       value="{{ old('wallpaper_phone_url', optional($gift)->wallpaper_phone_url) }}">
                                @error('wallpaper_phone_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Desktop Wallpaper URL</label>
                                <input type="url" name="wallpaper_desktop_url" class="form-control @error('wallpaper_desktop_url') is-invalid @enderror"
                                       value="{{ old('wallpaper_desktop_url', optional($gift)->wallpaper_desktop_url) }}">
                                @error('wallpaper_desktop_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-12 gift-section">
                        <div class="gift-section-title mb-2">Closing Blessing</div>
                        <textarea name="closing_blessing" rows="2" class="form-control @error('closing_blessing') is-invalid @enderror">{{ old('closing_blessing', optional($gift)->closing_blessing) }}</textarea>
                        @error('closing_blessing')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="material-icons-outlined me-1 align-middle">save</i>
                            Save Monthly Digital Gift
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
@endsection
