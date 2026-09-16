<!-- Approvals Table -->
<div class="bg-white rounded-2xl border border-stone-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead class="bg-stone-50 border-b border-stone-200 text-stone-600 text-xs uppercase tracking-wider font-semibold">
                <tr>
                    <th class="py-3 px-3.5 w-10 text-center">
                        <input type="checkbox" wire:model.live="selectAll" 
                               class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" 
                               title="Pilih semua pengajuan menunggu" />
                    </th>
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
                    @php
                        $isPending = $item->status === 'menunggu';
                        $isSelected = in_array($item->id, $selectedIds);
                    @endphp
                    <tr class="hover:bg-stone-50/70 transition-colors {{ $isSelected ? 'bg-emerald-50/40 font-medium' : '' }}">
                        <!-- Checkbox -->
                        <td class="py-3.5 px-3.5 text-center">
                            @if ($isPending)
                                <input type="checkbox" wire:model.live="selectedIds" value="{{ $item->id }}" 
                                       class="rounded border-stone-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer" />
                            @else
                                <span class="text-stone-300 text-xs">&ndash;</span>
                            @endif
                        </td>
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
                        <td colspan="8" class="py-12 text-center text-stone-400">
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
