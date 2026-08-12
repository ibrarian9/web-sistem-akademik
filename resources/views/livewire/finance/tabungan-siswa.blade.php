<div class="space-y-6 font-sans">
    <!-- Header Page -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-full text-xs font-bold uppercase tracking-wider inline-block">
                SD Tahfizh F3 Digital Finance
            </span>
            <h2 class="text-xl font-extrabold text-stone-900 tracking-tight mt-1">Manajemen Tabungan Siswa</h2>
            <p class="text-xs text-stone-500 font-medium">Kelola transaksi setoran &amp; penarikan tabungan siswa serta pantau saldo terkini secara akurat.</p>
        </div>
    </div>

    <!-- Alert Success Notification -->
    @if (session()->has('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs font-bold">
                <x-lucide-check-circle class="w-4 h-4 text-emerald-600" />
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>
    @endif

    <!-- Metric Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Saldo Tabungan</span>
                <div class="p-2 bg-emerald-50 text-emerald-700 rounded-xl border border-emerald-200">
                    <x-lucide-wallet class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-stone-900">Rp {{ number_format($totalSaldoGlobal, 0, ',', '.') }}</div>
            <div class="text-[11px] text-stone-500 font-medium">Saldo kumulatif seluruh siswa</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Akumulasi Setor</span>
                <div class="p-2 bg-blue-50 text-blue-700 rounded-xl border border-blue-200">
                    <x-lucide-arrow-down-left class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-stone-900">Rp {{ number_format($totalSetorAll, 0, ',', '.') }}</div>
            <div class="text-[11px] text-stone-500 font-medium">Total dana masuk tabungan</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Akumulasi Tarik</span>
                <div class="p-2 bg-amber-50 text-amber-700 rounded-xl border border-amber-200">
                    <x-lucide-arrow-up-right class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-stone-900">Rp {{ number_format($totalTarikAll, 0, ',', '.') }}</div>
            <div class="text-[11px] text-stone-500 font-medium">Total dana ditarik siswa</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Siswa Aktif Menabung</span>
                <div class="p-2 bg-purple-50 text-purple-700 rounded-xl border border-purple-200">
                    <x-lucide-users class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-stone-900">{{ number_format($jumlahSiswaMenabung) }} <span class="text-xs font-bold text-stone-400">Siswa</span></div>
            <div class="text-[11px] text-stone-500 font-medium">Memiliki transaksi aktif</div>
        </div>
    </div>

    <!-- Controls: Search & Filter -->
    <div class="bg-white border border-stone-200 p-4 rounded-2xl shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto flex-1">
            <div class="relative w-full sm:w-72">
                <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3.5 top-3" />
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari nama, NIS, atau NISN..." 
                       class="w-full pl-10 pr-4 py-2 bg-stone-50 border border-stone-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:bg-white transition" />
            </div>

            <select wire:model.live="filterKelas" class="w-full sm:w-48 py-2 px-3 bg-stone-50 border border-stone-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                <option value="">Semua Kelas</option>
                @foreach ($kelasList as $k)
                    <option value="{{ $k->id }}">Kelas {{ $k->nama_kelas }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Student Savings Table -->
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 font-extrabold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3.5 px-4">Siswa</th>
                        <th class="py-3.5 px-4">NIS / NISN</th>
                        <th class="py-3.5 px-4">Kelas</th>
                        <th class="py-3.5 px-4 text-right">Saldo Tabungan</th>
                        <th class="py-3.5 px-4 text-center">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 font-medium">
                    @forelse ($siswas as $siswa)
                        @php
                            $latestTabungan = \App\Models\Tabungan::where('siswa_id', $siswa->id)
                                ->orderBy('tanggal', 'desc')
                                ->orderBy('id', 'desc')
                                ->first();
                            $saldo = $latestTabungan ? (float) $latestTabungan->saldo_akhir : 0;
                        @endphp
                        <tr class="hover:bg-stone-50/80 transition duration-150">
                            <td class="py-3.5 px-4">
                                <div class="font-extrabold text-stone-900 text-xs">{{ $siswa->user->nama ?? '-' }}</div>
                                <div class="text-[10px] text-stone-500">Wali: {{ $siswa->nama_wali ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-stone-800">{{ $siswa->nis }}</div>
                                <div class="text-[10px] text-stone-400">{{ $siswa->nisn ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 bg-stone-100 border border-stone-200 text-stone-800 rounded-lg font-bold text-[10px]">
                                    Kelas {{ $siswa->kelas->nama_kelas ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="font-black text-emerald-700 text-sm">Rp {{ number_format($saldo, 0, ',', '.') }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" wire:click="openTransactionModal({{ $siswa->id }}, 'setor')" 
                                            class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-[11px] inline-flex items-center gap-1 transition shadow-sm">
                                        <x-lucide-plus-circle class="w-3.5 h-3.5" /> Setor
                                    </button>

                                    <button type="button" wire:click="openTransactionModal({{ $siswa->id }}, 'tarik')" 
                                            class="px-2.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-bold text-[11px] inline-flex items-center gap-1 transition shadow-sm">
                                        <x-lucide-minus-circle class="w-3.5 h-3.5" /> Tarik
                                    </button>

                                    <button type="button" wire:click="openHistoryModal({{ $siswa->id }})" 
                                            class="px-2.5 py-1.5 bg-stone-100 hover:bg-stone-200 text-stone-700 border border-stone-300 rounded-xl font-bold text-[11px] inline-flex items-center gap-1 transition">
                                        <x-lucide-history class="w-3.5 h-3.5" /> Histori
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-stone-400 font-medium">
                                <x-lucide-user-x class="w-8 h-8 mx-auto mb-2 opacity-50" />
                                Tidak ada data siswa ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($siswas->hasPages())
            <div class="p-4 border-t border-stone-200 bg-stone-50">
                {{ $siswas->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Transaction (Setor / Tarik) -->
    @if ($showTransactionModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full shadow-2xl border border-stone-200 p-6 space-y-5 animate-in fade-in zoom-in duration-200">
                <div class="flex items-center justify-between border-b border-stone-100 pb-4">
                    <div class="flex items-center gap-2">
                        <div class="p-2.5 rounded-2xl {{ $jenis === 'setor' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                            @if ($jenis === 'setor')
                                <x-lucide-arrow-down-left class="w-5 h-5" />
                            @else
                                <x-lucide-arrow-up-right class="w-5 h-5" />
                            @endif
                        </div>
                        <div>
                            <h3 class="text-base font-black text-stone-900">
                                {{ $jenis === 'setor' ? 'Setor Tabungan Siswa' : 'Penarikan Tabungan Siswa' }}
                            </h3>
                            <p class="text-xs text-stone-500 font-semibold">{{ $selectedSiswaNama }}</p>
                        </div>
                    </div>
                    <button type="button" wire:click="closeModals" class="text-stone-400 hover:text-stone-600 p-1">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                <!-- Info Current Balance -->
                <div class="p-3 bg-stone-50 border border-stone-200 rounded-2xl flex items-center justify-between">
                    <span class="text-xs font-bold text-stone-600">Saldo Tabungan Saat Ini:</span>
                    <span class="text-sm font-black text-emerald-700">Rp {{ number_format($selectedSiswaSaldo, 0, ',', '.') }}</span>
                </div>

                <form wire:submit.prevent="saveTransaction" class="space-y-4 text-xs font-medium">
                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Jenis Transaksi</label>
                        <select wire:model.live="jenis" class="w-full py-2.5 px-3 bg-stone-50 border border-stone-200 rounded-xl font-bold focus:ring-2 focus:ring-emerald-500">
                            <option value="setor">Setor Tabungan (+)</option>
                            <option value="tarik">Tarik Tabungan (-)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Nominal (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" wire:model="nominal" min="1000" step="500" placeholder="Contoh: 50000" 
                               class="w-full py-2.5 px-3 bg-stone-50 border border-stone-200 rounded-xl font-bold focus:ring-2 focus:ring-emerald-500 text-sm" />
                        @error('nominal') <span class="text-[11px] text-rose-500 font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                        <input type="date" wire:model="tanggal" class="w-full py-2.5 px-3 bg-stone-50 border border-stone-200 rounded-xl font-bold focus:ring-2 focus:ring-emerald-500" />
                        @error('tanggal') <span class="text-[11px] text-rose-500 font-bold block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block font-bold text-stone-700 mb-1">Catatan / Keterangan</label>
                        <textarea wire:model="keterangan" rows="2" placeholder="Catatan transaksi (opsional)..." 
                                  class="w-full py-2.5 px-3 bg-stone-50 border border-stone-200 rounded-xl font-medium focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-stone-100">
                        <button type="button" wire:click="closeModals" class="px-4 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl font-bold">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2.5 {{ $jenis === 'setor' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-amber-600 hover:bg-amber-700' }} text-white rounded-xl font-black shadow-md transition">
                            Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal History Mutasi -->
    @if ($showHistoryModal && $selectedSiswaHistory)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-stone-200 p-6 space-y-5 animate-in fade-in zoom-in duration-200">
                <div class="flex items-center justify-between border-b border-stone-100 pb-4">
                    <div>
                        <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-extrabold uppercase">
                            Histori Mutasi Tabungan
                        </span>
                        <h3 class="text-base font-black text-stone-900 mt-1">{{ $selectedSiswaHistory->user->nama ?? '-' }}</h3>
                        <p class="text-xs text-stone-500 font-semibold">NIS: {{ $selectedSiswaHistory->nis }} | Kelas: {{ $selectedSiswaHistory->kelas->nama_kelas ?? '-' }}</p>
                    </div>
                    <button type="button" wire:click="closeModals" class="text-stone-400 hover:text-stone-600 p-1">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                <div class="max-h-96 overflow-y-auto custom-scrollbar border border-stone-200 rounded-2xl">
                    <table class="w-full text-left text-xs text-stone-700">
                        <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 font-extrabold uppercase tracking-wider text-[10px] sticky top-0 bg-stone-50">
                            <tr>
                                <th class="py-3 px-3">Tanggal / Kode</th>
                                <th class="py-3 px-3">Jenis</th>
                                <th class="py-3 px-3 text-right">Nominal</th>
                                <th class="py-3 px-3 text-right">Saldo Akhir</th>
                                <th class="py-3 px-3">Petugas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-100 font-medium">
                            @forelse ($historyTransactions as $tx)
                                <tr class="hover:bg-stone-50">
                                    <td class="py-2.5 px-3">
                                        <div class="font-bold text-stone-900">{{ \Carbon\Carbon::parse($tx->tanggal)->translatedFormat('d M Y') }}</div>
                                        <div class="text-[10px] text-stone-400">{{ $tx->kode_transaksi }}</div>
                                    </td>
                                    <td class="py-2.5 px-3">
                                        @if ($tx->jenis === 'setor')
                                            <span class="px-2 py-0.5 bg-emerald-100 text-emerald-900 rounded-md text-[10px] font-black uppercase">Setor</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-amber-100 text-amber-900 rounded-md text-[10px] font-black uppercase">Tarik</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-bold {{ $tx->jenis === 'setor' ? 'text-emerald-700' : 'text-amber-700' }}">
                                        {{ $tx->jenis === 'setor' ? '+' : '-' }} Rp {{ number_format($tx->nominal, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 px-3 text-right font-extrabold text-stone-900">
                                        Rp {{ number_format($tx->saldo_akhir, 0, ',', '.') }}
                                    </td>
                                    <td class="py-2.5 px-3">
                                        <div class="text-[11px] font-semibold text-stone-800">{{ $tx->petugas->nama ?? 'Sistem' }}</div>
                                        @if ($tx->keterangan)
                                            <div class="text-[10px] text-stone-400 italic">{{ $tx->keterangan }}</div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-stone-400 font-medium">
                                        Belum ada riwayat mutasi tabungan untuk siswa ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-end pt-2 border-t border-stone-100">
                    <button type="button" wire:click="closeModals" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl font-bold text-xs">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
