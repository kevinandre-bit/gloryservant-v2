@extends('layouts.guest')

@section('head')
    <script src="https://cdn.tailwindcss.com" nonce="{{ $cspNonce }}"></script>
    <script nonce="{{ $cspNonce }}">
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#2563eb',
                            foreground: '#ffffff',
                        },
                        surface: '#0f172a',
                    },
                },
            },
        };
    </script>
@endsection

@section('content')
@php
    $forms = [
        [
            'id' => 'graphic',
            'label' => __('Request Graphics'),
            'description' => __('Submit creative briefs for print or digital graphics.'),
            'url' => 'https://form.asana.com/?k=8y4roi6vbCbYSPd1l73jWA&d=34642379201116&embed=true',
        ],
        [
            'id' => 'video',
            'label' => __('Request Video'),
            'description' => __('Ask the media team for filming or video editing support.'),
            'url' => 'https://form.asana.com/?k=YBGvMD6rPApBhJjn2gEj-w&d=34642379201116&embed=true',
        ],
        [
            'id' => 'special',
            'label' => __('Special Request'),
            'description' => __('Need something else? Tell us what you are looking for.'),
            'url' => 'https://form.asana.com/?k=zGNvbvatDUqU_0gXwL_UyA&d=34642379201116&embed=true',
        ],
        [
            'id' => 'website-support',
            'label' => __('Website Support'),
            'description' => __('Report issues or request enhancements for the site.'),
            'url' => 'https://form.asana.com/?k=igXWqRIDD3SIGfSUmaLTiQ&d=34642379201116&embed=true',
        ],
    ];
@endphp
 
<div class="min-h-screen bg-slate-900 pb-16 pt-12">
    <div class="mx-auto w-full max-w-5xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-3xl text-center text-slate-100">
            <img src="{{ asset('assets3/img/logo-white.png') }}" 
     alt="Glory Servant Logo" 
     class="portal-logo mb-3" 
     style="width:30%; display:block; margin:0 auto;">
            <h1 class="mt-6 text-3xl font-semibold tracking-tight text-white sm:text-4xl">
                {{ __('Connect with the Communication Team') }}
            </h1>
            <p class="mt-4 text-base leading-7 text-slate-300">
                {{ __('Choose the form that matches your request. We review new submissions throughout the day and will follow up as soon as possible.') }}
            </p>
        </div>

        <div role="tablist" aria-label="{{ __('Request categories') }}" class="mx-auto mt-10 grid gap-2 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($forms as $form)
                @php $isActive = $loop->first; @endphp
                <button
                    type="button"
                    data-tab-target="{{ $form['id'] }}"
                    class="tab-button flex h-full items-center justify-between gap-3 rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-left text-slate-200 transition hover:border-blue-400/60 hover:bg-white/10 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900 sm:px-5 sm:py-3 {{ $isActive ? 'border-blue-400/60 bg-blue-500/20 ring-2 ring-blue-400' : '' }}"
                    aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                    aria-controls="panel-{{ $form['id'] }}"
                    role="tab"
                >
                    <span class="flex items-center gap-3">
                        <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-blue-500/20 text-sm font-semibold text-blue-200 group-aria-selected:bg-blue-500/40 group-aria-selected:text-blue-100 sm:h-8 sm:w-8">
                            {{ $loop->iteration }}
                        </span>
                        <span class="text-sm font-semibold text-white group-aria-selected:text-blue-100 sm:text-base">
                            {{ $form['label'] }}
                        </span>
                    </span>
                    <svg class="h-4 w-4 text-blue-300 transition group-hover:translate-x-0.5 group-aria-selected:translate-x-0.5" viewBox="0 0 20 20" fill="none" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 5h6m0 0v6m0-6L5 17"></path>
                    </svg>
                </button>
            @endforeach
        </div>

        <div class="relative mt-10 rounded-3xl border border-white/10 bg-slate-950/40 p-6 shadow-2xl shadow-blue-900/50 ring-1 ring-blue-500/20 backdrop-blur-md">
            @foreach ($forms as $form)
                <section
                    id="panel-{{ $form['id'] }}"
                    data-tab-panel="{{ $form['id'] }}"
                    role="tabpanel"
                    tabindex="{{ $loop->first ? '0' : '-1' }}"
                    aria-hidden="{{ $loop->first ? 'false' : 'true' }}"
                    aria-label="{{ $form['label'] }}"
                    class="{{ $loop->first ? '' : 'hidden' }}"
                >
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-white">{{ $form['label'] }}</h2>
                    <p class="mt-1 text-sm text-slate-300">{{ $form['description'] }}</p>
                        </div>
                        <a
                            href="{{ $form['url'] }}"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex items-center gap-2 rounded-full border border-blue-500/20 bg-blue-500/10 px-4 py-2 text-sm font-medium text-blue-200 transition hover:border-blue-400/40 hover:bg-blue-500/20 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-900"
                        >
                            {{ __('Open in Asana') }}
                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 5h8m0 0v8m0-8L5 15"></path>
                            </svg>
                        </a>
                    </div>

                    <div class="relative mt-6 overflow-hidden rounded-2xl bg-slate-900 shadow-inner ring-1 ring-blue-500/20">
                        <div data-skeleton class="pointer-events-none absolute inset-0 flex flex-col gap-4 bg-slate-900/90 p-6 text-slate-300 transition-opacity duration-500">
                            <div class="h-6 w-1/4 animate-pulse rounded bg-slate-700/70"></div>
                            <div class="h-3 w-3/4 animate-pulse rounded bg-slate-700/60"></div>
                            <div class="h-64 animate-pulse rounded-xl bg-slate-800/80"></div>
                            <p class="text-xs text-slate-400">
                                {{ __('Loading the Asana form. If it does not appear, use the button above to open it directly.') }}
                            </p>
                        </div>

                        <iframe
                            data-tab-iframe
                            src="{{ $form['url'] }}"
                            title="{{ $form['label'] }}"
                            loading="lazy"
                            class="h-[32rem] w-full border-0 bg-white"
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</div>

<script nonce="{{ $cspNonce }}">
    document.addEventListener('DOMContentLoaded', () => {
        const tabButtons = Array.from(document.querySelectorAll('[data-tab-target]'));
        const panels = Array.from(document.querySelectorAll('[data-tab-panel]'));

        const activate = (id) => {
            let activePanel = null;
            tabButtons.forEach((button) => {
                const isActive = button.dataset.tabTarget === id;
                button.setAttribute('aria-selected', String(isActive));
                button.classList.toggle('ring-2', isActive);
                button.classList.toggle('ring-blue-400', isActive);
                button.classList.toggle('bg-blue-500/20', isActive);
                button.classList.toggle('border-blue-400/60', isActive);
            });

            panels.forEach((panel) => {
                const node = document.getElementById(`panel-${panel.dataset.tabPanel}`);
                if (!node) {
                    return;
                }
                const isActive = panel.dataset.tabPanel === id;
                node.classList.toggle('hidden', !isActive);
                node.setAttribute('aria-hidden', String(!isActive));
                node.setAttribute('tabindex', isActive ? '0' : '-1');
                if (isActive) {
                    activePanel = node;
                }
            });
            return activePanel;
        };

        tabButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const panel = activate(button.dataset.tabTarget);
                if (panel) {
                    window.requestAnimationFrame(() => {
                        panel.scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'nearest' });
                    });
                }
            });
            button.addEventListener('keydown', (event) => {
                const currentIndex = tabButtons.indexOf(button);
                if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
                    event.preventDefault();
                    const nextButton = tabButtons[(currentIndex + 1) % tabButtons.length];
                    nextButton.focus();
                    const panel = activate(nextButton.dataset.tabTarget);
                    if (panel) {
                        window.requestAnimationFrame(() => {
                            panel.scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'nearest' });
                        });
                    }
                }
                if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
                    event.preventDefault();
                    const prevIndex = (currentIndex - 1 + tabButtons.length) % tabButtons.length;
                    const prevButton = tabButtons[prevIndex];
                    prevButton.focus();
                    const panel = activate(prevButton.dataset.tabTarget);
                    if (panel) {
                        window.requestAnimationFrame(() => {
                            panel.scrollIntoView({ behavior: 'smooth', block: 'start', inline: 'nearest' });
                        });
                    }
                }
            });
        });

        const iframes = document.querySelectorAll('[data-tab-iframe]');
        iframes.forEach((iframe) => {
            iframe.addEventListener('load', (event) => {
                const skeleton = event.target.parentElement?.querySelector('[data-skeleton]');
                if (skeleton) {
                    skeleton.classList.add('opacity-0');
                    setTimeout(() => skeleton.classList.add('hidden'), 300);
                }
            });
        });

        const initiallySelected = tabButtons.find((button) => button.getAttribute('aria-selected') === 'true');
        if (initiallySelected) {
            activate(initiallySelected.dataset.tabTarget);
        }
    });
</script>
@endsection
