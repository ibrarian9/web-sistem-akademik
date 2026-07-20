<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-stone-800 tracking-tight">Persetujuan Koreksi Nilai Siswa</h2>
            <p class="text-xs text-stone-500">Verifikasi dan setujui hak akses pengajuan pergantian/koreksi nilai siswa dari guru mata pelajaran.</p>
        </div>

        <div class="flex items-center gap-3">
            <select wire:model.live="filterStatus" class="bg-stone-50 border border-stone-300 text-stone-800 rounded-xl px-3 py-2 text-xs font-semibold focus:outline-none focus:border-indigo-500">
                <option value="semua">Semua Status</option>
                <option value="pending">Menunggu Approval (Pending)</option>
                <option value="disetujui">Disetujui</option>
                <option value="ditolak">Ditolak</option>
            </select>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-xs font-bold flex items-center gap-2">
            <x-lucide-check-circle class="w-4 h-4 text-emerald-600" />
            <span>{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs font-bold flex items-center gap-2">
            <x-lucide-alert-triangle class="w-4 h-4 text-rose-600" />
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-stone-200 text-stone-500 text-xs uppercase tracking-wider">
                    <th class="pb-3 font-bold">Tanggal Pengajuan</th>
                    <th class="pb-3 font-bold">Guru Pemohon</th>
                    <th class="pb-3 font-bold">Siswa &amp; Kelas</th>
                    <th class="pb-3 font-bold">Mata Pelajaran &amp; Komponen</th>
                    <th class="pb-3 font-bold text-center">Nilai Lama &rarr; Baru</th>
                    <th class="pb-3 font-bold">Alasan Koreksi</th>
                    <th class="pb-3 font-bold text-center">Status</th>
                    <th class="pb-3 font-bold text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100 text-xs">
                @forelse ($pengajuans as $p)
                    <tr class="hover:bg-stone-50/50">
                        <td class="py-3.5 text-stone-600 font-mono">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                        <td class="py-3.5 font-bold text-stone-800">{{ $p->guru->user->nama ?? '-' }}</td>
                        <td class="py-3.5">
                            <span class="font-bold text-stone-800 block">{{ $p->nilai->siswa->user->nama ?? '-' }}</span>
                            <span class="text-[10px] text-stone-500 font-medium">Kelas {{ $p->nilai->kelas->nama_kelas ?? '-' }}</span>
                        </td>
                        <td class="py-3.5">
                            <span class="font-bold text-indigo-700 block">{{ $p->nilai->mapel->nama ?? '-' }}</span>
                            <span class="text-[10px] text-stone-500 font-medium">{{ $p->nilai->komponenNilai->nama ?? '-' }}</span>
                        </td>
                        <td class="py-3.5 text-center">
                            <span class="line-through text-rose-500 font-bold mr-1">{{ floatval($p->nilai->nilai ?? 0) }}</span>
                            <span class="text-emerald-700 font-black text-sm bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">{{ floatval($p->nilai_baru) }}</span>
                        </td>
                        <td class="py-3.5 text-stone-600 italic max-w-xs">{{ $p->alasan }}</td>
                        <td class="py-3.5 text-center">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase
                                {{ $p->status === 'pending' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}
                                {{ $p->status === 'disetujui' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : '' }}
                                {{ $p->status === 'ditolak' ? 'bg-rose-50 text-rose-700 border border-rose-200' : '' }}
                            ">
                                {{ $p->status }}
                            </span>
                        </td>
                        <td class="py-3.5 text-center">
                            @if ($p->status === 'pending')
                                <div class="flex items-center justify-center gap-1.5">
                                    <button wire:click="approve({{ $p->id }})" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold transition shadow-sm" title="Setujui Perubahan Nilai">
                                        Setujui
                                    </button>
                                    <button wire:click="reject({{ $p->id }})" class="px-2.5 py-1 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-bold transition shadow-sm" title="Tolak Pengajuan">
                                        Tolak
                                    </button>
                                </div>
                            @else
                                <span class="text-[10px] text-stone-400 font-medium">Selesai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-stone-400 text-xs">
                            Belum ada pengajuan koreksi nilai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $pengajuans->links() }}
        </div>
    </div>
</div>
