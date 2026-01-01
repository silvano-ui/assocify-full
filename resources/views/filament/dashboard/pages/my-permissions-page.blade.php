<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
        <x-filament::section>
            <x-slot name="heading">
                My Roles
            </x-slot>

            <div class="flex flex-wrap gap-2">
                @foreach($userRoles as $role)
                    <x-filament::badge :color="$role->role->color ?? 'primary'">
                        {{ $role->role->name }}
                    </x-filament::badge>
                @endforeach
                
                @if($userRoles->isEmpty())
                    <span class="text-gray-500">No roles assigned.</span>
                @endif
            </div>
        </x-filament::section>
    </div>

    <div class="space-y-6">
        @foreach($groupedPermissions as $module => $permissions)
            <x-filament::section :collapsible="true">
                <x-slot name="heading">
                    {{ ucfirst($module) }}
                </x-slot>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($permissions as $permission)
                        <div class="flex items-center space-x-2 p-2 rounded-lg {{ $permission->has_permission ? 'bg-success-50 dark:bg-success-900/10' : 'bg-gray-50 dark:bg-gray-800' }}">
                            @if($permission->has_permission)
                                <x-heroicon-o-check-circle class="w-5 h-5 text-success-500" />
                            @else
                                <x-heroicon-o-x-circle class="w-5 h-5 text-gray-400" />
                            @endif
                            
                            <div class="flex flex-col">
                                <span class="text-sm font-medium {{ $permission->has_permission ? 'text-gray-900 dark:text-gray-100' : 'text-gray-500' }}">
                                    {{ $permission->name }}
                                </span>
                                <span class="text-xs text-gray-400">
                                    {{ $permission->description }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-filament::section>
        @endforeach
    </div>
</x-filament-panels::page>
