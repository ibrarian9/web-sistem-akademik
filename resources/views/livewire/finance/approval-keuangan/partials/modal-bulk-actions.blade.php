<!-- Bulk Action Floating Bar -->
@if (count($selectedIds) > 0)
    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 bg-stone-900/95 text-white px-5 py-3 rounded-2xl shadow-2xl border border-stone-700/80 backdrop-blur-md flex items-center gap-3.5 max-w-2xl w-[92vw] sm:w-auto animate-in fade-in slide-in-from-bottom-4">
        <div class="flex items-center gap-2 pr-2 border-r border-stone-700">
            <span class="w-6 h-6 rounded-lg bg-emerald-500 text-stone-950 font-black text-xs flex items-center justify-center">
                {{ count($selectedIds) }}
            </span>
            <span class="text-xs font-bold text-stone-200 whitespace-nowrap">Pengajuan Dipilih</span>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if ($canApprove)
                <button type="button" wire:click="openBulkApproveModal" 
                        class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                    <x-lucide-check-circle-2 class="w-4 h-4" />
                    <span>Setuju Massal ({{ count($selectedIds) }})</span>
                </button>
            @endif

            <button type="button" wire:click="openBulkCancelModal" 
                    class="px-3.5 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold shadow-sm transition flex items-center gap-1.5">
                <x-lucide-ban class="w-4 h-4" />
                <span>Batalkan Massal ({{ count($selectedIds) }})</span>
            </button>

            <button type="button" wire:click="resetSelection" 
                    class="px-2.5 py-1.5 rounded-xl bg-stone-800 hover:bg-stone-700 text-stone-300 text-xs font-semibold transition">
                Batal Pilih
            </button>
        </div>
    </div>
@endif

<!-- BULK APPROVE MODAL -->
@if ($showBulkApproveModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/50 backdrop-blur-sm">
        <div class="bg-white w-full max-w-lg rounded-2xl border border-stone-200 shadow-2xl p-6 space-y-4">
            <div class="flex items-center gap-3">
                <div class="p-3 rounded-xl bg-green-100 text-green-700">
                    <x-lucide-shield-check class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="font-bold text-stone-800 text-base">Setujui Massal ({{ count($selectedIds) }} Data)</h3>
                    <p class="text-xs text-stone-500">Seluruh aksi yang dipilih akan langsung diterapkan ke sistem database.</p>
                </div>
            </div>

            <div class="p-3 bg-stone-50 rounded-xl border border-stone-200 max-h-48 overflow-y-auto space-y-2">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-stone-500 block">Daftar Pengajuan Terpilih:</span>
                @foreach ($this->selectedApprovals as $item)
                    <div class="text-xs border-b border-stone-200/60 pb-1.5 last:border-b-0">
                        <div class="font-bold text-stone-800 flex items-center gap-1.5">
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold {{ $item->tipe_aksi === 'edit' ? 'bg-blue-100 text-blue-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ strtoupper($item->tipe_aksi) }}
                            </span>
                            <span class="truncate">{{ $item->judul }}</span>
                        </div>
                        <div class="text-[11px] text-stone-500 mt-0.5 flex items-center gap-1">
                            <span>Pemohon: {{ $item->pemohon->nama ?? 'Keuangan' }}</span>
                            <span>&bull;</span>
                            <span class="capitalize">{{ str_replace('_', ' ', $item->fitur) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Catatan Persetujuan Massal (Opsional)</label>
                <textarea wire:model="bulkApprovalNote" rows="2" placeholder="Tambahkan catatan jika diperlukan..."
                          class="w-full px-3 py-2 text-sm bg-stone-50 border border-stone-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-stone-200">
                <button wire:click="closeBulkApproveModal" type="button"
                        class="px-4 py-2 text-sm font-semibold rounded-xl border border-stone-200 hover:bg-stone-100 text-stone-700">
                    Batal
                </button>
                <button wire:click="bulkApprove" type="button"
                        class="px-4 py-2 text-sm font-semibold rounded-xl bg-green-600 hover:bg-green-700 text-white shadow-sm flex items-center gap-1.5">
                    <x-lucide-check class="w-4 h-4" />
                    Ya, Setujui Semua ({{ count($selectedIds) }})
                </button>
            </div>
        </div>
    </div>
@endif

<!-- BULK CANCEL MODAL -->
@if ($showBulkCancelModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/50 backdrop-blur-sm">
        <div class="bg-white w-full max-w-lg rounded-2xl border border-stone-200 shadow-2xl p-6 space-y-4">
            <div class="flex items-center gap-3">
                <div class="p-3 rounded-xl bg-stone-100 text-stone-700">
                    <x-lucide-ban class="w-6 h-6 text-stone-600" />
                </div>
                <div>
                    <h3 class="font-bold text-stone-800 text-base">Batalkan Massal ({{ count($selectedIds) }} Data)</h3>
                    <p class="text-xs text-stone-500">Seluruh pengajuan yang dipilih tidak akan diproses lebih lanjut.</p>
                </div>
            </div>

            <div class="p-3 bg-stone-50 rounded-xl border border-stone-200 max-h-48 overflow-y-auto space-y-2">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-stone-500 block">Daftar Pengajuan Terpilih:</span>
                @foreach ($this->selectedApprovals as $item)
                    <div class="text-xs border-b border-stone-200/60 pb-1.5 last:border-b-0">
                        <div class="font-bold text-stone-800 flex items-center gap-1.5">
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-extrabold {{ $item->tipe_aksi === 'edit' ? 'bg-blue-100 text-blue-800' : 'bg-rose-100 text-rose-800' }}">
                                {{ strtoupper($item->tipe_aksi) }}
                            </span>
                            <span class="truncate">{{ $item->judul }}</span>
                        </div>
                        <div class="text-[11px] text-stone-500 mt-0.5 flex items-center gap-1">
                            <span>Pemohon: {{ $item->pemohon->nama ?? 'Keuangan' }}</span>
                            <span>&bull;</span>
                            <span class="capitalize">{{ str_replace('_', ' ', $item->fitur) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Alasan Pembatalan (Opsional)</label>
                <textarea wire:model="bulkCancelReason" rows="2" placeholder="Contoh: Dibatalkan secara massal karena perubahan kebijakan / data salah..."
                          class="w-full px-3 py-2 text-sm bg-stone-50 border border-stone-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-stone-400"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-stone-200">
                <button wire:click="closeBulkCancelModal" type="button"
                        class="px-4 py-2 text-sm font-semibold rounded-xl border border-stone-200 hover:bg-stone-100 text-stone-700">
                    Kembali
                </button>
                <button wire:click="bulkCancel" type="button"
                        class="px-4 py-2 text-sm font-semibold rounded-xl bg-stone-800 hover:bg-stone-900 text-white shadow-sm flex items-center gap-1.5">
                    <x-lucide-ban class="w-4 h-4" />
                    Ya, Batalkan Semua ({{ count($selectedIds) }})
                </button>
            </div>
        </div>
    </div>
@endif
