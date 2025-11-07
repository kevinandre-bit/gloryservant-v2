<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MonthlyDigitalGiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'month' => ['required', 'date'],
            'theme_heading' => ['required', 'string', 'max:255'],
            'welcome_subtext' => ['nullable', 'string'],
            'sermon_title' => ['required', 'string', 'max:255'],
            'sermon_date' => ['nullable', 'date'],
            'sermon_description' => ['nullable', 'string'],
            'sermon_audio_url' => ['required', 'url'],
            'worship_title' => ['required', 'string', 'max:255'],
            'worship_leader' => ['nullable', 'string', 'max:255'],
            'worship_theme_note' => ['nullable', 'string'],
            'worship_audio_url' => ['required', 'url'],
            'testimony_type' => ['nullable', 'string', 'max:255'],
            'testimony_body' => ['required', 'string'],
            'testimony_image_path' => ['nullable', 'string', 'max:255'],
            'verse_reference' => ['required', 'string', 'max:255'],
            'verse_text' => ['required', 'string'],
            'verse_reflection' => ['nullable', 'string'],
            'meditation_paragraphs' => ['nullable', 'array'],
            'meditation_paragraphs.*' => ['nullable', 'string'],
            'artwork_image_path' => ['nullable', 'string', 'max:255'],
            'artwork_caption' => ['nullable', 'string', 'max:255'],
            'wallpaper_phone_url' => ['nullable', 'url'],
            'wallpaper_desktop_url' => ['nullable', 'url'],
            'closing_blessing' => ['nullable', 'string'],
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'meditation_paragraphs' => collect($this->input('meditation_paragraphs', []))
                ->filter(fn ($paragraph) => filled($paragraph))
                ->values()
                ->all(),
        ]);
    }
}
