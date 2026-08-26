<div class="space-y-6">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Persetujuan Koreksi Nilai (Koordinator)"
        :steps="[
            ['title' => 'Verifikasi Alasan Koreksi', 'desc' => 'Periksa alasan pergantian nilai, nilai asal, serta nilai baru yang diajukan oleh guru.'],
            ['title' => 'Setujui / Tolak', 'desc' => 'Klik Setujui untuk memperbarui nilai siswa secara otomatis pada basis data rapor.'],
            ['title' => 'Filter Status', 'desc' => 'Gunakan dropdown filter status di kanan atas untuk memilah permohonan pending atau riwayat.']
        ]"
    />

    <!-- Page Header -->
    <x-page-header 
        title="Persetujuan Koreksi Nilai Siswa" 
        subtitle="Verifikasi dan setujui permohonan pergantian / koreksi nilai siswa dari guru mata pelajaran."
        badge="AKADEMIK"
        badgeVariant="emerald"
        icon="user-check"
    >
        <x-slot:actions>
            <select wire:model.live="filterStatus" class="bg-stone-50 border border-stone-200 text-stone-800 rounded-xl px-3.5 py-2 text-xs font-bold focus:ring-emerald-500 focus:border-emerald-500">
                <option value="semua">Semua Status</option>
                <option value="pending">Menunggu Approval (Pending)</option>
                <option value="disetujui">Disetujui</option>
                <option value="ditolak">Ditolak</option>
            </select>
        </x-slot:actions>
    </x-page-header>

    <div class="bg-white border border-stone-200 rounded-2xl shadow-xs overflow-hidden">
        <x-table loadingTarget="filterStatus">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="w-40">Tanggal Pengajuan</x-table.th>
                    <x-table.th class="w-48">Guru Pemohon</x-table.th>
                    <x-table.th class="w-48">Siswa & Kelas</x-table.th>
                    <x-table.th class="min-w-[160px]">Mata Pelajaran & Komponen</x-table.th>
                    <x-table.th align="center" class="w-36">Nilai Lama &rarr; Baru</x-table.th>
                    <x-table.th class="min-w-[180px]">Alasan Koreksi</x-table.th>
                    <x-table.th align="center" class="w-32">Status</x-table.th>
                    <x-table.th align="center" class="w-36">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($pengajuans as $p)
                    <tr class="hover:bg-stone-50 transition">
                        <td class="p-3.5 text-stone-600 font-mono text-xs border-r border-stone-200">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                        <td class="p-3.5 font-bold text-stone-900 text-xs border-r border-stone-200">{{ $p->guru->user->nama ?? '-' }}</td>
                        <td class="p-3.5 border-r border-stone-200">
                            <span class="font-extrabold text-stone-900 block text-xs">{{ $p->nilai->siswa->user->nama ?? '-' }}</span>
                            <span class="text-[10px] text-stone-500 font-medium">Kelas {{ $p->nilai->kelas->nama_kelas ?? '-' }}</span>
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <span class="font-bold text-emerald-800 block text-xs">{{ $p->nilai->mapel->nama ?? '-' }}</span>
                            <span class="text-[10px] text-stone-500 font-medium">{{ $p->nilai->komponenNilai->nama ?? '-' }}</span>
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            <span class="line-through text-rose-500 font-bold mr-1.5 text-xs">{{ floatval($p->nilai->nilai ?? 0) }}</span>
                            <span class="text-emerald-800 font-black text-sm bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">{{ floatval($p->nilai_baru) }}</span>
                        </td>
                        <td class="p-3.5 text-stone-600 italic text-xs border-r border-stone-200">{{ $p->alasan }}</td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if ($p->status === 'pending')
                                <x-badge variant="amber" size="xs">Pending</x-badge>
                            @elseif ($p->status === 'disetujui')
                                <x-badge variant="emerald" size="xs">Disetujui</x-badge>
                            @else
                                <x-badge variant="rose" size="xs">Ditolak</x-badge>
                            @endif
                        </td>
                        <td class="p-3.5 text-center">
                            @if ($p->status === 'pending')
                                <div class="flex items-center justify-center gap-1.5">
                                    <x-button variant="primary" size="xs" icon="check" wire:click="approve({{ $p->id }})" title="Setujui Perubahan Nilai">
                                        Setuju
                                    </x-button>
                                    <x-button variant="danger-outline" size="xs" icon="x" wire:click="reject({{ $p->id }})" title="Tolak Pengajuan">
                                        Tolak
                                    </x-button>
                                </div>
                            @else
                                <span class="text-[10px] text-stone-400 font-bold uppercase">Selesai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="8" title="Belum ada pengajuan" message="Belum ada pengajuan koreksi nilai yang masuk." />
                @endforelse
            </tbody>
        </x-table>

        <div class="mt-4">
            {{ $pengajuans->links() }}
        </div>
    </div>
</div>
