<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengajuan Anggaran & Pencairan Dana"
        :steps="[
            ['title' => 'Buat Pengajuan', 'desc' => 'Klik Buat Pengajuan Dana untuk mengisi judul proposal, bidang pengaju, rincian biaya, serta nota pendukung.'],
            ['title' => 'Verifikasi & Approval', 'desc' => 'Staf Finance dan Kepala Sekolah akan meninjau kelayakan anggaran sebelum persetujuan diberikan.'],
            ['title' => 'Pencairan Dana', 'desc' => 'Proposal yang disetujui dapat dicairkan oleh Finance dan tercatat otomatis pada buku kas keluar.']
        ]"
    />

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-stone-800 tracking-tight">Pengajuan Penggunaan Dana</h2>
            <p class="text-sm text-stone-500">Menu pengajuan anggaran operasional (buku, seragam, dll.) dengan persetujuan bertingkat.</p>
        </div>

        @if ($userRole === 'finance')
        <button wire:click="openModal" class="py-2.5 px-5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold transition duration-200 shadow-md shadow-green-600/10 flex items-center gap-2 w-fit">
            <x-lucide-plus-circle class="w-4 h-4" />
            <span>Buat Pengajuan Dana</span>
        </button>
        @endif
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif



    <!-- Main Content Table Panel -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-6">
        <!-- Filter & Search Controls -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="w-full sm:w-72 relative">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari judul / no pengajuan..." 
                    class="w-full pl-9 pr-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500/50 focus:border-green-500" />
                <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3 top-2.5" />
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <span class="text-xs font-semibold text-stone-400 uppercase tracking-wider">Status:</span>
                <select wire:model.live="filterStatus" class="px-3 py-2 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500">
                    <option value="semua">Semua Status</option>
                    <option value="menunggu_koordinator">Menunggu Koordinator</option>
                    <option value="menunggu_kepala_yayasan">Menunggu Kepala Yayasan</option>
                    <option value="disetujui">Disetujui (Siap Realisasi)</option>
                    <option value="direalisasi">Direalisasi (Selesai)</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-stone-200 text-xs font-semibold text-stone-500 uppercase tracking-wider">
                        <th class="pb-3">No. Pengajuan</th>
                        <th class="pb-3">Judul &amp; Kategori</th>
                        <th class="pb-3 text-right">Jumlah (Rp)</th>
                        <th class="pb-3 text-center">Pemohon</th>
                        <th class="pb-3 text-center">Status</th>
                        <th class="pb-3 text-center">Aksi / Persetujuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 text-sm">
                    @forelse ($pengajuans as $item)
                        <tr class="hover:bg-stone-50/80">
                            <!-- No. Pengajuan -->
                            <td class="py-4">
                                <span class="font-mono font-bold text-stone-800 block text-xs">{{ $item->no_pengajuan }}</span>
                                <span class="text-[11px] text-stone-400">{{ date('d M Y', strtotime($item->created_at)) }}</span>
                            </td>

                            <!-- Judul & Kategori -->
                            <td class="py-4 max-w-xs">
                                <span class="font-bold text-stone-900 block truncate" title="{{ $item->judul }}">{{ $item->judul }}</span>
                                <span class="text-xs text-stone-500 inline-block px-2 py-0.5 bg-stone-100 rounded border border-stone-200 mt-1">
                                    {{ $item->kategori }}
                                </span>
                                @if ($item->keterangan)
                                    <p class="text-xs text-stone-400 mt-1 line-clamp-1" title="{{ $item->keterangan }}">{{ $item->keterangan }}</p>
                                @endif
                            </td>

                            <!-- Jumlah -->
                            <td class="py-4 text-right">
                                <span class="font-black text-stone-900 text-base block">
                                    Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                </span>
                                <span class="text-[11px] font-semibold {{ $item->jumlah > 1000000 ? 'text-purple-600' : 'text-stone-400' }} block">
                                    {{ $item->jumlah > 1000000 ? 'Tier > Rp 1 Juta' : 'Tier ≤ Rp 1 Juta' }}
                                </span>
                            </td>

                            <!-- Pemohon -->
                            <td class="py-4 text-center">
                                <span class="font-medium text-stone-700 block text-xs">{{ $item->pemohon->nama ?? '-' }}</span>
                            </td>

                            <!-- Status -->
                            <td class="py-4 text-center">
                                @if ($item->status === 'menunggu_koordinator' || $item->status === 'menunggu_pengawas')
                                    <span class="px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-lg text-xs font-bold inline-flex items-center gap-1">
                                        <x-lucide-clock class="w-3.5 h-3.5" /> Menunggu Pengawas
                                    </span>
                                @elseif ($item->status === 'menunggu_kepala_yayasan')
                                    <span class="px-2.5 py-1 bg-purple-50 text-purple-700 border border-purple-200 rounded-lg text-xs font-bold inline-flex items-center gap-1">
                                        <x-lucide-clock class="w-3.5 h-3.5" /> Menunggu Kepala Yayasan
                                    </span>
                                @elseif ($item->status === 'disetujui')
                                    <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-xs font-bold inline-flex items-center gap-1">
                                        <x-lucide-check-circle class="w-3.5 h-3.5" /> Disetujui (Belum Dicairkan)
                                    </span>
                                @elseif ($item->status === 'direalisasi')
                                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 border border-blue-200 rounded-lg text-xs font-bold inline-flex items-center gap-1">
                                        <x-lucide-check-check class="w-3.5 h-3.5" /> Direalisasi (Selesai)
                                    </span>
                                @elseif ($item->status === 'ditolak')
                                    <span class="px-2.5 py-1 bg-red-50 text-red-700 border border-red-200 rounded-lg text-xs font-bold inline-flex items-center gap-1">
                                        <x-lucide-x-circle class="w-3.5 h-3.5" /> Ditolak
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Pengawas / Super Admin Approval Action -->
                                    @if (in_array($item->status, ['menunggu_koordinator', 'menunggu_pengawas']) && in_array($userRole, ['pengawas', 'koordinator', 'super_admin']))
                                        <button wire:click="approveByKoordinator({{ $item->id }})" title="Setujui sebagai Pengawas" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1 shadow-sm">
                                            <x-lucide-check class="w-3.5 h-3.5" />
                                            <span>Setujui</span>
                                        </button>
                                        <button wire:click="openRejectModal({{ $item->id }})" title="Tolak" class="px-2.5 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-xs font-semibold transition border border-red-200">
                                            <x-lucide-x class="w-3.5 h-3.5" />
                                        </button>
                                    @endif

                                    <!-- Kepala Yayasan Approval Action -->
                                    @if ($item->status === 'menunggu_kepala_yayasan' && in_array($userRole, ['kepala-sekolah', 'super_admin']))
                                        <button wire:click="approveByKepalaYayasan({{ $item->id }})" title="Setujui sebagai Kepala Yayasan" class="px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1 shadow-sm">
                                            <x-lucide-check-check class="w-3.5 h-3.5" />
                                            <span>Acc Yayasan</span>
                                        </button>
                                        <button wire:click="openRejectModal({{ $item->id }})" title="Tolak" class="px-2.5 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg text-xs font-semibold transition border border-red-200">
                                            <x-lucide-x class="w-3.5 h-3.5" />
                                        </button>
                                    @endif

                                    <!-- Finance Realisasi Action -->
                                    @if ($item->status === 'disetujui' && in_array($userRole, ['finance', 'super_admin']))
                                        <button wire:click="realisasikanDana({{ $item->id }})" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1 shadow-sm">
                                            <x-lucide-banknote class="w-3.5 h-3.5" />
                                            <span>Cairkan Dana</span>
                                        </button>
                                    @endif

                                    @if ($item->status === 'direalisasi')
                                        <span class="text-xs text-stone-400 font-medium">Dicairkan {{ date('d/m/Y', strtotime($item->realisasi_pada)) }}</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-stone-400 font-medium text-sm">
                                Belum ada data pengajuan penggunaan dana.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pt-4 border-t border-stone-200">
            {{ $pengajuans->links() }}
        </div>
    </div>

    <!-- Modal Form Buat Pengajuan -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 backdrop-blur-sm p-4">
            <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xl max-w-lg w-full space-y-5">
                <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                    <h3 class="text-lg font-bold text-stone-800">Form Pengajuan Penggunaan Dana</h3>
                    <button wire:click="closeModal" class="text-stone-400 hover:text-stone-600">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                <form wire:submit.prevent="createPengajuan" class="space-y-4">
                    <!-- Judul -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Judul Pengajuan</label>
                        <input type="text" wire:model="judul" placeholder="Contoh: Pembelian Buku Paket Matematika Kelas 7" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500" />
                        @error('judul') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Kategori -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Kategori Pengajuan</label>
                        <select wire:model="kategori" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500">
                            @foreach ($kategoriOptions as $kat)
                                <option value="{{ $kat }}">{{ $kat }}</option>
                            @endforeach
                        </select>
                        @error('kategori') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Jumlah -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Nominal Anggaran (Rp)</label>
                        <input type="number" wire:model.live="jumlah" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500 font-bold text-right text-emerald-700" />
                        @if ($jumlah > 1000000)
                            <span class="text-[11px] text-purple-600 font-bold block">Memerlukan persetujuan 2 Tahap (Koordinator &amp; Kepala Yayasan).</span>
                        @elseif ($jumlah > 0)
                            <span class="text-[11px] text-emerald-600 font-bold block">Memerlukan persetujuan Koordinator saja.</span>
                        @endif
                        @error('jumlah') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Target Realisasi -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Target Tanggal Realisasi</label>
                        <input type="date" wire:model="target_realisasi" class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500" />
                    </div>

                    <!-- Keterangan -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Keterangan / Rincian Kebutuhan</label>
                        <textarea wire:model="keterangan" rows="3" placeholder="Jelaskan kebutuhan pengajuan secara rinci..." class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-green-500"></textarea>
                        @error('keterangan') <span class="text-red-600 text-xs block mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-stone-200">
                        <button type="button" wire:click="closeModal" class="py-2.5 px-5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-sm font-bold transition">
                            Batal
                        </button>
                        <button type="submit" class="py-2.5 px-6 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-bold transition shadow-md shadow-green-600/10">
                            Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Penolakan -->
    @if ($showRejectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/50 backdrop-blur-sm p-4">
            <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xl max-w-md w-full space-y-4">
                <h3 class="text-lg font-bold text-stone-800">Alasan Penolakan Pengajuan</h3>
                <textarea wire:model="catatan_penolakan" rows="3" placeholder="Masukkan alasan penolakan..." class="w-full px-3 py-2.5 bg-stone-50 border border-stone-300 rounded-xl text-stone-800 text-sm focus:ring-2 focus:ring-red-500"></textarea>
                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" wire:click="$set('showRejectModal', false)" class="py-2 px-4 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-sm font-bold">
                        Batal
                    </button>
                    <button type="button" wire:click="rejectPengajuan" class="py-2 px-5 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-bold">
                        Konfirmasi Tolak
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
