<!-- Floating Card Form Transaction (Setor / Tarik) -->
<x-floating-card 
    :show="$showTransactionModal" 
    :title="$jenis === 'setor' ? 'Setor Tabungan Siswa' : 'Penarikan Tabungan Siswa'" 
    :subtitle="$selectedSiswaNama"
    :badge="$jenis === 'setor' ? 'SETOR TABUNGAN (+)' : 'TARIK TABUNGAN (-)'"
    :badgeVariant="$jenis === 'setor' ? 'emerald' : 'amber'"
    icon="wallet"
    maxWidth="max-w-lg"
    closeAction="closeModals"
>
    <!-- Info Current Balance -->
    <div class="p-4 bg-stone-50 border border-stone-200 rounded-2xl flex items-center justify-between shadow-2xs mb-4">
        <span class="text-xs font-bold text-stone-600 uppercase tracking-wider">Saldo Tabungan Terkini:</span>
        <span class="text-base font-black text-emerald-800">Rp {{ number_format($selectedSiswaSaldo, 0, ',', '.') }}</span>
    </div>

    <form wire:submit.prevent="saveTransaction" class="space-y-4 font-sans">
        <!-- Jenis Transaksi -->
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Jenis Transaksi</label>
            <div class="grid grid-cols-2 gap-3">
                <label class="flex items-center justify-center gap-2 p-3 border rounded-xl cursor-pointer text-xs font-bold select-none transition {{ $jenis === 'setor' ? 'bg-emerald-50 border-emerald-500 text-emerald-900 ring-2 ring-emerald-500/20 shadow-2xs' : 'bg-stone-50 border-stone-300 text-stone-600' }}">
                    <input type="radio" wire:model.live="jenis" value="setor" class="hidden" />
                    <x-lucide-arrow-down-left class="w-4 h-4 text-emerald-600" />
                    <span>Setor Tabungan (+)</span>
                </label>
                <label class="flex items-center justify-center gap-2 p-3 border rounded-xl cursor-pointer text-xs font-bold select-none transition {{ $jenis === 'tarik' ? 'bg-amber-50 border-amber-500 text-amber-900 ring-2 ring-amber-500/20 shadow-2xs' : 'bg-stone-50 border-stone-300 text-stone-600' }}">
                    <input type="radio" wire:model.live="jenis" value="tarik" class="hidden" />
                    <x-lucide-arrow-up-right class="w-4 h-4 text-amber-600" />
                    <span>Tarik Tabungan (-)</span>
                </label>
            </div>
        </div>

        <!-- Nominal Transaksi -->
        <x-input-currency 
            label="Nominal Transaksi (Rp)" 
            name="nominal" 
            wire:model="nominal" 
            placeholder="Contoh: 50.000" 
            required 
        />

        <!-- Tanggal Transaksi -->
        <x-input 
            type="date" 
            label="Tanggal Transaksi" 
            name="tanggal" 
            wire:model="tanggal" 
            required 
        />

        <!-- Keterangan -->
        <div class="space-y-1.5">
            <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Catatan / Keterangan</label>
            <textarea wire:model="keterangan" rows="2" placeholder="Catatan transaksi tabungan (opsional)..." class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs resize-none"></textarea>
        </div>

        <div class="flex items-center justify-end gap-2 pt-3 border-t border-stone-200">
            <x-button variant="secondary" size="md" wire:click="closeModals">
                Batal
            </x-button>
            <x-button variant="{{ $jenis === 'setor' ? 'primary' : 'warning' }}" size="md" type="submit" loadingTarget="saveTransaction">
                Simpan Transaksi
            </x-button>
        </div>
    </form>
</x-floating-card>
