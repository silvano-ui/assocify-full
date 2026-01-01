<x-filament-panels::page>
    <div class="space-y-6">
        {{-- User Roles --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold mb-4">I miei ruoli</h2>
            @if(count($userRoles) > 0)
                <div class="flex flex-wrap gap-2">
                    @foreach($userRoles as $role)
                        <span class="px-3 py-1 rounded-full text-sm font-medium bg-primary-100 text-primary-800 dark:bg-primary-800 dark:text-primary-100">
                            {{ $role->name }}
                        </span>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">Nessun ruolo assegnato</p>
            @endif
        </div>

        {{-- Permissions --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold mb-4">I miei permessi</h2>
            @if(count($groupedPermissions) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($groupedPermissions as $module => $permissions)
                        <div class="border rounded-lg p-4">
                            <h3 class="font-medium text-gray-900 dark:text-white mb-2 capitalize">{{ $module }}</h3>
                            <ul class="space-y-1">
                                @foreach($permissions as $permission)
                                    <li class="flex items-center text-sm">
                                        <x-heroicon-o-check-circle class="w-4 h-4 text-green-500 mr-2"/>
                                        {{ $permission }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500">Nessun permesso assegnato</p>
            @endif
        </div>
    </div>
</x-filament-panels::page>