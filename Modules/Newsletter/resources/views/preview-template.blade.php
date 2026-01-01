<div class="p-4 bg-white rounded-lg">
    <div class="mb-4 border-b pb-2">
        <h3 class="font-bold text-lg">{{ $template->name }}</h3>
        <p class="text-sm text-gray-500">Type: {{ $template->type }}</p>
    </div>
    <div class="border rounded p-4 bg-gray-50">
        @if($template->html_content)
            <iframe srcdoc="{{ $template->html_content }}" class="w-full h-96 border-0"></iframe>
        @else
            <p class="text-gray-400 text-center py-8">No content to preview</p>
        @endif
    </div>
</div>
