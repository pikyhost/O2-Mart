<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Cache Management</h3>
            <p class="text-sm text-gray-600 mb-4">
                Use the buttons above to clear cache or optimize the application.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium text-gray-900">Clear All Cache</h4>
                    <p class="text-sm text-gray-600 mt-1">Clears all cached data including config, routes, views, and compiled files.</p>
                </div>
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium text-gray-900">Optimize & Cache</h4>
                    <p class="text-sm text-gray-600 mt-1">Optimizes the application by caching config, routes, and views for better performance.</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>