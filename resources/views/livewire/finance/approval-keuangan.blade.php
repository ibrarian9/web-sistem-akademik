<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-stone-200">
        <div>
            <h1 class="text-2xl font-black text-stone-800 tracking-tight flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-amber-50 text-amber-600 border border-amber-200">
                    <x-lucide-shield-check class="w-6 h-6" />
                </span>
                Persetujuan Aksi Keuangan
            </h1>
            <p class="text-sm text-stone-500 mt-1">
                Persetujuan berjenjang untuk aksi <span class="font-semibold text-stone-700">Edit</span> dan <span class="font-semibold text-stone-700">Hapus</span> data keuangan oleh Super Admin & Super Admin 2.
            </p>
        </div>

        @if ($canApprove)
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-green-50 border border-green-200 text-xs font-semibold text-green-700 shadow-sm self-start sm:self-auto">
                <x-lucide-check-circle class="w-4 h-4 text-green-600" />
                Hak Approval Aktif (Super Admin / Super Admin 2)
            </div>
        @else
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-stone-100 border border-stone-200 text-xs font-semibold text-stone-600 shadow-sm self-start sm:self-auto">
                <x-lucide-info class="w-4 h-4 text-stone-500" />
                Mode Pemantauan Status (Keuangan)
            </div>
        @endif
    </div>

    <!-- Alert Messages -->
    @if (session()->has('success'))
        <div class="p-4 rounded-xl bg-green-50 border border-green-200 flex items-start gap-3 text-green-800 text-sm">
            <x-lucide-check-circle class="w-5 h-5 text-green-600 shrink-0 mt-0.5" />
            <div class="flex-1 font-medium">{{ session('success') }}</div>
        </div>
    @endif
    @if (session()->has('error'))
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 flex items-start gap-3 text-red-800 text-sm">
            <x-lucide-alert-circle class="w-5 h-5 text-red-600 shrink-0 mt-0.5" />
            <div class="flex-1 font-medium">{{ session('error') }}</div>
        </div>
    @endif

    <!-- Metrics Cards -->
    <!-- Metrics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <!-- Menunggu -->
        <button wire:click="$set('filterStatus', 'menunggu')" type="button"
                class="text-left p-4 rounded-2xl border transition-all duration-200 {{ $filterStatus === 'menunggu' ? 'bg-amber-50/70 border-amber-300 ring-2 ring-amber-400 shadow-sm' : 'bg-white border-stone-200 hover:border-stone-300 hover:shadow-sm' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Menunggu Approval</span>
                <span class="p-2 rounded-xl bg-amber-100 text-amber-600">
                    <x-lucide-clock class="w-4 h-4" />
                </span>
            </div>
            <p class="text-2xl font-black text-amber-700 mt-2">{{ $counts['menunggu'] }}</p>
            <p class="text-xs text-amber-600 font-medium mt-0.5">Membutuhkan tindakan</p>
        </button>

        <!-- Disetujui -->
        <button wire:click="$set('filterStatus', 'disetujui')" type="button"
                class="text-left p-4 rounded-2xl border transition-all duration-200 {{ $filterStatus === 'disetujui' ? 'bg-green-50/70 border-green-300 ring-2 ring-green-400 shadow-sm' : 'bg-white border-stone-200 hover:border-stone-300 hover:shadow-sm' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Disetujui</span>
                <span class="p-2 rounded-xl bg-green-100 text-green-600">
                    <x-lucide-check-circle class="w-4 h-4" />
                </span>
            </div>
            <p class="text-2xl font-black text-green-700 mt-2">{{ $counts['disetujui'] }}</p>
            <p class="text-xs text-green-600 font-medium mt-0.5">Berhasil dieksekusi</p>
        </button>

        <!-- Ditolak -->
        <button wire:click="$set('filterStatus', 'ditolak')" type="button"
                class="text-left p-4 rounded-2xl border transition-all duration-200 {{ $filterStatus === 'ditolak' ? 'bg-red-50/70 border-red-300 ring-2 ring-red-400 shadow-sm' : 'bg-white border-stone-200 hover:border-stone-300 hover:shadow-sm' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Ditolak</span>
                <span class="p-2 rounded-xl bg-red-100 text-red-600">
                    <x-lucide-x-circle class="w-4 h-4" />
                </span>
            </div>
            <p class="text-2xl font-black text-red-700 mt-2">{{ $counts['ditolak'] }}</p>
            <p class="text-xs text-red-600 font-medium mt-0.5">Tidak diterapkan</p>
        </button>

        <!-- Dibatalkan -->
        <button wire:click="$set('filterStatus', 'dibatalkan')" type="button"
                class="text-left p-4 rounded-2xl border transition-all duration-200 {{ $filterStatus === 'dibatalkan' ? 'bg-stone-100 border-stone-400 ring-2 ring-stone-400 shadow-sm' : 'bg-white border-stone-200 hover:border-stone-300 hover:shadow-sm' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Dibatalkan</span>
                <span class="p-2 rounded-xl bg-stone-100 text-stone-600">
                    <x-lucide-ban class="w-4 h-4" />
                </span>
            </div>
            <p class="text-2xl font-black text-stone-700 mt-2">{{ $counts['dibatalkan'] }}</p>
            <p class="text-xs text-stone-500 font-medium mt-0.5">Dibatalkan pemohon</p>
        </button>

        <!-- Semua -->
        <button wire:click="$set('filterStatus', 'semua')" type="button"
                class="text-left p-4 rounded-2xl border transition-all duration-200 {{ $filterStatus === 'semua' ? 'bg-stone-100 border-stone-400 ring-2 ring-stone-400 shadow-sm' : 'bg-white border-stone-200 hover:border-stone-300 hover:shadow-sm' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-stone-500 uppercase tracking-wider">Total Riwayat</span>
                <span class="p-2 rounded-xl bg-stone-100 text-stone-600">
                    <x-lucide-list class="w-4 h-4" />
                </span>
            </div>
            <p class="text-2xl font-black text-stone-800 mt-2">{{ $counts['total'] }}</p>
            <p class="text-xs text-stone-500 font-medium mt-0.5">Seluruh pengajuan</p>
        </button>
    </div>

    <!-- Filters & Search Toolbar -->
    <div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm flex flex-col md:flex-row items-center gap-3">
        <!-- Search -->
        <div class="relative flex-1 w-full">
            <x-lucide-search class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400" />
            <input type="text" wire:model.live.debounce.300ms="search"
                   placeholder="Cari judul, staf pemohon, atau alasan..."
                   class="w-full pl-10 pr-4 py-2 text-sm bg-stone-50 border border-stone-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-green-500" />
        </div>

        <!-- Filter Status -->
        <select wire:model.live="filterStatus"
                class="w-full md:w-44 px-3 py-2 text-sm bg-stone-50 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="semua">Semua Status</option>
            <option value="menunggu">Menunggu Approval</option>
            <option value="disetujui">Disetujui</option>
            <option value="ditolak">Ditolak</option>
            <option value="dibatalkan">Dibatalkan</option>
        </select>

        <!-- Filter Tipe Aksi -->
        <select wire:model.live="filterTipe"
                class="w-full md:w-36 px-3 py-2 text-sm bg-stone-50 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="semua">Semua Aksi</option>
            <option value="edit">Aksi Edit</option>
            <option value="hapus">Aksi Hapus</option>
        </select>

        <!-- Filter Fitur -->
        <select wire:model.live="filterFitur"
                class="w-full md:w-44 px-3 py-2 text-sm bg-stone-50 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
            <option value="semua">Semua Modul</option>
            <option value="tagihan">Tagihan Siswa</option>
            <option value="pembayaran">Pembayaran</option>
            <option value="tabungan">Tabungan Siswa</option>
            <option value="arus_kas">Arus Kas</option>
            <option value="dana_bos">Dana BOS</option>
            <option value="gaji_guru">Gaji Guru</option>
        </select>
    </div>

    <!-- Approvals Table -->
    <div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-stone-50 border-b border-stone-200 text-stone-600 text-xs uppercase tracking-wider font-semibold">
                    <tr>
                        <th class="py-3 px-4">Tgl Pengajuan</th>
                        <th class="py-3 px-4">Pemohon</th>
                        <th class="py-3 px-4">Aksi & Modul</th>
                        <th class="py-3 px-4">Ringkasan & Alasan</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Approver</th>
                        <th class="py-3 px-4 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200">
                    @forelse ($approvals as $item)
                        <tr class="hover:bg-stone-50/70 transition-colors">
                            <!-- Tanggal -->
                            <td class="py-3.5 px-4 text-xs font-mono text-stone-500 whitespace-nowrap">
                                {{ $item->created_at->isoFormat('D MMM YYYY, HH:mm') }}
                            </td>

                            <!-- Pemohon -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <p class="font-semibold text-stone-800">{{ $item->pemohon->nama ?? 'Keuangan' }}</p>
                                <p class="text-xs text-stone-400 capitalize">{{ $item->pemohon->role->nama ?? 'finance' }}</p>
                            </td>

                            <!-- Aksi & Fitur -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-1.5">
                                    @if ($item->tipe_aksi === 'edit')
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                            <x-lucide-edit-3 class="w-3 h-3" />
                                            EDIT
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <x-lucide-trash-2 class="w-3 h-3" />
                                            HAPUS
                                        </span>
                                    @endif
                                    <span class="px-2 py-0.5 rounded-lg text-xs font-medium bg-stone-100 text-stone-600 capitalize">
                                        {{ str_replace('_', ' ', $item->fitur) }}
                                    </span>
                                </div>
                            </td>

                            <!-- Ringkasan & Alasan -->
                            <td class="py-3.5 px-4 max-w-xs md:max-w-md">
                                <p class="font-bold text-stone-800 text-sm truncate">{{ $item->judul }}</p>
                                <p class="text-xs text-stone-500 mt-0.5 line-clamp-2">
                                    <span class="font-semibold text-stone-600">Alasan:</span> {{ $item->alasan }}
                                </p>
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                @if ($item->status === 'menunggu')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                        <x-lucide-clock class="w-3.5 h-3.5" />
                                        Menunggu
                                    </span>
                                @elseif ($item->status === 'disetujui')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200">
                                        <x-lucide-check-circle class="w-3.5 h-3.5" />
                                        Disetujui
                                    </span>
                                @elseif ($item->status === 'dibatalkan')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-stone-100 text-stone-600 border border-stone-300">
                                        <x-lucide-ban class="w-3.5 h-3.5 text-stone-500" />
                                        Dibatalkan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">
                                        <x-lucide-x-circle class="w-3.5 h-3.5" />
                                        Ditolak
                                    </span>
                                @endif
                            </td>

                            <!-- Approver -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap text-xs text-stone-600">
                                @if ($item->approver)
                                    <p class="font-semibold text-stone-800">{{ $item->approver->nama }}</p>
                                    <p class="text-[11px] text-stone-400 font-mono">{{ $item->tanggal_disetujui?->isoFormat('D MMM YYYY, HH:mm') }}</p>
                                @else
                                    <span class="text-stone-300 italic">-</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button wire:click="openDetail({{ $item->id }})" type="button"
                                            class="px-2.5 py-1.5 rounded-xl border border-stone-200 hover:border-stone-300 bg-white hover:bg-stone-50 text-stone-700 text-xs font-semibold shadow-sm transition">
                                        Rincian
                                    </button>

                                    @if ($item->status === 'menunggu' && ($item->pemohon_id === auth()->id() || in_array(auth()->user()->role->nama ?? '', ['finance', 'super_admin', 'super_admin_2'])))
                                        <button wire:click="openCancelModal({{ $item->id }})" type="button"
                                                class="px-2.5 py-1.5 rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 border border-stone-300 text-xs font-semibold shadow-sm transition flex items-center gap-1"
                                                title="Batalkan pengajuan persetujuan ini">
                                            <x-lucide-ban class="w-3.5 h-3.5 text-stone-500" />
                                            Batalkan
                                        </button>
                                    @endif

                                    @if ($canApprove && $item->status === 'menunggu')
                                        <button wire:click="openApproveModal({{ $item->id }})" type="button"
                                                class="px-2.5 py-1.5 rounded-xl bg-green-600 hover:bg-green-700 text-white text-xs font-semibold shadow-sm transition flex items-center gap-1">
                                            <x-lucide-check class="w-3.5 h-3.5" />
                                            Setujui
                                        </button>
                                        <button wire:click="openRejectModal({{ $item->id }})" type="button"
                                                class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold shadow-sm transition flex items-center gap-1">
                                            <x-lucide-x class="w-3.5 h-3.5" />
                                            Tolak
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-stone-400">
                                <x-lucide-inbox class="w-12 h-12 mx-auto mb-2 text-stone-300" />
                                <p class="text-sm font-medium">Tidak ada permohonan persetujuan yang cocok dengan kriteria filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($approvals->hasPages())
            <div class="p-4 border-t border-stone-200">
                {{ $approvals->links() }}
            </div>
        @endif
    </div>

    <!-- DETAIL MODAL -->
    @if ($showDetailModal && $selectedApproval)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/50 backdrop-blur-sm">
            <div class="bg-white w-full max-w-2xl rounded-2xl border border-stone-200 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-stone-200 flex items-center justify-between bg-stone-50">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 rounded-lg {{ $selectedApproval->tipe_aksi === 'edit' ? 'bg-blue-100 text-blue-700' : 'bg-rose-100 text-rose-700' }}">
                            @if ($selectedApproval->tipe_aksi === 'edit')
                                <x-lucide-edit-3 class="w-4 h-4" />
                            @else
                                <x-lucide-trash-2 class="w-4 h-4" />
                            @endif
                        </span>
                        <div>
                            <h3 class="font-bold text-stone-800 text-base">Rincian Pengajuan Persetujuan</h3>
                            <p class="text-xs text-stone-500">ID Pengajuan #{{ $selectedApproval->id }} &bull; {{ $selectedApproval->created_at->isoFormat('D MMMM YYYY, HH:mm') }}</p>
                        </div>
                    </div>
                    <button wire:click="closeDetail" type="button" class="p-1.5 rounded-xl text-stone-400 hover:text-stone-700 hover:bg-stone-200 transition">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                <!-- Body -->
                <div class="p-6 overflow-y-auto space-y-4 flex-1">
                    <!-- Ringkasan Info -->
                    <div class="bg-stone-50 p-4 rounded-xl border border-stone-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-stone-500 uppercase font-semibold">Tipe Aksi & Fitur</span>
                            <div class="flex items-center gap-1.5">
                                <span class="px-2 py-0.5 text-xs font-bold rounded-md {{ $selectedApproval->tipe_aksi === 'edit' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ strtoupper($selectedApproval->tipe_aksi) }}
                                </span>
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-md bg-stone-200 text-stone-700 uppercase">
                                    {{ $selectedApproval->fitur }}
                                </span>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-stone-500 uppercase font-semibold">Judul Pengajuan</p>
                            <p class="text-sm font-bold text-stone-800 mt-0.5">{{ $selectedApproval->judul }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-stone-500 uppercase font-semibold">Staf Pemohon</p>
                            <p class="text-sm font-semibold text-stone-700 mt-0.5">{{ $selectedApproval->pemohon->nama ?? 'Keuangan' }} ({{ $selectedApproval->pemohon->username ?? '-' }})</p>
                        </div>

                        <div>
                            <p class="text-xs text-stone-500 uppercase font-semibold">Alasan Perubahan / Penghapusan</p>
                            <p class="text-sm text-stone-800 font-medium bg-white p-3 rounded-lg border border-stone-200 mt-1 whitespace-pre-wrap">
                                {{ $selectedApproval->alasan }}
                            </p>
                        </div>
                    </div>

                    <!-- Perbandingan Data (Diff View) -->
                    <div>
                        <h4 class="text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Rincian Data yang Terpengaruh</h4>
                        
                        @if ($selectedApproval->tipe_aksi === 'edit')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Data Lama -->
                                <div class="p-3 bg-stone-50 rounded-xl border border-stone-200">
                                    <p class="text-xs font-bold text-stone-500 uppercase pb-1.5 border-b border-stone-200 flex items-center gap-1">
                                        <x-lucide-arrow-left class="w-3.5 h-3.5 text-stone-400" />
                                        Data Saat Ini (Lama)
                                    </p>
                                    <div class="mt-2 space-y-1 text-xs">
                                        @if ($selectedApproval->data_lama)
                                            @foreach ($selectedApproval->data_lama as $key => $val)
                                                @if (is_array($selectedApproval->data_baru) && array_key_exists($key, $selectedApproval->data_baru) && $selectedApproval->data_baru[$key] != $val)
                                                    <div class="p-1.5 rounded bg-rose-50 border border-rose-100 font-mono">
                                                        <span class="font-bold text-rose-700">{{ $key }}:</span>
                                                        <span class="text-stone-700">{{ is_array($val) ? json_encode($val) : $val }}</span>
                                                    </div>
                                                @else
                                                    <div class="p-1 text-stone-600 font-mono">
                                                        <span class="font-medium text-stone-500">{{ $key }}:</span>
                                                        <span>{{ is_array($val) ? json_encode($val) : $val }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @else
                                            <p class="text-stone-400 italic">Tidak ada snapshot data lama.</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Data Baru -->
                                <div class="p-3 bg-green-50/40 rounded-xl border border-green-200">
                                    <p class="text-xs font-bold text-green-700 uppercase pb-1.5 border-b border-green-200 flex items-center gap-1">
                                        <x-lucide-arrow-right class="w-3.5 h-3.5 text-green-600" />
                                        Data Yang Diajukan (Baru)
                                    </p>
                                    <div class="mt-2 space-y-1 text-xs">
                                        @if ($selectedApproval->data_baru)
                                            @foreach ($selectedApproval->data_baru as $key => $val)
                                                <div class="p-1.5 rounded bg-green-50 border border-green-200 font-mono">
                                                    <span class="font-bold text-green-700">{{ $key }}:</span>
                                                    <span class="text-stone-800 font-semibold">{{ is_array($val) ? json_encode($val) : $val }}</span>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-stone-400 italic">Tidak ada draft nilai baru.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Hapus snapshot -->
                            <div class="p-3 bg-rose-50/40 rounded-xl border border-rose-200">
                                <p class="text-xs font-bold text-rose-700 uppercase pb-1.5 border-b border-rose-200 flex items-center gap-1">
                                    <x-lucide-trash-2 class="w-3.5 h-3.5 text-rose-600" />
                                    Data yang Diajukan untuk Dihapus
                                </p>
                                <div class="mt-2 space-y-1 text-xs font-mono">
                                    @if ($selectedApproval->data_lama)
                                        @foreach ($selectedApproval->data_lama as $key => $val)
                                            <div class="p-1 text-stone-700">
                                                <span class="font-semibold text-rose-800">{{ $key }}:</span>
                                                <span>{{ is_array($val) ? json_encode($val) : $val }}</span>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-stone-400 italic">Snapshot data kosong.</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Status History -->
                    @if ($selectedApproval->status !== 'menunggu')
                        @if ($selectedApproval->status === 'dibatalkan')
                            <div class="p-4 rounded-xl border bg-stone-100 border-stone-300 text-stone-800">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-sm flex items-center gap-1.5 text-stone-700">
                                        <x-lucide-ban class="w-4 h-4 text-stone-500" />
                                        Status: DIBATALKAN
                                    </span>
                                    <span class="text-xs font-mono text-stone-500">{{ $selectedApproval->updated_at?->isoFormat('D MMMM YYYY, HH:mm') }}</span>
                                </div>
                                <p class="text-xs mt-1 text-stone-600">Permohonan persetujuan ini telah dibatalkan.</p>
                                @if ($selectedApproval->catatan_approval)
                                    <p class="text-xs mt-2 p-2.5 bg-white/80 rounded-lg border border-stone-200 text-stone-700">
                                        <span class="font-semibold text-stone-800">Alasan Pembatalan:</span> {{ $selectedApproval->catatan_approval }}
                                    </p>
                                @endif
                            </div>
                        @else
                            <div class="p-4 rounded-xl border {{ $selectedApproval->status === 'disetujui' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800' }}">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-sm">Status: {{ strtoupper($selectedApproval->status) }}</span>
                                    <span class="text-xs font-mono">{{ $selectedApproval->tanggal_disetujui?->isoFormat('D MMMM YYYY, HH:mm') }}</span>
                                </div>
                                <p class="text-xs mt-1">Diproses oleh: <span class="font-bold">{{ $selectedApproval->approver->nama ?? 'Super Admin' }}</span></p>
                                @if ($selectedApproval->catatan_approval)
                                    <p class="text-xs mt-2 p-2 bg-white/70 rounded-lg border border-black/10">
                                        <span class="font-semibold">Catatan Approver:</span> {{ $selectedApproval->catatan_approval }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Footer -->
                <div class="px-6 py-4 border-t border-stone-200 bg-stone-50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <button wire:click="closeDetail" type="button" class="px-4 py-2 text-sm font-semibold rounded-xl border border-stone-200 hover:bg-stone-100 text-stone-700">
                            Tutup
                        </button>

                        @if ($selectedApproval->status === 'menunggu' && ($selectedApproval->pemohon_id === auth()->id() || in_array(auth()->user()->role->nama ?? '', ['finance', 'super_admin', 'super_admin_2'])))
                            <button wire:click="openCancelModal({{ $selectedApproval->id }})" type="button"
                                    class="px-3.5 py-2 text-sm font-semibold rounded-xl bg-stone-100 hover:bg-stone-200 text-stone-700 border border-stone-300 flex items-center gap-1.5 transition">
                                <x-lucide-ban class="w-4 h-4 text-stone-500" />
                                Batalkan Pengajuan
                            </button>
                        @endif
                    </div>

                    @if ($canApprove && $selectedApproval->status === 'menunggu')
                        <div class="flex items-center gap-2">
                            <button wire:click="openRejectModal({{ $selectedApproval->id }})" type="button"
                                    class="px-4 py-2 text-sm font-semibold rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 flex items-center gap-1.5">
                                <x-lucide-x class="w-4 h-4" />
                                Tolak
                            </button>
                            <button wire:click="openApproveModal({{ $selectedApproval->id }})" type="button"
                                    class="px-4 py-2 text-sm font-semibold rounded-xl bg-green-600 hover:bg-green-700 text-white flex items-center gap-1.5 shadow-sm">
                                <x-lucide-check class="w-4 h-4" />
                                Setujui & Terapkan
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

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

    <!-- REJECT MODAL -->
    @if ($showRejectModal && $selectedApproval)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/50 backdrop-blur-sm">
            <div class="bg-white w-full max-w-md rounded-2xl border border-stone-200 shadow-2xl p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="p-3 rounded-xl bg-rose-100 text-rose-700">
                        <x-lucide-alert-triangle class="w-6 h-6" />
                    </div>
                    <div>
                        <h3 class="font-bold text-stone-800 text-base">Tolak Permohonan</h3>
                        <p class="text-xs text-stone-500">Berikan alasan penolakan untuk staf keuangan.</p>
                    </div>
                </div>

                <div class="p-3 bg-stone-50 rounded-xl border border-stone-200 text-xs text-stone-600 space-y-1">
                    <p><span class="font-semibold text-stone-800">Judul:</span> {{ $selectedApproval->judul }}</p>
                    <p><span class="font-semibold text-stone-800">Tindakan:</span> {{ strtoupper($selectedApproval->tipe_aksi) }} data {{ $selectedApproval->fitur }}</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Alasan Penolakan <span class="text-rose-500">*</span></label>
                    <textarea wire:model="rejectReason" rows="3" placeholder="Tuliskan alasan penolakan secara jelas..."
                              class="w-full px-3 py-2 text-sm bg-stone-50 border border-stone-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
                    @error('rejectReason')
                        <p class="text-xs text-rose-600 mt-1 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-stone-200">
                    <button wire:click="closeRejectModal" type="button"
                            class="px-4 py-2 text-sm font-semibold rounded-xl border border-stone-200 hover:bg-stone-100 text-stone-700">
                        Batal
                    </button>
                    <button wire:click="reject" type="button"
                            class="px-4 py-2 text-sm font-semibold rounded-xl bg-rose-600 hover:bg-rose-700 text-white shadow-sm flex items-center gap-1.5">
                        <x-lucide-x class="w-4 h-4" />
                        Tolak Permohonan
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- CANCEL MODAL -->
    @if ($showCancelModal && $selectedApproval)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/50 backdrop-blur-sm">
            <div class="bg-white w-full max-w-md rounded-2xl border border-stone-200 shadow-2xl p-6 space-y-4">
                <div class="flex items-center gap-3">
                    <div class="p-3 rounded-xl bg-stone-100 text-stone-700">
                        <x-lucide-ban class="w-6 h-6 text-stone-600" />
                    </div>
                    <div>
                        <h3 class="font-bold text-stone-800 text-base">Batalkan Permohonan</h3>
                        <p class="text-xs text-stone-500">Pengajuan ini tidak akan diproses lebih lanjut.</p>
                    </div>
                </div>

                <div class="p-3 bg-stone-50 rounded-xl border border-stone-200 text-xs text-stone-600 space-y-1">
                    <p><span class="font-semibold text-stone-800">Judul:</span> {{ $selectedApproval->judul }}</p>
                    <p><span class="font-semibold text-stone-800">Tindakan:</span> {{ strtoupper($selectedApproval->tipe_aksi) }} data {{ $selectedApproval->fitur }}</p>
                    <p><span class="font-semibold text-stone-800">Pemohon:</span> {{ $selectedApproval->pemohon->nama ?? 'Keuangan' }}</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Alasan Pembatalan (Opsional)</label>
                    <textarea wire:model="cancelReason" rows="3" placeholder="Contoh: Kesalahan input nominal, data tidak jadi dihapus, dsb..."
                              class="w-full px-3 py-2 text-sm bg-stone-50 border border-stone-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-stone-400"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-stone-200">
                    <button wire:click="closeCancelModal" type="button"
                            class="px-4 py-2 text-sm font-semibold rounded-xl border border-stone-200 hover:bg-stone-100 text-stone-700">
                        Kembali
                    </button>
                    <button wire:click="cancelApproval" type="button"
                            class="px-4 py-2 text-sm font-semibold rounded-xl bg-stone-800 hover:bg-stone-900 text-white shadow-sm flex items-center gap-1.5">
                        <x-lucide-ban class="w-4 h-4" />
                        Ya, Batalkan Pengajuan
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

