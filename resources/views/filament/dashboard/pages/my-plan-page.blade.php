<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-filament::section>
            <x-slot name="heading">
                Current Plan
            </x-slot>

            <div class="space-y-4">
                <div class="text-2xl font-bold text-primary-600" style="color: rgb(var(--primary-600))">
                    {{ $this->plan_name }}
                </div>
                
                @if($this->isTrial())
                    <div class="p-4 bg-yellow-50 text-yellow-700 rounded-lg border border-yellow-200 dark:bg-yellow-900/20 dark:text-yellow-400 dark:border-yellow-700">
                        <strong>Trial Active:</strong> You have {{ $this->trial_days_left }} days left in your trial.
                    </div>
                @endif

                <div class="flex gap-4">
                     {{-- Placeholder for Upgrade button --}}
                    <x-filament::button tag="a" href="#" color="primary">
                        Upgrade Plan
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section>
            <x-slot name="heading">
                Usage Overview
            </x-slot>
            
             <div class="space-y-4">
                @foreach($this->features as $tf)
                    @if($tf->limit_value > 0)
                        @php
                            $percentage = min(100, ($tf->used_value / $tf->limit_value) * 100);
                            $color = $percentage > 90 ? 'danger' : ($percentage > 75 ? 'warning' : 'primary');
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm font-medium mb-1">
                                <span>{{ $tf->feature->name }}</span>
                                <span>{{ $tf->used_value }} / {{ $tf->limit_value }} {{ $tf->feature->unit_name }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                <div class="h-2.5 rounded-full" style="width: {{ $percentage }}%; background-color: rgb(var(--{{ $color }}-500));"></div>
                            </div>
                        </div>
                    @endif
                @endforeach
                 
                 @if($this->features->where('limit_value', 0)->count() > 0)
                     <div class="pt-4 border-t dark:border-gray-700">
                         <h4 class="text-sm font-semibold mb-2">Unlimited Features</h4>
                         <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400">
                             @foreach($this->features->where('limit_value', 0) as $tf)
                                 <li>{{ $tf->feature->name }}</li>
                             @endforeach
                         </ul>
                     </div>
                 @endif
            </div>
        </x-filament::section>
    </div>
    
    <x-filament::section>
        <x-slot name="heading">
            All Active Features
        </x-slot>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
             @foreach($this->features as $tf)
                <div class="p-4 border rounded-lg dark:border-gray-700">
                    <div class="font-semibold">{{ $tf->feature->name }}</div>
                    <div class="text-sm text-gray-500">{{ $tf->feature->description }}</div>
                    <div class="mt-2 text-xs flex flex-wrap gap-2">
                        <span class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-800">
                            Source: {{ ucfirst($tf->source) }}
                        </span>
                         @if($tf->expires_at)
                            <span class="px-2 py-1 rounded bg-gray-100 dark:bg-gray-800">
                                Expires: {{ $tf->expires_at->format('Y-m-d') }}
                            </span>
                        @endif
                    </div>
                </div>
             @endforeach
        </div>
    </x-filament::section>
</x-filament-panels::page>
