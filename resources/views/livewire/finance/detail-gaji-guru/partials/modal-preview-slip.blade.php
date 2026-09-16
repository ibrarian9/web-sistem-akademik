<!-- Modal Pratinjau Slip Gaji PDF (Rendered on top with highest z-index) -->
@if ($showPreviewModal && $previewSalaryId)
    <x-floating-card 
        :show="true" 
        title="Pratinjau Slip Gaji Pegawai" 
        subtitle="Dokumen resmi Slip Honorarium Pegawai Yayasan F3 ber-QR Code verifikasi." 
        badge="DOKUMEN RESMI" 
        badgeVariant="emerald" 
        icon="file-text" 
        maxWidth="max-w-4xl" 
        closeAction="closePreview"
        zIndex="z-[99999]"
    >
        <div class="space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-stone-200">
                <span class="text-xs text-stone-500 font-medium">Dokumen siap dicetak atau disimpan dalam format PDF.</span>
                <x-button variant="primary" size="sm" icon="download" href="{{ route('finance.gaji-guru.slip', ['id' => $previewSalaryId, 'download' => 1]) }}" :wireNavigate="false" target="_blank">
                    Unduh File PDF
                </x-button>
            </div>

            <div class="w-full h-[620px] rounded-2xl overflow-hidden border border-stone-200 shadow-inner bg-stone-100">
                <iframe src="{{ route('finance.gaji-guru.slip', ['id' => $previewSalaryId]) }}" class="w-full h-full border-none"></iframe>
            </div>

            <div class="flex items-center justify-end pt-2">
                <x-button variant="secondary" size="md" wire:click="closePreview">Tutup</x-button>
            </div>
        </div>
    </x-floating-card>
@endif
