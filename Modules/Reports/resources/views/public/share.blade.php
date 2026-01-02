<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shared Report</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md max-w-md w-full">
        <h1 class="text-2xl font-bold mb-4 text-center">Shared Report</h1>
        <p class="text-gray-600 mb-6 text-center">
            You have been granted access to view report: <br>
            <span class="font-semibold">{{ $share->generatedReport->template->name ?? 'Report' }}</span>
        </p>

        @if($share->password)
            <form action="{{ url()->current() }}/download" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" name="password" id="password" required 
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm p-2 border">
                </div>
                <button type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Download Report
                </button>
            </form>
        @else
            <form action="{{ url()->current() }}/download" method="POST">
                @csrf
                <button type="submit" 
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Download Report
                </button>
            </form>
        @endif
        
        <p class="mt-4 text-xs text-center text-gray-400">
            Expires: {{ $share->expires_at ? $share->expires_at->format('Y-m-d H:i') : 'Never' }}
        </p>
    </div>
</body>
</html>
