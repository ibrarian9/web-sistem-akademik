<!-- RIWAYAT SURAT TAB CARD -->
<div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
        <div class="max-w-md w-full">
            <x-search-input wire:model.live.debounce.300ms="searchRiwayat" placeholder="Cari nomor surat atau nama penerima..." />
        </div>
    </div>

    <x-table loadingTarget="searchRiwayat, activeTab">
        <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
            <tr>
                <x-table.th class="w-44">Nomor Surat</x-table.th>
                <x-table.th class="min-w-[180px]">Jenis Surat</x-table.th>
                <x-table.th class="min-w-[180px]">Nama Penerima</x-table.th>
                <x-table.th class="w-32">Tanggal Surat</x-table.th>
                <x-table.th class="min-w-[140px]">Dibuat Oleh</x-table.th>
                <x-table.th align="center" class="min-w-[200px]">Aksi</x-table.th>
            </tr>
        </thead>
        <tbody class="divide-y divide-stone-200 bg-white">
            @forelse ($riwayats as $r)
                @php
                    $jenisLabel = match($r->jenis_surat) {
                        'aktif_sekolah' => 'Surat Keterangan Aktif Sekolah',
                        'pengalaman_kerja' => 'Surat Pengalaman Kerja',
                        'menerima_pindah' => 'Mutasi: Menerima Pindah',
                        'pindah_sekolah' => 'Mutasi: Pindah Sekolah',
                        default => $r->jenis_surat,
                    };
                @endphp
                <tr class="hover:bg-stone-50 transition">
                    <td class="p-3.5 font-bold text-stone-900 border-r border-stone-200">{{ $r->nomor_surat }}</td>
                    <td class="p-3.5 border-r border-stone-200 font-extrabold text-stone-900">{{ $jenisLabel }}</td>
                    <td class="p-3.5 border-r border-stone-200 font-bold text-stone-800">{{ strtoupper($r->penerima_nama) }}</td>
                    <td class="p-3.5 border-r border-stone-200 font-semibold text-stone-600">{{ $r->tanggal_surat ? $r->tanggal_surat->format('d M Y') : '-' }}</td>
                    <td class="p-3.5 border-r border-stone-200 font-medium text-stone-500">{{ $r->creator->nama ?? 'Admin TU' }}</td>
                    <td class="p-3.5 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <x-button type="button" variant="secondary" size="xs" icon="edit" wire:click="loadRiwayatSurat({{ $r->id }})">
                                Edit / Preview
                            </x-button>
                            <x-button type="button" variant="warning" size="xs" icon="download" wire:click="downloadPdfById({{ $r->id }})">
                                PDF
                            </x-button>
                            @if (!auth()->user()?->isSuperAdmin2())
                                <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click="deleteRiwayat({{ $r->id }})" data-confirm="Apakah Anda yakin ingin menghapus arsip surat ini?">
                                    Hapus
                                </x-button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-stone-400">
                        <x-table.empty title="Belum ada riwayat penerbitan surat" subtitle="Buat surat resmi pertama Anda melalui tab Buat Surat Baru di atas." />
                    </td>
                </tr>
            @endforelse
        </tbody>
    </x-table>

    <div class="pt-2">
        {{ $riwayats->links() }}
    </div>
</div>
