<!-- APPROVE MODAL -->
@if ($showApproveModal && $selectedApproval)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/50 backdrop-blur-sm">
        <div class="bg-white w-full max-w-md rounded-2xl border border-stone-200 shadow-2xl p-6 space-y-4">
            <div class="flex items-center gap-3">
                <div class="p-3 rounded-xl bg-green-100 text-green-700">
                    <x-lucide-shield-check class="w-6 h-6" />
                </div>
                <div>
                    <h3 class="font-bold text-stone-800 text-base">Konfirmasi Persetujuan</h3>
                    <p class="text-xs text-stone-500">Aksi akan langsung dieksekusi di database.</p>
                </div>
            </div>

            <div class="p-3 bg-stone-50 rounded-xl border border-stone-200 text-xs text-stone-600 space-y-1">
                <p><span class="font-semibold text-stone-800">Judul:</span> {{ $selectedApproval->judul }}</p>
                <p><span class="font-semibold text-stone-800">Tindakan:</span> {{ strtoupper($selectedApproval->tipe_aksi) }} data {{ $selectedApproval->fitur }}</p>
                <p><span class="font-semibold text-stone-800">Pemohon:</span> {{ $selectedApproval->pemohon->nama ?? 'Keuangan' }}</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Catatan Persetujuan (Opsional)</label>
                <textarea wire:model="approvalNote" rows="3" placeholder="Tambahkan catatan jika diperlukan..."
                          class="w-full px-3 py-2 text-sm bg-stone-50 border border-stone-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2 border-t border-stone-200">
                <button wire:click="closeApproveModal" type="button"
                        class="px-4 py-2 text-sm font-semibold rounded-xl border border-stone-200 hover:bg-stone-100 text-stone-700">
                    Batal
                </button>
                <button wire:click="approve" type="button"
                        class="px-4 py-2 text-sm font-semibold rounded-xl bg-green-600 hover:bg-green-700 text-white shadow-sm flex items-center gap-1.5">
                    <x-lucide-check class="w-4 h-4" />
                    Ya, Setujui
                </button>
            </div>
        </div>
    </div>
@endif
