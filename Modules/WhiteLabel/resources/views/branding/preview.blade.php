@extends('whitelabel::layouts.app')

@section('content')
<div class="preview-container p-8">
    <h1 class="text-2xl font-bold mb-4">Branding Preview</h1>
    
    <div class="branding-preview p-6 rounded-lg shadow-lg" style="background-color: {{ $branding->background_color ?? '#F3F4F6' }};">
        @if($branding->logo_path)
            <img src="{{ Storage::url($branding->logo_path) }}" alt="Logo" class="h-16 mb-4">
        @endif
        
        <h2 class="text-xl font-semibold mb-2" style="color: {{ $branding->primary_color ?? '#3B82F6' }};">
            Primary Color Sample
        </h2>
        
        <p class="mb-4" style="color: {{ $branding->text_color ?? '#1F2937' }};">
            This is a sample text to demonstrate the typography and color settings.
            The quick brown fox jumps over the lazy dog.
        </p>
        
        <button class="px-4 py-2 rounded text-white font-medium" 
                style="background-color: {{ $branding->accent_color ?? '#10B981' }};">
            Accent Button
        </button>
    </div>

    <div class="mt-8 grid grid-cols-2 gap-6">
        <div class="p-4 bg-white rounded shadow">
            <h3 class="font-bold mb-2">Custom CSS</h3>
            <pre class="bg-gray-100 p-2 rounded text-xs overflow-auto">{{ $branding->custom_css }}</pre>
        </div>
        
        <div class="p-4 bg-white rounded shadow">
            <h3 class="font-bold mb-2">Custom JS</h3>
            <pre class="bg-gray-100 p-2 rounded text-xs overflow-auto">{{ $branding->custom_js }}</pre>
        </div>
    </div>
</div>
@endsection
