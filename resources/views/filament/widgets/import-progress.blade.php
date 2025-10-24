<x-filament-widgets::widget wire:poll.500ms="loadActiveImports">
    <style>
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        .animate-shimmer {
            animation: shimmer 2s infinite;
        }
        @keyframes progress-pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        .progress-bar-active {
            animation: progress-pulse 1.5s ease-in-out infinite;
        }
    </style>
    
    @if($hasActiveImports && isset($activeImports[0]))
        @php $import = $activeImports[0]; @endphp
        
        <!-- Ultra Compact Single Line Design with Dark Mode Support -->
        <div class="bg-white dark:bg-gray-800 border border-success-500 dark:border-success-600 rounded-lg p-4 shadow-sm">
            <!-- Header Label -->
            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 10px;">
                <svg class="w-4 h-4 text-success-600 dark:text-success-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">Last Bulk Upload</span>
            </div>

            <div style="display: flex; align-items: center; justify-content: space-between; gap: 16px; width: 100%;">
                <!-- Left: Status & File -->
                <div style="display: flex; align-items: center; gap: 12px; min-width: 0; flex-shrink: 1; max-width: 320px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div class="relative flex h-5 w-5 items-center justify-center">
                            <div class="absolute h-5 w-5 bg-success-500 rounded-full opacity-20 {{ $import['percentage'] < 100 ? 'animate-ping' : '' }}"></div>
                            <div class="h-3 w-3 bg-success-600 rounded-full {{ $import['percentage'] < 100 ? 'animate-pulse' : '' }}"></div>
                        </div>
                        <div style="display: flex; flex-direction: column; min-width: 0; max-width: 280px;">
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span class="text-xs font-semibold text-gray-900 dark:text-gray-100" style="white-space: nowrap;">
                                    {{ $import['importer'] }}
                                </span>
                            </div>
                            <span class="text-[10px] text-gray-600 dark:text-gray-400" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 280px;" title="{{ $import['file_name'] }}">
                                {{ $import['file_name'] }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Center: Stats in Ultra Compact Format -->
<div style="display: flex; align-items: center; gap: 16px; font-size: 12px; font-weight: 600; flex-shrink: 0; white-space: nowrap;">
                    <div class="flex items-center gap-1">
                        <span class="text-gray-900 dark:text-gray-100">{{ number_format($import['total']) }}</span>
                        <span class="text-[10px] text-gray-400">Total</span>
                    </div>
                    <div class="w-px h-4 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center gap-1">
                        <span class="text-primary-600 dark:text-primary-400">{{ number_format($import['processed']) }}</span>
                        <span class="text-[10px] text-gray-400">Done</span>
                    </div>
                    <div class="w-px h-4 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center gap-1">
                        <svg class="w-3 h-3 text-success-600 dark:text-success-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-success-600 dark:text-success-400 font-semibold">{{ number_format($import['successful']) }}</span>
                    </div>
                    @if($import['failed'] > 0)
                    <div class="w-px h-4 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center gap-1">
                        <svg class="w-3 h-3 text-danger-600 dark:text-danger-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-danger-600 dark:text-danger-400 font-semibold">{{ number_format($import['failed']) }}</span>
                    </div>
                    @else
                    <div class="w-px h-4 bg-gray-300 dark:bg-gray-600"></div>
                    <div class="flex items-center gap-1">
                        <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-gray-400 font-semibold">0</span>
                    </div>
                    @endif
                </div>

                <!-- Right: Progress Bar & Percentage -->
                <div style="display: flex; align-items: center; gap: 16px; flex-shrink: 0; white-space: nowrap;">
                    <!-- Clean minimal progress bar with dark mode support -->
                    <div class="relative w-80 h-6 bg-gray-300 dark:bg-gray-600 rounded-lg overflow-hidden">
                        <!-- Bright GREEN progress fill -->
                        <div class="absolute top-0 left-0 h-full bg-success-500 dark:bg-success-600 transition-all duration-500 ease-out rounded-lg" 
                             style="width: {{ min($import['percentage'], 100) }}%;"></div>
                    </div>
                    <!-- Percentage outside -->
                    <span class="text-base font-bold text-gray-700 dark:text-gray-200 min-w-[60px] text-right tabular-nums">
                        {{ $import['percentage'] }}%
                    </span>
                    <span class="text-xs text-gray-600 dark:text-gray-400 min-w-[55px] text-right">
                        {{ $import['elapsed_time'] }}
                    </span>
                </div>
            </div>
        </div>
    @endif
</x-filament-widgets::widget>