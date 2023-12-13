<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="light" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <title>{{ env('APP_NAME') }}</title>
    <link rel="shortcut icon" href="{{ asset('/image/logo/logo-calegplus.png') }}" />

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    @vite(['resources/vue/app.ts', 'resources/vue/assets/css/app.css'])
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/font-awesome-pro-master/css/all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap.css') }}" />
    <link rel="stylesheet" href="{{ asset('vendor/highlight/styles/paraiso-light.css') }}" />
</head>
<body class="antialiased">
<div id="app"></div>
<script type="text/javascript" src="{{asset('vendor/jquery.min.js')}}"></script>
<script type="text/javascript" src="{{asset('vendor/jquery-ui.min.js')}}"></script>
<script type="text/javascript" src="{{asset('vendor/daterangepicker.min.js')}}"></script>
<script type="text/javascript" src="{{asset('vendor/select2.min.js')}}"></script>
<script type="text/javascript" src="{{asset('vendor/selectize/js/standalone/selectize.min.js')}}"></script>
<script type="text/javascript" src="{{asset('vendor/summernote/summernote-lite.js')}}"></script>
<script type="text/javascript" src="{{asset('vendor/ckeditor-super-build.js')}}"></script>
</body>
</html>
