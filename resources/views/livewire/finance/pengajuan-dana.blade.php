<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Pengajuan Penggunaan Dana" 
        subtitle="Menu pengajuan anggaran belanja operasional (buku, seragam, sarpras) dengan persetujuan bertingkat."
        badge="ANGGARAN & PENGAJUAN DANA"
        badgeVariant="emerald"
        icon="banknote"
    >
        @if ($userRole === 'finance' || $userRole === 'super_admin')
            <x-slot:actions>
                <x-button variant="primary" size="md" icon="plus" wire:click="openModal">
                    Buat Pengajuan Dana
                </x-button>
            </x-slot:actions>
        @endif
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengajuan Anggaran & Pencairan Dana"
        :steps="[
            ['title' => 'Buat Pengajuan', 'desc' => 'Klik Buat Pengajuan Dana untuk mengisi judul proposal, bidang pengaju, rincian biaya, serta target realisasi.'],
            ['title' => 'Verifikasi & Approval', 'desc' => 'Staf Finance, Pengawas, dan Kepala Yayasan akan meninjau kelayakan anggaran sebelum persetujuan diberikan.'],
            ['title' => 'Pencairan Dana', 'desc' => 'Proposal yang disetujui dapat dicairkan oleh Finance dan tercatat otomatis pada buku kas keluar.']
        ]"
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Main Content Table Panel (Full Width) -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <!-- Filter & Search Controls -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="max-w-md w-full">
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari judul atau nomor pengajuan..." />
            </div>

            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-stone-600 uppercase tracking-wider shrink-0">Status:</span>
                <select wire:model.live="filterStatus" class="px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="semua">Semua Status</option>
                    <option value="menunggu_koordinator">Menunggu Pengawas / Koordinator</option>
                    <option value="menunggu_kepala_yayasan">Menunggu Kepala Yayasan</option>
                    <option value="disetujui">Disetujui (Siap Realisasi)</option>
                    <option value="direalisasi">Direalisasi (Selesai)</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <x-table loadingTarget="search, filterStatus, page">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="w-36">No. Pengajuan</x-table.th>
                    <x-table.th class="min-w-[200px]">Judul & Kategori</x-table.th>
                    <x-table.th align="right" class="w-44">Jumlah Anggaran</x-table.th>
                    <x-table.th align="center" class="w-36">Pemohon</x-table.th>
                    <x-table.th align="center" class="w-48">Status Approval</x-table.th>
                    <x-table.th align="center" class="w-44">Aksi / Persetujuan</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($pengajuans as $item)
                    <tr class="hover:bg-emerald-50/40 transition">
                        <!-- No. Pengajuan -->
                        <td class="p-3.5 border-r border-stone-200">
                            <span class="font-mono font-bold text-stone-900 block text-xs">{{ $item->no_pengajuan }}</span>
                            <span class="text-[10px] text-stone-400 font-medium">{{ date('d M Y', strtotime($item->created_at)) }}</span>
                        </td>

                        <!-- Judul & Kategori -->
                        <td class="p-3.5 border-r border-stone-200">
                            <span class="font-bold text-stone-900 text-xs block" title="{{ $item->judul }}">{{ $item->judul }}</span>
                            <x-badge variant="stone" size="xs" class="mt-1">{{ $item->kategori }}</x-badge>
                            @if ($item->keterangan)
                                <p class="text-[11px] text-stone-500 font-medium mt-1 line-clamp-1" title="{{ $item->keterangan }}">{{ $item->keterangan }}</p>
                            @endif
                        </td>

                        <!-- Jumlah -->
                        <td class="p-3.5 text-right border-r border-stone-200">
                            <span class="font-black text-stone-900 text-sm block">
                                Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] font-bold {{ $item->jumlah > 1000000 ? 'text-purple-600' : 'text-stone-400' }} block">
                                {{ $item->jumlah > 1000000 ? 'Tier > Rp 1 Juta' : 'Tier ≤ Rp 1 Juta' }}
                            </span>
                        </td>

                        <!-- Pemohon -->
                        <td class="p-3.5 text-center text-xs font-semibold text-stone-700 border-r border-stone-200">
                            {{ $item->pemohon->nama ?? '-' }}
                        </td>

                        <!-- Status -->
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if ($item->status === 'menunggu_koordinator' || $item->status === 'menunggu_pengawas')
                                <x-badge variant="amber" size="xs" :dot="true">Menunggu Pengawas</x-badge>
                            @elseif ($item->status === 'menunggu_kepala_yayasan')
                                <x-badge variant="purple" size="xs" :dot="true">Menunggu Yayasan</x-badge>
                            @elseif ($item->status === 'disetujui')
                                <x-badge variant="emerald" size="xs" :dot="true">Disetujui (Siap Cair)</x-badge>
                            @elseif ($item->status === 'direalisasi')
                                <x-badge variant="sky" size="xs" :dot="true">Direalisasi (Selesai)</x-badge>
                            @elseif ($item->status === 'ditolak')
                                <x-badge variant="rose" size="xs" :dot="true">Ditolak</x-badge>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="p-3.5 text-center">
                            <div class="flex items-center justify-center gap-1.5 flex-wrap">
                                <!-- Pengawas / Super Admin Approval Action -->
                                @if (in_array($item->status, ['menunggu_koordinator', 'menunggu_pengawas']) && in_array($userRole, ['pengawas', 'koordinator', 'super_admin']))
                                    <x-button variant="primary" size="xs" icon="check" wire:click="approveByKoordinator({{ $item->id }})">
                                        Setujui
                                    </x-button>
                                    <x-button variant="danger" size="xs" icon="x" wire:click="openRejectModal({{ $item->id }})" />
                                @endif

                                <!-- Kepala Yayasan Approval Action -->
                                @if ($item->status === 'menunggu_kepala_yayasan' && in_array($userRole, ['kepala-sekolah', 'super_admin']))
                                    <x-button variant="primary" size="xs" icon="check" wire:click="approveByKepalaYayasan({{ $item->id }})">
                                        Acc Yayasan
                                    </x-button>
                                    <x-button variant="danger" size="xs" icon="x" wire:click="openRejectModal({{ $item->id }})" />
                                @endif

                                <!-- Finance Realisasi Action -->
                                @if ($item->status === 'disetujui' && in_array($userRole, ['finance', 'super_admin']))
                                    <x-button variant="primary" size="xs" icon="banknote" wire:click="realisasikanDana({{ $item->id }})">
                                        Cairkan Dana
                                    </x-button>
                                @endif

                                @if ($item->status === 'direalisasi')
                                    <span class="text-[10px] text-stone-500 font-semibold italic">Dicairkan {{ \Carbon\Carbon::parse($item->realisasi_pada)->translatedFormat('d M Y') }}</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-table.empty :colspan="6" title="Belum ada pengajuan penggunaan dana" message="Klik tombol Buat Pengajuan Dana untuk mengajukan anggaran belanja." />
                @endforelse
            </tbody>
        </x-table>

        <!-- Pagination -->
        <div class="pt-2">
            {{ $pengajuans->links() }}
        </div>
    </div>

    <!-- Floating Card Form Buat Pengajuan -->
    <x-floating-card 
        :show="$showModal" 
        title="Form Pengajuan Penggunaan Dana" 
        subtitle="Ajukan anggaran belanja operasional atau sarana prasarana sekolah."
        badge="PROPOSAL ANGGARAN"
        badgeVariant="emerald"
        icon="plus-circle"
        maxWidth="max-w-lg"
        closeAction="closeModal"
    >
        <form wire:submit.prevent="createPengajuan" class="space-y-4">
            <!-- Judul -->
            <x-input 
                label="Judul Pengajuan" 
                name="judul" 
                wire:model="judul" 
                placeholder="Contoh: Pembelian Buku Paket Matematika Kelas 7" 
                required 
            />

            <!-- Kategori -->
            <x-select 
                label="Kategori Pengajuan" 
                name="kategori" 
                wire:model="kategori" 
                :options="array_combine($kategoriOptions, $kategoriOptions)" 
                required 
            />

            <!-- Jumlah Nominal -->
            <x-input-currency 
                label="Nominal Anggaran (Rp)" 
                name="jumlah" 
                wire:model.live="jumlah" 
                placeholder="Contoh: 1.500.000" 
                hint="{{ $jumlah > 1000000 ? 'Memerlukan persetujuan 2 Tahap (Pengawas & Kepala Yayasan).' : ($jumlah > 0 ? 'Memerlukan persetujuan Pengawas saja.' : '') }}"
                required 
            />

            <!-- Target Realisasi -->
            <x-input 
                type="date" 
                label="Target Tanggal Realisasi" 
                name="target_realisasi" 
                wire:model="target_realisasi" 
            />

            <!-- Keterangan -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider">Keterangan / Rincian Kebutuhan</label>
                <textarea wire:model="keterangan" rows="3" placeholder="Jelaskan rincian dan alokasi kebutuhan belanja dana..." class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs resize-none"></textarea>
                @error('keterangan') <span class="text-rose-600 text-[11px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-stone-200">
                <x-button variant="secondary" size="md" wire:click="closeModal">
                    Batal
                </x-button>
                <x-button variant="primary" size="md" type="submit" loadingTarget="createPengajuan">
                    Kirim Pengajuan
                </x-button>
            </div>
        </form>
    </x-floating-card>

    <!-- Floating Card Modal Penolakan -->
    @if ($showRejectModal)
        <x-floating-card 
            :show="true" 
            title="Alasan Penolakan Pengajuan" 
            subtitle="Berikan catatan alasan penolakan proposal anggaran ini."
            badge="PENOLAKAN ANGGARAN"
            badgeVariant="rose"
            icon="x-circle"
            maxWidth="max-w-md"
            closeAction="$set('showRejectModal', false)"
        >
            <textarea wire:model="catatan_penolakan" rows="3" placeholder="Masukkan alasan penolakan pengajuan dana..." class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-rose-600 shadow-2xs resize-none"></textarea>
            <div class="flex justify-end gap-2 pt-2 border-t border-stone-200">
                <x-button variant="secondary" size="md" wire:click="$set('showRejectModal', false)">
                    Batal
                </x-button>
                <x-button variant="danger-solid" size="md" wire:click="rejectPengajuan" loadingTarget="rejectPengajuan">
                    Konfirmasi Tolak
                </x-button>
            </div>
        </x-floating-card>
    @endif
</div>
