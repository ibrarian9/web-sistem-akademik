<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Penggajian Guru & Tenaga Pendidik"
        :steps="[
            ['title' => 'Generate Draf Gaji', 'desc' => 'Klik Generate Draf Gaji untuk menghitung gaji pokok, tunjangan jam mengajar, serta insentif piket bulanan.'],
            ['title' => 'Potongan Kasbon', 'desc' => 'Sistem secara otomatis memperhitungkan potongan cicilan pinjaman/kasbon guru yang masih berjalan.'],
            ['title' => 'Pencairan & Slip Gaji', 'desc' => 'Klik Cairkan Gaji untuk mengonfirmasi pembayaran dan mengunduh Slip Gaji fisik ber-QR Code & TTD.']
        ]"
    />

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-stone-800 tracking-tight">Manajemen Gaji Guru</h2>
            <p class="text-sm text-stone-500">Kelola draf penggajian bulanan guru, insentif, potongan kasbon, dan pencatatan pengeluaran otomatis.</p>
        </div>
        <button wire:click="openGenerateModal" class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold transition duration-200 shadow-md shadow-green-600/10 flex items-center gap-2">
            <x-lucide-plus-circle class="w-4 h-4" />
            <span>Generate Draf Gaji</span>
        </button>
    </div>



    @if (session()->has('message'))
        <div class="p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm font-semibold flex items-center gap-2">
            <x-lucide-check-circle class="w-5 h-5 text-green-600" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm font-semibold flex items-center gap-2">
            <x-lucide-alert-triangle class="w-5 h-5 text-red-600" />
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Search & Filters Bar -->
    <div class="bg-white border border-stone-200 rounded-2xl p-4 shadow-sm flex flex-wrap items-center gap-4">
        <div class="flex-1 min-w-[200px]">
            <input wire:model.live="search" type="text" placeholder="Cari nama guru..." class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500" />
        </div>
        
        <div class="w-40">
            <select wire:model.live="filterStatus" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500">
                <option value="">Semua Status</option>
                <option value="draft">Draft</option>
                <option value="dibayar">Dibayar</option>
            </select>
        </div>

        <div class="w-40">
            <select wire:model.live="filterBulan" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500">
                <option value="">Semua Bulan</option>
                @foreach ($listBulan as $b)
                    <option value="{{ $b }}">{{ $b }}</option>
                @endforeach
            </select>
        </div>

        <div class="w-32">
            <input wire:model.live="filterTahun" type="number" placeholder="Tahun" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500 text-center" />
        </div>
    </div>

    <!-- Salary List Table -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-stone-200">
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider">Guru</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center">Periode</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Gaji Pokok</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Insentif</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Potongan</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-right">Total Diterima</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center">Status</th>
                        <th class="pb-3 text-xs font-semibold text-stone-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @forelse ($salaries as $sal)
                        <tr class="hover:bg-stone-50">
                            <td class="py-3.5 text-sm font-semibold text-stone-800">{{ $sal->guru->user->nama ?? '-' }}</td>
                            <td class="py-3.5 text-sm text-stone-600 text-center">{{ $sal->bulan }} {{ $sal->tahun }}</td>
                            <td class="py-3.5 text-sm text-stone-700 text-right">Rp {{ number_format($sal->gaji_pokok, 0, ',', '.') }}</td>
                            <td class="py-3.5 text-sm text-stone-700 text-right">
                                <span class="text-xs block text-stone-500">BPJS: Rp {{ number_format($sal->insentif_bpjs, 0, ',', '.') }}</span>
                                <span class="text-xs block text-stone-500">Ngaji: Rp {{ number_format($sal->insentif_maghrib_mengaji, 0, ',', '.') }}</span>
                            </td>
                            <td class="py-3.5 text-sm text-stone-700 text-right">
                                <span class="text-xs block text-stone-500">Kasbon: Rp {{ number_format($sal->potongan_peminjaman, 0, ',', '.') }}</span>
                                <span class="text-xs block text-stone-500">Lain: Rp {{ number_format($sal->potongan_lainnya, 0, ',', '.') }}</span>
                            </td>
                            <td class="py-3.5 text-sm font-bold text-stone-900 text-right">Rp {{ number_format($sal->total_diterima, 0, ',', '.') }}</td>
                            <td class="py-3.5 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
                                    {{ $sal->status === 'dibayar' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}
                                ">
                                    {{ ucfirst($sal->status) }}
                                </span>
                            </td>
                            <td class="py-3.5 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if ($sal->status === 'draft')
                                        <button wire:click="openEditModal({{ $sal->id }})" class="p-1.5 text-stone-500 hover:text-stone-700 hover:bg-stone-100 rounded-lg transition" title="Edit Draf">
                                            <x-lucide-edit-3 class="w-4 h-4" />
                                        </button>
                                        <button wire:click="paySalary({{ $sal->id }})" class="px-2.5 py-1 bg-green-100 hover:bg-green-200 text-green-800 rounded-lg text-xs font-bold transition flex items-center gap-1" title="Bayar">
                                            <x-lucide-credit-card class="w-3.5 h-3.5" />
                                            <span>Bayar</span>
                                        </button>
                                        <button wire:click="deleteDraft({{ $sal->id }})" wire:confirm="Apakah Anda yakin ingin menghapus draf gaji ini?" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition" title="Hapus Draf">
                                            <x-lucide-trash-2 class="w-4 h-4" />
                                        </button>
                                    @else
                                        <a href="{{ route('finance.gaji-guru.slip', $sal->id) }}" target="_blank" class="px-2.5 py-1 bg-blue-100 hover:bg-blue-200 text-blue-800 rounded-lg text-xs font-bold transition flex items-center gap-1" title="Unduh Slip Gaji">
                                            <x-lucide-file-text class="w-3.5 h-3.5" />
                                            <span>Slip PDF</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-stone-400 font-medium text-sm">
                                Belum ada draf/data gaji guru yang direkam.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pt-4 border-t border-stone-200">
            {{ $salaries->links() }}
        </div>
    </div>

    <!-- Generate Draft Modal -->
    @if ($showGenerateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/40 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white border border-stone-200 rounded-2xl w-full max-w-md p-6 shadow-xl space-y-6">
                <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                    <h3 class="text-base font-bold text-stone-800">Generate Draf Gaji Guru</h3>
                    <button wire:click="closeGenerateModal" class="text-stone-400 hover:text-stone-600">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>
                
                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Bulan</label>
                        <select wire:model="generateBulan" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500">
                            @foreach ($listBulan as $b)
                                <option value="{{ $b }}">{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Tahun</label>
                        <input wire:model="generateTahun" type="number" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500 text-center font-bold" />
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-stone-200">
                    <button wire:click="closeGenerateModal" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-sm font-semibold transition">
                        Batal
                    </button>
                    <button wire:click="generateDrafts" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold transition shadow-md shadow-green-600/10">
                        Generate
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Edit Draft Modal -->
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/40 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white border border-stone-200 rounded-2xl w-full max-w-lg p-6 shadow-xl space-y-6">
                <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                    <h3 class="text-base font-bold text-stone-800">Edit Draf Gaji: {{ $editGuruNama }}</h3>
                    <button wire:click="closeEditModal" class="text-stone-400 hover:text-stone-600">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5 col-span-2">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Gaji Pokok</label>
                        <input wire:model="editGajiPokok" wire:keyup="calculateEditTotal" type="number" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm font-bold text-right focus:ring-2 focus:ring-green-500/50" />
                        @error('editGajiPokok') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Insentif BPJS</label>
                        <input wire:model="editInsentifBpjs" wire:keyup="calculateEditTotal" type="number" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm text-right focus:ring-2 focus:ring-green-500/50" />
                        @error('editInsentifBpjs') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Insentif Maghrib Mengaji</label>
                        <input wire:model="editInsentifMaghrib" wire:keyup="calculateEditTotal" type="number" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm text-right focus:ring-2 focus:ring-green-500/50" />
                        @error('editInsentifMaghrib') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Potongan Pinjaman (Kasbon)</label>
                        <input wire:model="editPotonganPinjaman" wire:keyup="calculateEditTotal" type="number" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm text-right focus:ring-2 focus:ring-green-500/50" />
                        @error('editPotonganPinjaman') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Potongan Lainnya</label>
                        <input wire:model="editPotonganLainnya" wire:keyup="calculateEditTotal" type="number" class="w-full px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm text-right focus:ring-2 focus:ring-green-500/50" />
                        @error('editPotonganLainnya') <span class="text-red-600 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-2 p-4 bg-stone-50 rounded-xl border border-stone-200 flex items-center justify-between">
                        <span class="text-sm font-semibold text-stone-600">Total Diterima Guru:</span>
                        <span class="text-lg font-bold text-green-700">Rp {{ number_format($editTotalDiterima, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-3 border-t border-stone-200">
                    <button wire:click="closeEditModal" class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-sm font-semibold transition">
                        Batal
                    </button>
                    <button wire:click="saveEdit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold transition shadow-md shadow-green-600/10">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
