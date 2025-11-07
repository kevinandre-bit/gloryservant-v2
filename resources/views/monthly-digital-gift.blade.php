@extends('layouts.admin_v2')

@section('meta')
  <title>Monthly Digital Gift | Glory Servant</title>
@endsection

@section('content')
@php
    $monthLabel = optional(optional($gift)->month)->format('F Y');
    $welcome = optional($gift)->welcome_subtext ?? "We are preparing this month's spiritual gift. Check back soon for fresh encouragement.";
    $meditations = collect(optional($gift)->meditation_paragraphs ?? [])->filter();
    $testimonyParagraphs = collect(preg_split("/(\r?\n){2,}/", trim($gift->testimony_body ?? '')))->filter();
    $theme = strtolower($gift->theme_heading ?? '');
    $artworkThemeMap = [
        'strength' => asset('assets/images/mdg/art-strength.jpg'),
        'peace' => asset('assets/images/mdg/art-peace.jpg'),
        'hope' => asset('assets/images/mdg/art-hope.jpg'),
        'joy' => asset('assets/images/mdg/art-joy.jpg'),
    ];
    $ambientThemeMap = [
        'strength' => asset('assets/videos/ambient-mountains.mp4'),
        'renewal' => asset('assets/videos/ambient-sunrise.mp4'),
        'peace' => asset('assets/videos/ambient-river.mp4'),
        'hope' => asset('assets/videos/ambient-sky.mp4'),
    ];
    $dynamicArtwork = $gift->artwork_image_path
        ?? collect($artworkThemeMap)->first(fn ($path, $keyword) => str_contains($theme, $keyword))
        ?? asset('assets/images/mdg/art-default.jpg');
    $ambientVideo = collect($ambientThemeMap)->first(fn ($path, $keyword) => str_contains($theme, $keyword))
        ?? asset('assets/videos/ambient-stars.mp4');
    $wowMessage = "Thank you for being a Centurion.";
@endphp

<style>
  @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Lora:wght@500&display=swap');

  :root {
    --mdg-bg-start: #e9edf8;
    --mdg-bg-end: #f9fbff;
    --mdg-hero: #0d193a;
    --mdg-surface: rgba(255,255,255,0.97);
    --mdg-border: rgba(148, 163, 184, 0.22);
    --mdg-shadow: 0 18px 40px rgba(12, 18, 46, 0.16);
    --mdg-accent: #5164ff;
    --mdg-accent-soft: rgba(81,100,255,0.18);
    --mdg-focus-bg: #050913;
    --mdg-focus-text-scale: 1.12;
    --mdg-body-bg: #e7ecff;
    --mdg-body-text: #162347;
    --mdg-dark-bg: #050913;
    --mdg-dark-surface: rgba(11,15,28,0.92);
    --mdg-dark-border: rgba(148,163,184,0.18);
  }

  body {
    background: var(--mdg-body-bg);
    color: var(--mdg-body-text);
    transition: background 0.4s ease, color 0.4s ease;
    font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  }

  body.mdg-focus-mode {
    background: var(--mdg-focus-bg);
    color: #eff2ff;
  }

  body.mdg-dark {
    background: var(--mdg-dark-bg);
    color: #dbe6ff;
  }

  .mdg-shell {
    background: transparent;
    padding: 72px 0 120px;
    position: relative;
  }

  body.mdg-focus-mode .mdg-shell {
    background: radial-gradient(circle at 20% 20%, rgba(33, 37, 73, 0.85), transparent 55%), var(--mdg-focus-bg);
  }

  body.mdg-dark .mdg-shell {
    background: transparent;
  }

  .mdg-hero {
    border-radius: 36px;
    background: linear-gradient(135deg, #0f1b46, #274cda 48%, #6fe0ff 100%);
    color: #fff;
    padding: clamp(2.5rem, 6vw, 4rem);
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(0, 360px);
    gap: clamp(2rem, 5vw, 4rem);
    box-shadow: var(--mdg-shadow);
    position: relative;
    overflow: hidden;
  }

  .mdg-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(6, 13, 31, 0.55);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.6s ease;
  }

  .mdg-hero.audio-immersive::before {
    opacity: 1;
  }

  .mdg-hero::after {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 20% 20%, rgba(255,255,255,0.25), transparent 45%),
                radial-gradient(circle at 80% 10%, rgba(91, 122, 255, 0.35), transparent 55%);
    opacity: 0.55;
    pointer-events: none;
  }

  .mdg-hero > * { position: relative; z-index: 1; }


  .mdg-hero-controls {
    position: absolute;
    top: 24px;
    right: 24px;
    display: flex;
    gap: 10px;
  }

  .mdg-legacy-badge {
    position: absolute;
    top: 24px;
    left: 24px;
    padding: 10px 18px;
    border-radius: 999px;
    background: linear-gradient(135deg, #f9d976, #f39f36);
    color: #4f2c00;
    font-size: 0.8rem;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 18px 36px rgba(121, 79, 0, 0.25);
  }

  .mdg-hero-top {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 18px;
  }

  .mdg-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,0.3);
    background: rgba(255,255,255,0.16);
    font-size: 0.78rem;
    letter-spacing: 0.24em;
    text-transform: uppercase;
  }

  .mdg-badge.gold {
    background: linear-gradient(120deg, #ffe27c, #f3ad24);
    color: #4f2f00;
    border-color: rgba(255,255,255,0.45);
  }

  .mdg-hero-main {
    position: relative;
    z-index: 1;
    display: grid;
    gap: 18px;
    align-content: start;
  }

  .mdg-hero-immersive {
    position: absolute;
    inset: 0;
    z-index: 0;
    opacity: 0;
    transition: opacity 0.6s ease;
  }

  .mdg-hero-immersive video {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .mdg-hero.audio-immersive .mdg-hero-immersive {
    opacity: 0.9;
  }

  .mdg-hero h1 {
    font-size: clamp(2.4rem, 6vw, 3.6rem);
    font-weight: 600;
    margin: 0;
    font-family: 'Lora', 'Times New Roman', serif;
  }

  .mdg-hero-main p {
    margin: 0;
    color: rgba(235,237,255,0.85);
    font-size: 1.08rem;
    max-width: 32rem;
  }

  .mdg-hero-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }

  .mdg-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 16px;
    border-radius: 999px;
    border: 1px solid rgba(255,255,255,0.2);
    background: rgba(255,255,255,0.12);
    font-size: 0.78rem;
    letter-spacing: 0.18em;
  }

  .mdg-hero-media {
    position: relative;
    z-index: 1;
    border-radius: 30px;
    overflow: hidden;
    background: rgba(8, 16, 42, 0.4);
    box-shadow: 0 32px 60px rgba(6, 20, 68, 0.28);
    display: flex;
    align-items: flex-end;
    justify-content: center;
  }

  .mdg-hero-media img,
  .mdg-hero-media video {
    width: 100%;
    height: 100%;
    object-fit: cover;
  }

  .mdg-focus-toggle,
  .mdg-theme-toggle {
    border: none;
    background: rgba(5,10,28,0.35);
    color: #fff;
    padding: 10px 16px;
    border-radius: 14px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: transform 0.2s ease, background 0.3s ease;
  }

  .mdg-focus-toggle:hover,
  .mdg-theme-toggle:hover { transform: translateY(-2px); background: rgba(5,10,28,0.55); }

  .mdg-pinned {
    margin-top: 22px;
    border-radius: 20px;
    border: 1px solid rgba(255,255,255,0.18);
    background: rgba(6,12,32,0.34);
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    gap: 16px;
    align-items: center;
  }

  .mdg-pinned h6 {
    letter-spacing: 0.22em;
    font-size: 0.7rem;
    color: rgba(241,244,255,0.75);
    margin: 0 0 6px;
    text-transform: uppercase;
  }

  .mdg-wow {
    position: absolute;
    bottom: 28px;
    left: clamp(2.5rem, 6vw, 4.5rem);
    display: inline-flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    border-radius: 999px;
    background: rgba(255,255,255,0.2);
    color: #0f1533;
    font-weight: 600;
    animation: wowFloat 10s ease-in-out infinite;
  }

  .mdg-wow span {
    display: inline-block;
    animation: wowFade 6s ease-in-out infinite;
  }

  @keyframes wowFade {
    0%,100% { opacity: 0.4; transform: translateY(4px); }
    25%,75% { opacity: 1; transform: translateY(0); }
    50% { opacity: 1; transform: translateY(-2px); }
  }

  @keyframes wowFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-5px); }
  }

  .mdg-stats-row {
    margin-top: 32px;
    display: grid;
    gap: 16px;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  }

  .mdg-nav {
    position: sticky;
    top: 0;
    z-index: 30;
    margin-top: 32px;
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    background: rgba(255,255,255,0.92);
    backdrop-filter: blur(12px);
    padding: 12px 20px;
    border-radius: 20px;
    box-shadow: 0 18px 36px rgba(16,24,64,0.12);
  }

  .mdg-nav button {
    border: none;
    background: rgba(81,100,255,0.12);
    color: var(--mdg-hero);
    padding: 10px 18px;
    border-radius: 999px;
    font-size: 0.82rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    transition: background 0.3s ease, color 0.3s ease, transform 0.2s ease;
  }

  .mdg-nav button:hover,
  .mdg-nav button:focus-visible {
    background: linear-gradient(135deg, #5164ff, #6fe0ff);
    color: #fff;
    transform: translateY(-2px);
    outline: none;
  }

  body.mdg-dark .mdg-nav {
    background: rgba(9,14,32,0.94);
    box-shadow: 0 18px 36px rgba(0,0,0,0.32);
  }

  body.mdg-dark .mdg-nav button {
    background: rgba(81,100,255,0.18);
    color: #e5ecff;
  }

  body.mdg-dark .mdg-nav button:hover,
  body.mdg-dark .mdg-nav button:focus-visible {
    background: linear-gradient(135deg, #5d6bff, #7ff3ff);
  }

  .mdg-stat {
    border-radius: 22px;
    background: rgba(255,255,255,0.9);
    border: 1px solid rgba(148,163,184,0.18);
    padding: 20px;
    box-shadow: 0 30px 60px rgba(15, 28, 75, 0.12);
    text-align: center;
  }

  .mdg-stat strong { display: block; font-size: 1.8rem; color: var(--mdg-accent); }

  .mdg-section {
    margin-top: 56px;
  }

  .mdg-section-header {
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .mdg-section-header h2 {
    font-size: 1.8rem;
    margin: 0;
    font-weight: 600;
    color: var(--mdg-hero);
  }

  .mdg-section-header p {
    margin: 0;
    color: rgba(22, 35, 71, 0.68);
  }

  .mdg-grid {
    display: grid;
    gap: 26px;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  }

  .mdg-card {
    border-radius: 26px;
    background: rgba(255,255,255,0.92);
    border: 1px solid rgba(148,163,184,0.16);
    box-shadow: 0 24px 50px rgba(15, 28, 75, 0.12);
    padding: 24px 26px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    transition: transform 0.32s ease, box-shadow 0.35s ease, border-color 0.32s ease;
  }

  body.mdg-focus-mode .mdg-card {
    background: rgba(9,14,30,0.86);
    border-color: rgba(255,255,255,0.06);
    color: #eef2ff;
  }

  .mdg-card:hover,
  .mdg-card:focus-within {
    transform: translateY(-4px);
    box-shadow: 0 36px 70px rgba(12, 25, 84, 0.18);
    border-color: rgba(81,100,255,0.28);
  }

  .mdg-card h3 { font-size: 1.14rem; font-weight: 600; margin: 0; display: flex; gap: 10px; align-items: center; }

  .mdg-anchor-card {
    gap: 20px;
  }

  .mdg-exclusive-callout {
    background: linear-gradient(135deg, rgba(81,100,255,0.14), rgba(111,224,255,0.18));
    border: 1px solid rgba(81,100,255,0.24);
  }

  .mdg-anchor-preview {
    border-radius: 18px;
    background: rgba(81,100,255,0.12);
    padding: 16px 18px;
  }

  body.mdg-focus-mode .mdg-anchor-preview {
    background: rgba(81,100,255,0.2);
  }

  .mdg-anchor-preview p {
    font-size: 1rem;
  }

  .mdg-exclusive {
    align-self: flex-start;
    background: linear-gradient(130deg, rgba(81,100,255,0.85), rgba(118,136,255,0.65));
    color: #fff;
    padding: 6px 12px;
    border-radius: 999px;
    letter-spacing: 0.22em;
    font-size: 0.66rem;
  }

  .mdg-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    border-radius: 999px;
    background: rgba(99, 102, 255, 0.12);
    color: var(--mdg-accent);
    font-size: 0.74rem;
    letter-spacing: 0.16em;
    text-transform: uppercase;
  }

  .mdg-audio {
    border-radius: 18px;
    background: rgba(15,23,42,0.05);
    padding: 16px;
    display: grid;
    gap: 14px;
  }

  .mdg-audio audio { width: 100%; border-radius: 12px; }

  .mdg-visualizer {
    display: none;
    gap: 8px;
    align-items: flex-end;
    height: 34px;
  }

  .mdg-visualizer span {
    flex: 1;
    background: linear-gradient(180deg, rgba(81,100,255,0.7), rgba(81,100,255,0.18));
    border-radius: 8px;
    animation: bars 1.1s ease-in-out infinite;
  }

  .mdg-audio.playing .mdg-visualizer { display: flex; }

  @keyframes bars { 0%,100%{height:25%;} 50%{height:90%;} }

  .mdg-testimony {
    display: grid;
    gap: 18px;
    grid-template-columns: auto 1fr;
    align-items: center;
  }

  .mdg-testimony img {
    width: 100px;
    height: 100px;
    border-radius: 26px;
    object-fit: cover;
    border: 4px solid rgba(81,100,255,0.2);
  }

  .mdg-verse-card {
    border-radius: 22px;
    background: linear-gradient(135deg, rgba(81,100,255,0.22), rgba(152,180,255,0.35));
    padding: 20px;
    display: grid;
    gap: 12px;
  }

  .mdg-verse-actions,
  .mdg-footer-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
  }

  .mdg-progress {
    border-radius: 18px;
    background: rgba(81,100,255,0.08);
    padding: 18px;
    display: grid;
    gap: 16px;
  }

  .mdg-progress-bar {
    height: 10px;
    border-radius: 999px;
    background: rgba(148,163,184,0.22);
    overflow: hidden;
  }

  .mdg-progress-fill {
    height: 100%;
    width: 0%;
    background: linear-gradient(90deg, #5164ff, #74e2f8);
    transition: width 0.3s ease;
  }

  .mdg-progress-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    border-radius: 14px;
    background: rgba(81,100,255,0.12);
    transition: transform 0.2s ease, background 0.3s ease;
  }

  .mdg-progress-item.completed {
    background: rgba(34,197,94,0.18);
    transform: translateX(4px);
  }

  body.mdg-focus-mode .mdg-card h3,
  body.mdg-focus-mode .mdg-card p,
  body.mdg-focus-mode .mdg-card li,
  body.mdg-focus-mode .mdg-card label,
  body.mdg-focus-mode .mdg-card small {
    font-size: calc(1em * var(--mdg-focus-text-scale));
    line-height: 1.7;
  }

  body.mdg-focus-mode .mdg-testimony,
  body.mdg-focus-mode .mdg-meditations {
    max-width: 720px;
    margin-inline: auto;
  }

  body.mdg-focus-mode .mdg-testimony img {
    order: -1;
    margin-inline: auto;
  }

  .mdg-meditations { display: grid; gap: 12px; }

  .mdg-meditations .tile {
    border-radius: 16px;
    padding: 14px 16px;
    background: rgba(243,245,252,0.9);
    border: 1px solid rgba(148,163,184,0.2);
    position: relative;
  }

  .mdg-meditations .tile button {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(81,100,255,0.18);
    border: none;
    border-radius: 10px;
    padding: 6px 10px;
    font-size: 0.68rem;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--mdg-accent);
    cursor: pointer;
  }

  .mdg-artwork img {
    width: 100%;
    border-radius: 20px;
    box-shadow: 0 20px 32px rgba(19, 25, 54, 0.22);
  }

  .mdg-form textarea,
  .mdg-form input,
  .mdg-form select {
    width: 100%;
    border-radius: 14px;
    border: 1px solid rgba(148,163,184,0.24);
    padding: 12px 14px;
  }

  body.mdg-focus-mode .mdg-form textarea,
  body.mdg-focus-mode .mdg-form input,
  body.mdg-focus-mode .mdg-form select {
    background: rgba(7,10,25,0.85);
    border-color: rgba(217,226,255,0.12);
    color: inherit;
  }

  body.mdg-dark .mdg-card {
    background: var(--mdg-dark-surface);
    border-color: rgba(148,163,184,0.12);
    color: #e6edff;
  }

  body.mdg-dark .mdg-card:hover,
  body.mdg-dark .mdg-card:focus-within {
    border-color: rgba(123,136,255,0.4);
    box-shadow: 0 32px 64px rgba(0,0,0,0.35);
  }

  body.mdg-dark .mdg-section-header h2 { color: #e8edff; }
  body.mdg-dark .mdg-section-header p { color: rgba(223,233,255,0.65); }
  body.mdg-dark .mdg-anchor-preview { background: rgba(81,100,255,0.24); }
  body.mdg-dark .mdg-badge { background: rgba(255,255,255,0.16); color: #fff; }
  body.mdg-dark .mdg-stat { background: rgba(11,15,28,0.9); border-color: rgba(148,163,184,0.16); color: #e9edff; }
  body.mdg-dark .mdg-hero { box-shadow: 0 40px 90px rgba(0,0,0,0.35); }
  body.mdg-dark .mdg-verse-card { background: linear-gradient(135deg, rgba(104,120,255,0.42), rgba(73,101,255,0.28)); }
  body.mdg-dark .mdg-verse-card small { color: rgba(224,231,255,0.8); }
  body.mdg-dark .mdg-meditations .tile { background: rgba(32,42,82,0.62); border-color: rgba(118,135,255,0.24); color: #e2e8ff; }

  .mdg-modal {
    position: fixed;
    inset: 0;
    background: rgba(5,10,24,0.68);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 24px;
    z-index: 40;
  }

  .mdg-modal.active { display: flex; }

  .mdg-modal-content {
    background: var(--mdg-surface);
    padding: 30px;
    border-radius: 22px;
    width: min(520px, 100%);
    box-shadow: var(--mdg-shadow);
  }

  body.mdg-focus-mode .mdg-modal-content {
    background: rgba(9,13,28,0.92);
    color: #f1f5ff;
  }

  .mdg-floating-toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    border-radius: 16px;
    background: linear-gradient(135deg, #5164ff, #73dcfa);
    color: #fff;
    padding: 14px 18px;
    display: inline-flex;
    gap: 12px;
    align-items: center;
    box-shadow: 0 22px 44px rgba(15,27,63,0.24);
    animation: wowFloat 12s ease-in-out infinite;
    z-index: 20;
  }

  .mdg-floating-toast button {
    border: none;
    background: rgba(255,255,255,0.25);
    color: #fff;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    display: grid;
    place-items: center;
  }

  .mdg-journal-trigger {
    position: fixed;
    right: 24px;
    bottom: 90px;
    width: 54px;
    height: 54px;
    border-radius: 50%;
    border: none;
    background: linear-gradient(135deg, #5164ff, #73dcfa);
    color: #fff;
    box-shadow: 0 16px 28px rgba(81,100,255,0.4);
    display: grid;
    place-items: center;
    z-index: 50;
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.3s ease;
  }

  .mdg-journal-trigger:hover {
    transform: translateY(-3px);
    box-shadow: 0 22px 36px rgba(81,100,255,0.45);
  }

  .mdg-journal-panel {
    position: fixed;
    top: 0;
    right: 0;
    width: min(380px, 100%);
    height: 100vh;
    background: var(--mdg-surface);
    border-left: 1px solid var(--mdg-border);
    box-shadow: -16px 0 32px rgba(11,20,55,0.16);
    padding: 28px;
    display: flex;
    flex-direction: column;
    gap: 18px;
    transition: transform 0.5s ease;
    z-index: 90;
    transform: translateX(100%);
  }

  body.mdg-focus-mode .mdg-journal-panel {
    background: rgba(9,14,30,0.93);
    color: #eef2ff;
  }

  body.mdg-journal-open .mdg-journal-panel {
    transform: translateX(0);
  }

  body.mdg-journal-open .mdg-journal-overlay {
    opacity: 1;
    pointer-events: auto;
  }

  .mdg-journal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(5,10,24,0.55);
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.35s ease;
    z-index: 80;
  }

  .mdg-journal-panel textarea {
    flex: 1;
    resize: none;
  }

  .mdg-journal-history {
    border-radius: 16px;
    background: rgba(81,100,255,0.08);
    padding: 16px;
    max-height: 180px;
    overflow-y: auto;
  }

  .mdg-footer-cta {
    margin-top: 36px;
    text-align: center;
  }

  @media (max-width: 991.98px) {
    .mdg-shell { padding: 48px 0 90px; }
    .mdg-hero {
      grid-template-columns: 1fr;
      text-align: center;
    }
    .mdg-legacy-badge {
      position: static;
      margin-bottom: 16px;
      justify-content: center;
    }
    .mdg-hero-controls {
      position: static;
      justify-content: center;
      margin-bottom: 18px;
    }
    .mdg-hero-media {
      justify-content: center;
      margin-inline: auto;
      max-width: 320px;
    }
    .mdg-hero-main { text-align: center; justify-items: center; }
    .mdg-testimony { grid-template-columns: 1fr; text-align: center; }
    .mdg-floating-toast { left: 18px; right: 18px; justify-content: space-between; }
    .mdg-journal-trigger { right: 18px; bottom: 84px; }
  }

  @media (max-width: 575.98px) {
    .mdg-pinned { flex-direction: column; align-items: flex-start; gap: 12px; }
  }

  @media (prefers-reduced-motion: reduce) {
    * {
      animation-duration: 0.01ms !important;
      animation-iteration-count: 1 !important;
      transition-duration: 0.01ms !important;
      scroll-behavior: auto !important;
    }
  }

  @media (prefers-color-scheme: dark) {
    :root { color-scheme: dark; }
    body:not(.mdg-light) {
      background: var(--mdg-dark-bg);
      color: #dbe6ff;
    }
  }
</style>

<div class="mdg-journal-overlay" id="mdgJournalOverlay" aria-hidden="true"></div>
 @if($gift)
      <nav class="mdg-nav d-flex justify-content-center" aria-label="Monthly gift navigation">
  <button type="button" data-scroll="#mdgContentSection">Content</button>
  <button type="button" data-scroll="#mdgReflectionSection">Reflect</button>
  <button type="button" data-scroll="#mdgResourcesSection">Resources</button>
  <button type="button" data-scroll="#mdgRecognitionSection">Impact</button>
</nav>
<aside class="mdg-journal-panel" id="mdgJournalPanel" aria-hidden="true">
  <div class="d-flex justify-content-between align-items-start">
    <div>
      <h5 class="mb-1">Centurion Prayer Journal</h5>
      <small class="text-muted">Capture what God is speaking as you journey through this gift.</small>
    </div>
    <button type="button" class="btn btn-sm btn-outline-secondary" id="mdgJournalClose">
      <i class="material-icons-outlined">close</i>
    </button>
  </div>
  <label for="mdgJournalEntry" class="fw-semibold">Today's Reflection</label>
  <textarea id="mdgJournalEntry" class="form-control" rows="6" placeholder="Write your prayer, insight, or gratitude..."></textarea>
  <div class="d-flex justify-content-between align-items-center">
    <small class="text-muted">Entries save securely to your device.</small>
    <button type="button" class="btn btn-primary btn-sm" id="mdgJournalSave">Save Entry</button>
  </div>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-2">
      <span class="fw-semibold">Recent Notes</span>
      <button type="button" class="btn btn-link btn-sm p-0" id="mdgJournalClear">Clear All</button>
    </div>
    <div class="mdg-journal-history" id="mdgJournalHistory" role="log" aria-live="polite"></div>
  </div>
  <hr>
  <form method="POST" action="#" class="mdg-form">
    @csrf
    <label class="fw-semibold" for="mdgJournalMessage">Send a quick encouragement to Bishop Gregory Toussaint</label>
    <textarea id="mdgJournalMessage" name="mdgJournalMessage" rows="3" maxlength="360" class="form-control mb-2" placeholder="Share a 360 character message..."></textarea>
    <div class="d-flex justify-content-between align-items-center">
      <small class="text-muted">360 characters max</small>
      <button type="submit" class="btn btn-outline-primary btn-sm">Send Message</button>
    </div>
  </form>
</aside>
<section class="mdg-shell">
  <div class="container">
    <div class="mdg-hero" id="mdgHero">
      <!--<span class="mdg-legacy-badge"><i class="material-icons-outlined" aria-hidden="true">military_tech</i> Your Centurion Legacy</span>-->
      <div class="mdg-hero-controls" role="group" aria-label="Display controls">
        <!--<button class="mdg-theme-toggle" type="button" id="mdgThemeToggle">
          <i class="material-icons-outlined" aria-hidden="true">dark_mode</i><span>Dark</span>
        </button>
        <button class="mdg-focus-toggle" type="button" id="mdgFocusToggle">
          <i class="material-icons-outlined">glasses</i><span>Focus Mode</span>
        </button>-->
      </div>
      <div class="mdg-hero-main">
        <div class="mdg-hero-top">
          <!--<span class="mdg-badge">Monthly Digital Gift</span>
          <span class="mdg-badge gold"><i class="material-icons-outlined">military_tech</i> Centurion Legacy Builder</span>-->
        </div>
        <img src="{{ asset('assets/faces/Shekinah Logo White.png') }}" style="width:20%;" alt="Monthly greeting from the pastor">
        <div>
          <h1>{{ $monthLabel ?: 'Coming Soon' }}</h1><br>
          <p>{{ $welcome }}</p>
        </div>
      </div>
      <div class="mdg-hero-media mdg-hero-portrait">
        <img src="{{ asset('assets/faces/157A6207.png') }}" alt="Monthly greeting from the pastor">
      </div>
      <div class="mdg-wow">
        <i class="material-icons-outlined">auto_awesome</i>
        <span id="mdgWowMessage">{{ $wowMessage }}</span>
      </div>
    </div>


      <section class="mdg-section" id="mdgContentSection">
        <div class="mdg-section-header">
          <h2>This Month’s Ministry Content</h2>
          <p>Stream, read, and meditate on the curated resources prepared for you.</p>
        </div>
        <div class="mdg-grid">
          <article class="mdg-card mdg-exclusive-callout">
            <span class="mdg-exclusive">Centurion Exclusive</span>
            <h3>Pastor’s Bonus Greeting</h3>
            <p>{{ $gift->bonus_message ?? 'Enjoy this exclusive message prepared just for Centurion partners. Press play on the hero video if you have not already and let the monthly encouragement wash over you.' }}</p>
            <button class="btn btn-outline-primary d-inline-flex align-items-center gap-2" type="button" data-scroll="#mdgHero">
              <i class="material-icons-outlined" aria-hidden="true">play_circle</i>
              <span>Watch Greeting</span>
            </button>
          </article>

          <article class="mdg-card">
            <span class="mdg-exclusive">Centurion Exclusive</span>
            <h3>Sermon Audio</h3>
            @if(! empty($gift->sermon_description))
              <p>{{ $gift->sermon_description }}</p>
            @endif
            <div class="mdg-audio" id="mdgSermonWrapper">
              <div class="d-flex flex-wrap align-items-center gap-3">
                <span class="mdg-chip">{{ $gift->sermon_title }}</span>
                @if($gift->sermon_date)
                  <small class="text-muted">Recorded {{ optional($gift->sermon_date)->format('F j, Y') }}</small>
                @endif
              </div>
              <audio controls preload="none" id="mdgSermonAudio">
                <source src="{{ $gift->sermon_audio_url }}" type="audio/mpeg">
                Your browser does not support the audio element.
              </audio>
            </div>
          </article>

          <article class="mdg-card">
            <span class="mdg-exclusive">Centurion Exclusive</span>
            <h3>Worship Audio</h3>
            <p>Led by {{ $gift->worship_leader ?? 'our worship team' }}</p>
            <div class="mdg-audio" id="mdgWorshipWrapper">
              <div class="d-flex flex-wrap align-items-center gap-3">
                <span class="mdg-chip">{{ $gift->worship_title }}</span>
                @if(! empty($gift->worship_theme_note))
                  <small class="text-muted">{{ $gift->worship_theme_note }}</small>
                @endif
              </div>
              <div class="mdg-visualizer" id="mdgVisualizer">
                <span style="animation-delay:0s"></span>
                <span style="animation-delay:0.12s"></span>
                <span style="animation-delay:0.24s"></span>
                <span style="animation-delay:0.36s"></span>
                <span style="animation-delay:0.48s"></span>
              </div>
              <audio controls preload="none" id="mdgWorshipAudio">
                <source src="{{ $gift->worship_audio_url }}" type="audio/mpeg">
                Your browser does not support the audio element.
              </audio>
            </div>
          </article>

          <article class="mdg-card">
            <h3>Verse of the Month</h3>
            <div class="mdg-verse-card">
              <div class="d-flex justify-content-between align-items-center">
                <span class="mdg-chip" style="background:rgba(255,255,255,0.6);color:#1b2759;">{{ $gift->verse_reference }}</span>
                <button class="btn btn-sm btn-outline-light text-uppercase" type="button" data-pin-text="{{ $gift->verse_text }}" data-pin-source="Verse" style="letter-spacing:0.14em;">Pin Verse</button>
              </div>
              <p class="fw-semibold mb-0" id="mdgVerseText">“{{ $gift->verse_text }}”</p>
              <small class="text-muted">{{ $gift->verse_reflection ?? 'Take a moment to sit with this verse.' }}</small>
            </div>
            <div class="mdg-verse-actions">
              <button class="btn btn-outline-primary" type="button" id="mdgFlipVerse">Flip Card</button>
              <button class="btn btn-primary" type="button" data-copy-target="#mdgVerseText">Copy Verse</button>
              <button class="btn btn-outline-secondary" type="button" data-share-target="#mdgVerseText">Share Verse</button>
              <button class="btn btn-outline-info" type="button" id="mdgScrollMeditations">Short Meditations</button>
            </div>
          </article>

          <article class="mdg-card">
            <h3>Short Meditations</h3>
            @if($meditations->isNotEmpty())
              <div class="mdg-meditations" id="mdgMeditations">
                @foreach($meditations as $paragraph)
                  <div class="tile">
                    <button type="button" data-pin-text="{{ $paragraph }}" data-pin-source="Meditation">Pin</button>
                    {{ $paragraph }}
                  </div>
                @endforeach
              </div>
            @else
              <p>Let this month’s theme draw you into stillness and prayer.</p>
            @endif
          </article>
        </div>
      </section>

      <section class="mdg-section" id="mdgReflectionSection">
        <div class="mdg-section-header">
          <h2>Reflect &amp; Respond</h2>
          <p>Capture what God is speaking, celebrate testimonies, and track your journey.</p>
        </div>
        <div class="mdg-grid">
          <article class="mdg-card mdg-anchor-card" id="mdgAnchorCard">
            <h3>Anchored in His Promise</h3>
            <p>{{ $gift->anchored_intro ?? "This month, we are reminded that God's promises are not bound by time or circumstance. He is faithful to complete what He began in each of us." }}</p>
            <div class="mdg-anchor-preview">
              <small class="text-muted text-uppercase fw-semibold">Pinned Promise</small>
              <p class="fw-semibold mb-0" id="mdgAnchorPreviewText" data-default="Select a verse or meditation to anchor your heart this month.">Select a verse or meditation to anchor your heart this month.</p>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1" type="button" data-copy-target="#mdgAnchorPreviewText">
                <i class="material-icons-outlined" style="font-size:18px;">content_copy</i> Copy
              </button>
              <button class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center gap-1" type="button" data-share-target="#mdgAnchorPreviewText">
                <i class="material-icons-outlined" style="font-size:18px;">ios_share</i> Share
              </button>
            </div>
          </article>

          <article class="mdg-card">
            <h3>Progress Tracker</h3>
            <div class="mdg-progress">
              <div class="mdg-progress-bar">
                <div class="mdg-progress-fill" id="mdgProgressFill"></div>
              </div>
              <label class="mdg-progress-item" data-progress="sermon">
                <input type="checkbox" id="progressSermon"> Sermon Heard
              </label>
              <label class="mdg-progress-item" data-progress="verse">
                <input type="checkbox" id="progressVerse"> Verse Saved
              </label>
              <label class="mdg-progress-item" data-progress="meditation">
                <input type="checkbox" id="progressMeditation"> Meditation Read
              </label>
            </div>
          </article>

          <article class="mdg-card">
            <h3>Written Testimony</h3>
            <div class="mdg-testimony">
              @if(! empty($gift->testimony_image_path))
                <img src="{{ $gift->testimony_image_path }}" alt="Testimony">
              @endif
              <div>
                @if(! empty($gift->testimony_type))
                  <span class="mdg-chip" style="background:rgba(34,197,94,0.18);color:#047857;">{{ strtoupper($gift->testimony_type) }}</span>
                @endif
                @foreach($testimonyParagraphs as $paragraph)
                  <p class="mb-2">{{ $paragraph }}</p>
                @endforeach
              </div>
            </div>
          </article>

          <article class="mdg-card mdg-form">
            <span class="mdg-exclusive">Centurion Exclusive</span>
            <h3><i class="material-icons-outlined text-warning">favorite</i> Note to Bishop Gregory</h3>
            <p>Bless Bishop Gregory Toussaint with a testimony, prayer, or personal encouragement.</p>
            <form method="POST" action="#">
              @csrf
              <textarea name="bishop_message" maxlength="600" placeholder="Type your encouragement…" rows="5"></textarea>
              <div class="d-flex justify-content-between align-items-center mt-2">
                <small class="text-muted">600 characters max</small>
                <button type="submit" class="btn btn-primary">Send Message</button>
              </div>
            </form>
          </article>

          <article class="mdg-card mdg-feedback mdg-form">
            <h3>We’d Love Your Feedback</h3>
            <form method="POST" action="#">
              @csrf
              <label class="fw-semibold">Rate the Message</label>
              <select name="rating" class="mb-3">
                <option value="" selected disabled>Choose 1 – 5</option>
                @for($i=5; $i>=1; $i--)
                  <option value="{{ $i }}">{{ $i }}</option>
                @endfor
              </select>
              <label class="fw-semibold">Most Helpful Resource</label>
              <input type="text" name="helpful_resource" placeholder="E.g., Worship Audio, Meditation" class="mb-3">
              <label class="fw-semibold">Suggestions</label>
              <textarea name="suggestions" placeholder="Share how we can serve you better" rows="4"></textarea>
              <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary">Submit Feedback</button>
              </div>
            </form>
          </article>

          <article class="mdg-card">
            <h3>Message the Pastor</h3>
            <p>Send a private note to Pastor or the executive team. We prayerfully read each message.</p>
            <button class="btn btn-outline-primary" type="button" id="mdgMessagePastor">Message the Pastor</button>
          </article>
        </div>
      </section>

      <section class="mdg-section" id="mdgResourcesSection">
        <div class="mdg-section-header">
          <h2>Resources &amp; Downloads</h2>
          <p>Carry this month’s encouragement wherever you go.</p>
        </div>
        <div class="mdg-grid">
          <article class="mdg-card">
            <h3>Dynamic Spiritual Artwork</h3>
            <div class="mdg-artwork">
              <img src="{{ $dynamicArtwork }}" alt="Spiritual artwork">
            </div>
            @if(! empty($gift->artwork_caption))
              <p class="mt-3 small text-muted">{{ $gift->artwork_caption }}</p>
            @endif
          </article>

          <article class="mdg-card">
            <h3>Closing Blessing</h3>
            <p>{{ $gift->closing_blessing ?? 'May the peace of Christ guard your heart and mind this month.' }}</p>
            <div class="mdg-footer-actions">
              <button class="btn btn-outline-primary" type="button" data-copy-target="#mdgVerseText">Copy Verse</button>
              <button class="btn btn-outline-secondary" type="button" data-share-target="#mdgVerseText">Share Verse</button>
              @if(! empty($gift->wallpaper_phone_url))
                <a href="{{ $gift->wallpaper_phone_url }}" download class="btn btn-primary">📱 Phone</a>
              @endif
              @if(! empty($gift->wallpaper_desktop_url))
                <a href="{{ $gift->wallpaper_desktop_url }}" download class="btn btn-outline-primary">💻 Desktop</a>
              @endif
            </div>
          </article>
        </div>
      </section>

      <section class="mdg-section" id="mdgRecognitionSection">
        <div class="mdg-section-header">
          <h2>Centurion Impact &amp; Recognition</h2>
          <p>Because of your faithfulness, lives continue to be transformed.</p>
        </div>
        <div class="mdg-stats-row" role="list">
          <div class="mdg-stat" role="listitem">
            <strong data-mdg-count="1250">1,250</strong>
            <small class="text-muted">Lives Changed</small>
            <p class="mb-0 small text-muted">Because of your faithfulness, people are saying yes to Jesus.</p>
          </div>
          <div class="mdg-stat" role="listitem">
            <strong data-mdg-count="45000">45,000+</strong>
            <small class="text-muted">Listeners Reached</small>
            <p class="mb-0 small text-muted">Your monthly seed keeps the Gospel on the airwaves worldwide.</p>
          </div>
          <div class="mdg-stat" role="listitem">
            <strong data-mdg-count="12">12</strong>
            <small class="text-muted">Nations Served</small>
            <p class="mb-0 small text-muted">Centurion partners are touching nations every month.</p>
          </div>
        </div>
      </section>

      <div class="mdg-footer-cta">
        <button class="btn btn-lg btn-outline-light d-inline-flex align-items-center gap-2" type="button" id="mdgPastorFooterCTA">
          <i class="material-icons-outlined">volunteer_activism</i>
          <span>Send a Special Message to the Pastor</span>
        </button>
      </div>
    @else
      <div class="mdg-card mt-4 text-center">
        <h3>Refreshing Soon</h3>
        <p>Our team is preparing this month’s digital gift. Check back shortly for new audio, verses, and devotionals.</p>
      </div>
    @endif
  </div>
</section>

<button class="mdg-journal-trigger" type="button" id="mdgJournalTrigger" aria-label="Open Centurion journal">
  <i class="material-icons-outlined">edit_note</i>
</button>

<div class="mdg-modal" id="mdgPastorModal" aria-hidden="true">
  <div class="mdg-modal-content">
    <div class="d-flex justify-content-between align-items-start mb-3">
      <h4 class="mb-0">Private Message to Pastor</h4>
      <button type="button" class="btn-close" aria-label="Close" id="mdgPastorModalClose"></button>
    </div>
    <form method="POST" action="#">
      @csrf
      <div class="mb-3">
        <label class="form-label">Subject</label>
        <input type="text" name="pastor_subject" class="form-control" placeholder="Subject line">
      </div>
      <div class="mb-3">
        <label class="form-label">Your Message</label>
        <textarea name="pastor_message" class="form-control" rows="6" placeholder="Share your heart…"></textarea>
      </div>
      <div class="d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-outline-secondary" id="mdgPastorModalCancel">Cancel</button>
        <button type="submit" class="btn btn-primary">Send Message</button>
      </div>
    </form>
  </div>
</div>

<div class="mdg-floating-toast" id="mdgFloatingToast">
  <i class="material-icons-outlined">celebration</i>
  <span>Centurions, thank you for changing lives this month!</span>
  <button type="button" id="mdgFloatingToastClose"><i class="material-icons-outlined">close</i></button>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js" integrity="sha512-l9Q7xvFeoJEB6Digw1VE3DybMT0SqnX+0Gooy2cuglRez2oJ6TP2PzefDs2fzGEylh4G6dkprdFMn/hTyBC0RA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
  (function () {
    const body = document.body;
    const focusToggle = document.getElementById('mdgFocusToggle');
    const themeToggle = document.getElementById('mdgThemeToggle');
    const focusToggleLabel = focusToggle?.querySelector('span');
    const themeLabel = themeToggle?.querySelector('span');
    const themeIcon = themeToggle?.querySelector('i');
    const hero = document.getElementById('mdgHero');
    const ambientVideo = document.getElementById('mdgAmbientVideo');
    const themeKey = 'mdgTheme';
    const prefersDark = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : { matches: false };
    let manualTheme = false;

    function applyTheme(mode, persist = true) {
      if (mode === 'dark') {
        body.classList.add('mdg-dark');
        body.classList.remove('mdg-light');
        themeToggle?.setAttribute('aria-pressed', 'true');
        if (themeLabel) themeLabel.textContent = 'Light';
        if (themeIcon) themeIcon.textContent = 'light_mode';
      } else {
        body.classList.remove('mdg-dark');
        body.classList.add('mdg-light');
        themeToggle?.setAttribute('aria-pressed', 'false');
        if (themeLabel) themeLabel.textContent = 'Dark';
        if (themeIcon) themeIcon.textContent = 'dark_mode';
      }
      if (persist) {
        manualTheme = true;
        localStorage.setItem(themeKey, mode);
      }
    }

    const storedTheme = localStorage.getItem(themeKey);
    if (storedTheme) {
      applyTheme(storedTheme);
    } else if (prefersDark.matches) {
      applyTheme('dark', false);
    } else {
      applyTheme('light', false);
    }

    themeToggle?.addEventListener('click', () => {
      const isDark = body.classList.contains('mdg-dark');
      applyTheme(isDark ? 'light' : 'dark');
    });
    if (themeToggle) themeToggle.setAttribute('aria-pressed', String(body.classList.contains('mdg-dark')));

    const handlePrefersChange = event => {
      if (!manualTheme) applyTheme(event.matches ? 'dark' : 'light', false);
    };
    if (prefersDark.addEventListener) {
      prefersDark.addEventListener('change', handlePrefersChange);
    } else if (prefersDark.addListener) {
      prefersDark.addListener(handlePrefersChange);
    }

    focusToggle?.addEventListener('click', () => {
      const isFocus = body.classList.toggle('mdg-focus-mode');
      focusToggle?.setAttribute('aria-pressed', String(isFocus));
      if (focusToggleLabel) focusToggleLabel.textContent = isFocus ? 'Exit Reading Mode' : 'Focus Mode';
    });
    if (focusToggle) focusToggle.setAttribute('aria-pressed', String(body.classList.contains('mdg-focus-mode')));

    function copyText(text) {
      if (!text) return;
      navigator.clipboard.writeText(text).then(() => alert('Copied to clipboard!'))
        .catch(() => alert('Unable to copy on this device.'));
    }

    function shareText(text) {
      if (!text) return;
      if (navigator.share) {
        navigator.share({ title: 'Monthly Digital Gift', text, url: window.location.href })
          .catch(err => {
            if (!err || err.name !== 'AbortError') alert('Sharing failed. You can still copy the verse manually.');
          });
      } else {
        alert('Sharing is not supported on this device. Try copying the verse instead.');
      }
    }

    document.querySelectorAll('[data-copy-target]').forEach(btn => {
      btn.addEventListener('click', () => {
        const target = document.querySelector(btn.dataset.copyTarget);
        copyText(target ? target.textContent.trim() : '');
      });
    });

    document.querySelectorAll('[data-share-target]').forEach(btn => {
      btn.addEventListener('click', () => {
        const target = document.querySelector(btn.dataset.shareTarget);
        shareText(target ? target.textContent.trim() : '');
      });
    });

    document.querySelectorAll('[data-scroll]').forEach(btn => {
      btn.addEventListener('click', () => {
        const target = document.querySelector(btn.dataset.scroll);
        target?.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    });

    if (window.gsap && 'IntersectionObserver' in window) {
      const reveal = (entry) => {
        gsap.fromTo(entry.target, { opacity: 0, y: 48 }, { opacity: 1, y: 0, duration: 0.8, ease: 'power3.out' });
      };
      const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            reveal(entry);
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.2 });

      document.querySelectorAll('.mdg-card').forEach(card => observer.observe(card));
      document.querySelectorAll('.mdg-stat').forEach(stat => observer.observe(stat));
    }

    const journalTrigger = document.getElementById('mdgJournalTrigger');
    const journalPanel = document.getElementById('mdgJournalPanel');
    const journalOverlay = document.getElementById('mdgJournalOverlay');
    const journalClose = document.getElementById('mdgJournalClose');
    const journalEntry = document.getElementById('mdgJournalEntry');
    const journalSave = document.getElementById('mdgJournalSave');
    const journalClear = document.getElementById('mdgJournalClear');
    const journalHistory = document.getElementById('mdgJournalHistory');

    const journalKey = 'mdgJournalEntries';
    let journalEntries = [];

    journalPanel?.setAttribute('aria-hidden', 'true');
    journalOverlay?.setAttribute('aria-hidden', 'true');

    const escapeHTML = (value = '') => value.replace(/[&<>"']/g, char => (
      { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[char] || char
    ));

    const renderJournalEntries = () => {
      if (!journalHistory) return;
      if (!journalEntries.length) {
        journalHistory.innerHTML = '<p class="text-muted mb-0">Your saved reflections will live here this month.</p>';
        return;
      }
      journalHistory.innerHTML = journalEntries.map(entry => {
        const date = new Date(entry.date).toLocaleString(undefined, { month: 'short', day: 'numeric', hour: 'numeric', minute: '2-digit' });
        return `<div class="mb-2"><small class="text-muted d-block">${date}</small><p class="mb-0">${escapeHTML(entry.text)}</p></div>`;
      }).join('');
    };

    try {
      journalEntries = JSON.parse(localStorage.getItem(journalKey) || '[]');
    } catch (_) {
      journalEntries = [];
    }
    renderJournalEntries();

    if (journalTrigger) journalTrigger.setAttribute('aria-expanded', 'false');

    const openJournal = () => {
      body.classList.add('mdg-journal-open');
      journalPanel?.setAttribute('aria-hidden', 'false');
      journalOverlay?.setAttribute('aria-hidden', 'false');
      journalTrigger?.setAttribute('aria-expanded', 'true');
      setTimeout(() => journalEntry?.focus({ preventScroll: false }), 80);
    };

    const closeJournal = () => {
      body.classList.remove('mdg-journal-open');
      journalPanel?.setAttribute('aria-hidden', 'true');
      journalOverlay?.setAttribute('aria-hidden', 'true');
      journalTrigger?.setAttribute('aria-expanded', 'false');
    };

    journalTrigger?.addEventListener('click', openJournal);
    journalOverlay?.addEventListener('click', closeJournal);
    journalClose?.addEventListener('click', closeJournal);
    document.addEventListener('keydown', evt => {
      if (evt.key === 'Escape' && body.classList.contains('mdg-journal-open')) closeJournal();
    });

    journalSave?.addEventListener('click', () => {
      const value = journalEntry?.value.trim();
      if (!value) {
        alert('Please write a quick note before saving.');
        return;
      }
      journalEntries.unshift({ text: value, date: new Date().toISOString() });
      journalEntries = journalEntries.slice(0, 12);
      localStorage.setItem(journalKey, JSON.stringify(journalEntries));
      if (journalEntry) journalEntry.value = '';
      renderJournalEntries();
      alert('Reflection saved to your device.');
    });

    journalClear?.addEventListener('click', () => {
      if (!journalEntries.length) return;
      if (!confirm('Clear all saved reflections for this month?')) return;
      journalEntries = [];
      localStorage.removeItem(journalKey);
      renderJournalEntries();
    });

    const pinnedBox = document.getElementById('mdgPinnedPromise');
    const pinnedText = pinnedBox?.querySelector('.mdg-pinned-text');
    const pinnedClear = document.getElementById('mdgPinnedClear');
    const anchorPreview = document.getElementById('mdgAnchorPreviewText');
    const anchorDefault = anchorPreview?.dataset.default || 'Select a verse or meditation to anchor your heart this month.';
    const pinnedDefault = pinnedText?.dataset.default || 'Select a verse or meditation to anchor your heart this month.';

    function setPinned(text, source) {
      if (!pinnedBox || !pinnedText) return;
      pinnedText.textContent = text;
      pinnedBox.dataset.empty = 'false';
      pinnedBox.classList.add('active');
      if (anchorPreview) anchorPreview.textContent = text;
      localStorage.setItem('mdgPinnedPromise', JSON.stringify({ text, source }));
    }

    function resetPinned() {
      if (!pinnedBox || !pinnedText) return;
      localStorage.removeItem('mdgPinnedPromise');
      pinnedText.textContent = pinnedDefault;
      if (anchorPreview) anchorPreview.textContent = anchorDefault;
      pinnedBox.dataset.empty = 'true';
      pinnedBox.classList.remove('active');
    }

    pinnedClear?.addEventListener('click', resetPinned);

    const storedPromise = localStorage.getItem('mdgPinnedPromise');
    if (storedPromise) {
      const parsed = JSON.parse(storedPromise);
      if (parsed?.text) setTimeout(() => setPinned(parsed.text, parsed.source), 0);
    }

    document.querySelectorAll('[data-pin-text]').forEach(btn => {
      btn.addEventListener('click', () => setPinned(btn.dataset.pinText, btn.dataset.pinSource));
    });

    document.getElementById('mdgAnchorScroll')?.addEventListener('click', () => {
      document.getElementById('mdgAnchorCard')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    document.getElementById('mdgHeroMeditations')?.addEventListener('click', () => {
      document.getElementById('mdgMeditations')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    const checks = ['progressSermon', 'progressVerse', 'progressMeditation'];
    const progressFill = document.getElementById('mdgProgressFill');

    function updateProgress() {
      let completed = 0;
      checks.forEach(id => {
        const checkbox = document.getElementById(id);
        const item = document.querySelector(`[data-progress="${id.replace('progress', '').toLowerCase()}"]`);
        if (checkbox?.checked) {
          completed++;
          item?.classList.add('completed');
        } else {
          item?.classList.remove('completed');
        }
      });
      if (progressFill) progressFill.style.width = `${(completed / checks.length) * 100}%`;
      localStorage.setItem('mdgProgress', JSON.stringify(checks.map(id => ({ id, value: document.getElementById(id)?.checked ?? false }))));
    }

    JSON.parse(localStorage.getItem('mdgProgress') || '[]').forEach(item => {
      const checkbox = document.getElementById(item.id);
      if (checkbox) checkbox.checked = item.value;
    });
    updateProgress();
    checks.forEach(id => document.getElementById(id)?.addEventListener('change', updateProgress));

    document.querySelectorAll('[data-mdg-count]').forEach(el => {
      const target = parseInt(el.dataset.mdgCount, 10);
      if (!Number.isFinite(target)) return;
      let current = 0;
      const step = Math.max(1, Math.round(target / 60));
      const timer = setInterval(() => {
        current += step;
        if (current >= target) {
          current = target;
          clearInterval(timer);
        }
        el.textContent = current.toLocaleString();
      }, 40);
    });

    const worshipAudio = document.getElementById('mdgWorshipAudio');
    const worshipWrapper = document.getElementById('mdgWorshipWrapper');

    const toggleImmersiveHero = (active) => {
      if (!hero) return;
      hero.classList.toggle('audio-immersive', active);
      if (!ambientVideo) return;
      if (active) {
        ambientVideo.play().catch(() => {});
      } else {
        ambientVideo.pause();
        ambientVideo.currentTime = 0;
      }
    };

    worshipAudio?.addEventListener('play', () => {
      worshipWrapper?.classList.add('playing');
      toggleImmersiveHero(true);
    });
    const stopImmersive = () => {
      worshipWrapper?.classList.remove('playing');
      toggleImmersiveHero(false);
    };
    worshipAudio?.addEventListener('pause', stopImmersive);
    worshipAudio?.addEventListener('ended', stopImmersive);

    const toast = document.getElementById('mdgFloatingToast');
    document.getElementById('mdgFloatingToastClose')?.addEventListener('click', () => toast?.remove());

    const modal = document.getElementById('mdgPastorModal');
    const openPastorModal = () => modal?.classList.add('active');
    document.getElementById('mdgMessagePastor')?.addEventListener('click', openPastorModal);
    document.getElementById('mdgPastorFooterCTA')?.addEventListener('click', openPastorModal);
    document.getElementById('mdgPastorModalClose')?.addEventListener('click', () => modal?.classList.remove('active'));
    document.getElementById('mdgPastorModalCancel')?.addEventListener('click', () => modal?.classList.remove('active'));

    document.getElementById('mdgFlipVerse')?.addEventListener('click', () => alert('Flip animation is simplified in this layout.'));
    document.getElementById('mdgScrollMeditations')?.addEventListener('click', () => {
      document.getElementById('mdgMeditations')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
  })();
</script>
@endsection
