<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title','Site')</title>
  @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
  @include('partials.header')
  <main>@yield('content')</main>
  @include('partials.footer')
</body>
</html>
