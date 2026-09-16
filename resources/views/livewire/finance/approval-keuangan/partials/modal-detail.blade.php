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
