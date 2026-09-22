<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SIAKAD DIGITAL') }}</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('log-yayasan.jpg') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <meta http-equiv="refresh" content="0; url={{ route('login') }}">
</head>
<body class="bg-stone-100 flex items-center justify-center min-h-screen p-4 font-sans antialiased text-stone-800">
    <div class="max-w-md w-full text-center bg-white border border-stone-200 rounded-2xl p-8 shadow-xs space-y-4">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-stone-50 border border-stone-200 p-2 mx-auto">
            <img src="{{ asset('log-yayasan.jpg') }}" alt="Logo Yayasan" class="w-full h-full object-contain rounded-xl">
        </div>
        <div class="space-y-1">
            <h1 class="text-xl font-extrabold text-stone-900 tracking-tight">SIAKAD Digital Yayasan</h1>
            <p class="text-xs text-stone-600 font-medium">Mengarahkan ke halaman autentikasi sistem...</p>
        </div>
        <div class="pt-2">
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl transition shadow-xs">
                Buka Halaman Login
            </a>
        </div>
    </div>
</body>
</html>
