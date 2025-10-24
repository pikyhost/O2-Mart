<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Instructions Card -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Import Instructions</span>
                </div>
            </x-slot>

            <div class="prose dark:prose-invert max-w-none">
                <h4 class="text-sm font-semibold mb-2">How to Import Auto Parts</h4>
                <ol class="text-sm space-y-2 list-decimal list-inside text-gray-600 dark:text-gray-400">
                    <li>Prepare your CSV file with auto parts data</li>
                    <li>Ensure the CSV has the required columns (SKU, Name, Price, etc.)</li>
                    <li>Click the "Import Auto Parts" button above</li>
                    <li>Upload your CSV file and map the columns</li>
                    <li>Watch the real-time progress bar update as rows are imported</li>
                    <li>Review any errors after the import completes</li>
                </ol>
            </div>

            <div class="mt-4 p-4 bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-lg">
                <div class="flex gap-2">
                    <svg class="w-5 h-5 text-warning-600 dark:text-warning-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div class="text-sm">
                        <p class="font-semibold text-warning-800 dark:text-warning-200">Important Notes:</p>
                        <ul class="mt-2 space-y-1 text-warning-700 dark:text-warning-300 list-disc list-inside">
                            <li>Large files may take several minutes to process</li>
                            <li>Do not close this page while the import is in progress</li>
                            <li>The progress bar updates automatically every 2 seconds</li>
                            <li>Failed rows will be available for download after completion</li>
                        </ul>
                    </div>
                </div>
            </div>
        </x-filament::section>

        <!-- Required Columns -->
        <x-filament::section>
            <x-slot name="heading">
                Required CSV Columns
            </x-slot>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Column Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Required</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Example</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        <tr>
                            <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100">sku</td>
                            <td class="px-4 py-3 text-sm">
                                <x-filament::badge color="danger" size="xs">Required</x-filament::badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">Unique product identifier</td>
                            <td class="px-4 py-3 text-sm font-mono text-gray-600 dark:text-gray-400">AP-12345</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100">name</td>
                            <td class="px-4 py-3 text-sm">
                                <x-filament::badge color="danger" size="xs">Required</x-filament::badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">Product name</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">Brake Pads Set</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100">price</td>
                            <td class="px-4 py-3 text-sm">
                                <x-filament::badge color="danger" size="xs">Required</x-filament::badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">Product price (AED)</td>
                            <td class="px-4 py-3 text-sm font-mono text-gray-600 dark:text-gray-400">150.00</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100">stock_quantity</td>
                            <td class="px-4 py-3 text-sm">
                                <x-filament::badge color="warning" size="xs">Optional</x-filament::badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">Stock available</td>
                            <td class="px-4 py-3 text-sm font-mono text-gray-600 dark:text-gray-400">50</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100">description</td>
                            <td class="px-4 py-3 text-sm">
                                <x-filament::badge color="warning" size="xs">Optional</x-filament::badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">Product description</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">High quality ceramic...</td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3 text-sm font-mono text-gray-900 dark:text-gray-100">auto_part_brand_name</td>
                            <td class="px-4 py-3 text-sm">
                                <x-filament::badge color="warning" size="xs">Optional</x-filament::badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">Brand name</td>
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">Bosch</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <!-- Recent Imports History -->
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Recent Import History</span>
                </div>
            </x-slot>

            @php
                $recentImports = \Filament\Actions\Imports\Models\Import::where('importer', 'App\Filament\Imports\AutoPartImporter')
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get();
            @endphp

            @if($recentImports->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">File</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Success</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Failed</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($recentImports as $import)
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">
                                        {{ $import->file_name ?? 'N/A' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $import->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        @if($import->completed_at)
                                            <x-filament::badge color="success" size="xs">Completed</x-filament::badge>
                                        @else
                                            <x-filament::badge color="warning" size="xs">Processing</x-filament::badge>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-gray-900 dark:text-gray-100">
                                        {{ number_format($import->total_rows) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-success-600 dark:text-success-400">
                                        {{ number_format($import->successful_rows) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-right font-mono text-danger-600 dark:text-danger-400">
                                        {{ number_format($import->getFailedRowsCount()) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="mt-2 text-sm">No import history yet. Start your first import above!</p>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
