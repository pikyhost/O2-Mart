<!-- resources/views/filament/compatibility-rules.blade.php -->
<div class="space-y-4">
    <div class="p-4 bg-white rounded-lg shadow">
        <h3 class="font-bold text-lg mb-2">Compatibility Rules for {{ $product->name }}</h3>
        <p class="text-sm text-gray-600 mb-4">Vehicle: {{ $carModel->make->name }} {{ $carModel->name }} ({{ $carModel->year_from }}-{{ $carModel->year_to ?? 'present' }})</p>

        <div class="space-y-2">
            @foreach($rules as $rule)
                <div class="p-3 border rounded-lg {{ $rule['matches'] ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                    <div class="flex justify-between items-center">
                        <span class="font-medium">{{ $rule['attribute'] }}</span>
                        <span class="px-2 py-1 text-xs rounded-full {{ $rule['matches'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $rule['matches'] ? 'Matches' : 'Does not match' }}
                        </span>
                    </div>
                    <div class="mt-1 text-sm">
                        <p><span class="text-gray-600">Rule:</span> {{ $rule['rule'] }}</p>
                        <p><span class="text-gray-600">Vehicle Value:</span> {{ $rule['value'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
