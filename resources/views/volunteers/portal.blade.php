@php use App\Classes\permission; @endphp
@extends('layouts.admin_v2')

@section('title', 'Team Portal')

@section('content')
@php
  // Defensive permission checker: unknown keys just return false (no crash)
  $can = function (?string $key) {
    if (is_null($key)) return true; // public card
    try {
      return permission::permitted($key) === 'success';
    } catch (\Throwable $e) {
      return false;
    }
  };

  $cards = [
    [
      'title'       => 'Volunteer Dashboard',
      'description' => 'Manage shifts, events, and volunteer resources.',
      'icon'        => 'bi-heart',
      'color'       => 'primary',
      'href'        => url('home'),
      'perm'        => null, // visible to any signed-in user
      'btn'         => 'Open Volunteer →',
    ],
    [
      'title'       => 'Admin Dashboard (Old)',
      'description' => 'Access the legacy administration tools and reports.',
      'icon'        => 'bi-gear',
      'color'       => 'warning',
      'href'        => url('dashboard'),
      'perm'        => 'dashboard', // use existing key
      'btn'         => 'Open Admin (Old) →',
    ],/*
    [
      'title'       => 'Admin Dashboard (New)',
      'description' => 'Use the updated admin system for improved management.',
      'icon'        => 'bi-lightning-charge',
      'color'       => 'info',
      'href'        => route('admin_v2.index'),
      'perm'        => 'dashboard', // same existing key
      'btn'         => 'Open Admin (New) →',
    ],*/
    [
      'title'       => 'Radio Dashboard',
      'description' => 'Operate live shows, playlists, and broadcasting tools.',
      'icon'        => 'bi-broadcast',
      'color'       => 'success',
      'href'        => url('/radio/dashboard'),
      'perm'        => 'radio-dashboard', // existing key
      'btn'         => 'Open Radio →',
    ],
    [
      'title'       => 'Creative Workload (Admin)',
      'description' => 'Manage creative requests, tasks, and track team contributions.',
      'icon'        => 'bi-palette',
      'color'       => 'danger',
      'href'        => route('admin.creative.index'),
      'perm'        => 'dashboard',
      'btn'         => 'Open Creative →',
    ],
    [
      'title'       => 'My Creative Tasks',
      'description' => 'View your assigned tasks, track points, and see earned badges.',
      'icon'        => 'bi-list-check',
      'color'       => 'info',
      'href'        => route('personal.creative.dashboard'),
      'perm'        => null,
      'btn'         => 'View My Tasks →',
    ],
  ];

  // Filter by permission
  $cards = array_values(array_filter($cards, fn ($c) => $can($c['perm'])));
@endphp

<div class="container py-2">
  <div class="text-center mb-4"><br><br>
    <img src="{{ asset('assets3/img/logo-white.png') }}" alt="Glory Servant Logo" class="portal-logo mb-3"><br><br>
    <h2 class="fw-semibold mb-1">Team Portal</h2>
    <p class="text-muted">Choose your dashboard to continue</p><br><br>
  </div>

  @if (empty($cards))
    <div class="alert alert-warning text-center">
      You don’t have access to any dashboards yet. Please contact an administrator.
    </div>
  @else
    <div class="row justify-content-center">
      @foreach ($cards as $card)
        <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
          <div class="card h-100 border-0 shadow-sm raised text-center p-4">
            <div class="mb-3">
              <div class="d-inline-flex align-items-center justify-content-center" style="width:64px;height:64px;" class="rounded-circle bg-light text-{{ $card['color'] }}">
                <i class="bi {{ $card['icon'] }} font-22"></i>
              </div>
            </div>
            <h5 class="fw-semibold mb-2">{{ $card['title'] }}</h5>
            <p class="text-secondary small mb-4">{{ $card['description'] }}</p>
            <a href="{{ $card['href'] }}" class="btn btn-{{ $card['color'] }} w-100 fw-semibold">
              {{ __($card['btn']) }}
            </a>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection
