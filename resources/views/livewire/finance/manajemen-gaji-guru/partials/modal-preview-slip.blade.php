{{-- 5. PDF Preview Modal (Rendered on top with highest z-index) --}}
@if ($showPreviewModal && $previewSalary)
    <x-floating-card 
        :show="true" 
        title="Pratinjau Dokumen Slip Gaji Digital" 
        :subtitle="'Periode: ' . $previewSalary->bulan . ' ' . $previewSalary->tahun . ' — ' . ($previewSalary->guru->user->nama ?? '-')"
        badge="SLIP GAJI RESMI"
        badgeVariant="emerald"
        icon="file-text"
        maxWidth="max-w-4xl"
        closeAction="closePreview"
        zIndex="z-[99999]"
    >
        <div class="space-y-4 font-sans">
            <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                <div class="text-xs font-bold text-stone-700">
                    Total THP: <span class="text-emerald-800 font-black">Rp {{ number_format($previewSalary->total_diterima, 0, ',', '.') }}</span>
                </div>
                
                @if ($previewSalaryId)
                    <x-button variant="primary" size="sm" icon="download" href="{{ route('finance.gaji-guru.slip', ['id' => $previewSalaryId, 'download' => 1]) }}" :wireNavigate="false" target="_blank">
                        Unduh PDF Resmi
                    </x-button>
                @endif
            </div>

            <div class="w-full bg-stone-100 rounded-2xl overflow-hidden border border-stone-300 shadow-inner h-[540px]">
                <iframe src="{{ route('finance.gaji-guru.slip', ['id' => $previewSalaryId]) }}" class="w-full h-full border-none"></iframe>
            </div>

            <div class="flex items-center justify-end pt-2">
                <x-button variant="secondary" size="md" wire:click="closePreview">Tutup</x-button>
            </div>
        </div>
    </x-floating-card>
@endif
