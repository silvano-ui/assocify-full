<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $branding->theme_mode ?? 'system' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} - Branding Preview</title>
    
    @if(isset($branding->favicon_path))
        <link rel="icon" href="{{ Storage::url($branding->favicon_path) }}">
    @endif

    @if(isset($branding->font_url))
        <link rel="stylesheet" href="{{ $branding->font_url }}">
    @endif

    <style>
        :root {
            --primary: {{ $branding->primary_color ?? '#000000' }};
            --secondary: {{ $branding->secondary_color ?? '#ffffff' }};
            --accent: {{ $branding->accent_color ?? '#cccccc' }};
            --font-family: {{ $branding->font_family ?? 'sans-serif' }};
        }
        
        body {
            font-family: var(--font-family);
            background-color: var(--secondary);
            color: {{ $branding->text_color ?? '#333333' }};
            margin: 0;
            padding: 20px;
        }

        .header {
            display: flex;
            align-items: center;
            padding: 20px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border-radius: 8px;
        }

        .logo {
            max-height: 50px;
            margin-right: 20px;
        }

        .btn {
            background-color: var(--primary);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-accent {
            background-color: var(--accent);
            color: white;
        }

        {!! $branding->custom_css ?? '' !!}
    </style>
</head>
<body>
    <div class="header">
        @if(isset($branding->logo_path))
            <img src="{{ Storage::url($branding->logo_path) }}" class="logo" alt="Logo">
        @else
            <h1>{{ config('app.name') }}</h1>
        @endif
    </div>

    <div class="content">
        <h1>Branding Preview</h1>
        <p>This is a preview of your branding settings.</p>
        
        <div style="margin-top: 20px;">
            <button class="btn">Primary Button</button>
            <button class="btn btn-accent">Accent Button</button>
        </div>

        <div style="margin-top: 20px; padding: 20px; border: 1px solid #ddd; border-radius: 8px;">
            <h2>Typography Check</h2>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
        </div>
    </div>

    @if(isset($branding->custom_js))
        <script>
            {!! $branding->custom_js !!}
        </script>
    @endif
</body>
</html>
