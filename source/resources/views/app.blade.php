<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title inertia>Buddy Script</title>

  <link rel="shortcut icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
  
  <link rel="stylesheet" href="{{ asset('css/public-style.css') }}" />

  @viteReactRefresh
  @vite(['resources/js/app.jsx', 'resources/css/app.css'])
  @inertiaHead
</head>

<body>
  @inertia
</body>

</html>