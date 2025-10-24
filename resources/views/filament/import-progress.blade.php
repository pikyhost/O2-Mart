<div x-data="importProgress()" x-init="startPolling()">
    <div x-show="isImporting" class="mb-4">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-gray-700">Import Progress</span>
            <span class="text-sm text-gray-500" x-text="`${processed}/${total} (${percentage}%)`"></span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                 :style="`width: ${percentage}%`"></div>
        </div>
    </div>
</div>

<script>
function importProgress() {
    return {
        isImporting: false,
        processed: 0,
        total: 0,
        percentage: 0,
        importId: null,
        
        startPolling() {
            // Get import ID from URL or other source
            const urlParams = new URLSearchParams(window.location.search);
            this.importId = urlParams.get('import_id');
            
            if (this.importId) {
                this.isImporting = true;
                this.poll();
            }
        },
        
        async poll() {
            try {
                const response = await fetch(`/api/import-progress/${this.importId}`);
                const data = await response.json();
                
                this.processed = data.processed;
                this.total = data.total;
                this.percentage = data.percentage;
                
                if (data.status === 'processing' && this.percentage < 100) {
                    setTimeout(() => this.poll(), 1000);
                } else {
                    this.isImporting = false;
                }
            } catch (error) {
                console.error('Failed to fetch progress:', error);
                this.isImporting = false;
            }
        }
    }
}
</script>