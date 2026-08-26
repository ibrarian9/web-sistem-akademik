<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Kenaikan Kelas & Kelulusan SD Tahfizh" 
        subtitle="Pemindahan Kelas Akademik Rombel & Plotting Kelompok Halaqah Tahfizh Lintas Kelas."
        badge="DUAL KENAIKAN KELAS & HALAQAH"
        badgeVariant="emerald"
        icon="arrow-right-left"
    >
        <x-slot:actions>
            <div class="flex items-center gap-1.5 bg-stone-100 border border-stone-200 p-1.5 rounded-2xl shadow-2xs">
                <x-button type="button" :variant="$tipeKenaikan === 'akademik' ? 'primary' : 'ghost'" size="sm" icon="graduation-cap" wire:click="$set('tipeKenaikan', 'akademik')">
                    1. Kelas Akademik (1-6 SD)
                </x-button>
                <x-button type="button" :variant="$tipeKenaikan === 'tahfidz' ? 'primary' : 'ghost'" size="sm" icon="award" wire:click="$set('tipeKenaikan', 'tahfidz')">
                    2. Halaqah Tahfizh (Hafalan)
                </x-button>
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Dual Kenaikan Kelas (Akademik & Halaqah Tahfizh)"
        :steps="[
            ['title' => 'Pilih Tipe Kenaikan', 'desc' => 'Gunakan tombol di header untuk memproses Kenaikan Kelas Akademik (Jenjang SD 1-6) atau Plotting Halaqah Tahfizh Lintas Kelas.'],
            ['title' => 'Cek Evaluasi Tahfizh', 'desc' => 'Tabel menampilkan status capaian hafalan santri sebagai bahan pertimbangan diskresi Tata Usaha.'],
            ['title' => 'Diskresi Manual TU', 'desc' => 'Gunakan tombol Tandai Tinggal Kelas jika siswa dinilai belum memenuhi kriteria kenaikan akademik/tahfizh.']
        ]"
        notes="Pada SD Tahfizh, murid wajib memiliki 2 kelas: Kelas Akademik (Jenjang SD) dan Halaqah Tahfizh (Kelompok Hafalan Ustaz)."
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Control Panel / Settings Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 space-y-4 shadow-xs">
        <div class="flex items-center justify-between border-b border-stone-200 pb-3">
            <h3 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2">
                <x-lucide-arrow-right-left class="w-4 h-4 text-emerald-700" />
                <span>
                    @if($tipeKenaikan === 'tahfidz') Pengaturan Pemindahan Halaqah Tahfizh Lintas Kelas
                    @else Pengaturan Rombel Akademik & Kenaikan Kelas
                    @endif
                </span>
            </h3>
            <x-badge variant="emerald" size="xs">
                Mode Active: {{ strtoupper($tipeKenaikan) }}
            </x-badge>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
            <!-- 1. Kelas / Halaqah Asal -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">
                    1. {{ $tipeKenaikan === 'tahfidz' ? 'Pilih Halaqah Tahfizh Asal' : 'Pilih Kelas Akademik Asal' }} <span class="text-rose-600">*</span>
                </label>
                <select wire:model.live="kelasAsalId" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">-- Pilih {{ $tipeKenaikan === 'tahfidz' ? 'Halaqah Tahfizh' : 'Kelas Akademik' }} Asal --</option>
                    @foreach ($kelasesAsal as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }} {{ strtolower($k->jenis_kelas) === 'tahfidz' ? '(Halaqah Tahfizh)' : '(Kelas Akademik)' }}</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Jenis Aksi -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">2. Jenis Aksi <span class="text-rose-600">*</span></label>
                @if($tipeKenaikan === 'tahfidz')
                    <div class="px-3.5 py-2.5 bg-emerald-50 border border-emerald-300 rounded-xl text-emerald-900 text-xs font-bold flex items-center gap-2">
                        <x-lucide-award class="w-4 h-4 text-emerald-700 shrink-0" />
                        <span>Plotting / Rotasi Group Halaqah Tahfizh</span>
                    </div>
                @else
                    <select wire:model.live="aksiTujuan" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="naik_kelas">Kenaikan Kelas Akademik (Pindah Rombel)</option>
                        <option value="lulus_alumni">Kelulusan (Pindah ke Data Alumni)</option>
                    </select>
                @endif
            </div>

            <!-- 3. Target Tujuan -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">
                    3. Target {{ $tipeKenaikan === 'tahfidz' ? 'Halaqah Tujuan' : 'Kelas Tujuan' }}
                </label>
                @if ($tipeKenaikan === 'tahfidz' || $aksiTujuan === 'naik_kelas')
                    <select wire:model="kelasTujuanId" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="">-- Pilih {{ $tipeKenaikan === 'tahfidz' ? 'Halaqah' : 'Kelas' }} Tujuan --</option>
                        @foreach ($kelasesTujuan as $k)
                            @if ($k->id != $kelasAsalId)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }} {{ strtolower($k->jenis_kelas) === 'tahfidz' ? '(Halaqah Tahfizh)' : '(Kelas Akademik)' }}</option>
                            @endif
                        @endforeach
                    </select>
                @else
                    <div class="px-3.5 py-2.5 bg-purple-50 border border-purple-300 rounded-xl text-purple-900 text-xs font-bold flex items-center gap-2">
                        <x-lucide-check-circle class="w-4 h-4 text-purple-700 shrink-0" />
                        <span>Status Siswa ➔ ALUMNI (Tahun Lulus {{ date('Y') }})</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-t border-stone-200 pt-4 gap-3">
            <div class="text-xs text-stone-600 font-medium space-x-2">
                <span>Terpilih: <strong class="text-stone-900 font-extrabold">{{ count($selectedSiswa) }}</strong> dari {{ count($students) }} santri/siswa</span>
                @if ($tipeKenaikan === 'akademik' && $aksiTujuan === 'naik_kelas')
                    <x-badge variant="emerald" size="xs">
                        Naik: {{ count($selectedSiswa) - count($siswaTinggalKelas) }}
                    </x-badge>
                    @if (count($siswaTinggalKelas) > 0)
                        <x-badge variant="rose" size="xs">
                            Tinggal Kelas: {{ count($siswaTinggalKelas) }}
                        </x-badge>
                    @endif
                @endif
            </div>
            <x-button type="button" variant="primary" size="md" icon="check" wire:click="prosesKenaikan" :disabled="count($selectedSiswa) === 0" loadingTarget="prosesKenaikan" data-confirm="Apakah Anda yakin ingin memproses aksi pemindahan/kenaikan untuk santri/siswa terpilih ini?">
                Proses {{ $tipeKenaikan === 'tahfidz' ? 'Pemindahan Halaqah Tahfizh' : ($aksiTujuan === 'naik_kelas' ? 'Kenaikan Kelas' : 'Kelulusan') }}
            </x-button>
        </div>
    </div>

    <!-- Student Table Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <x-table loadingTarget="prosesKenaikan, kelasAsalId, tipeKenaikan">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th align="center" class="w-12">
                        <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 rounded text-emerald-700 border-stone-300 focus:ring-emerald-600 cursor-pointer" />
                    </x-table.th>
                    <x-table.th class="min-w-[180px]">Nama Santri / Siswa</x-table.th>
                    <x-table.th class="min-w-[160px]">Kelas Akademik & Halaqah</x-table.th>
                    <x-table.th class="min-w-[160px]">Status Evaluasi Tahfizh</x-table.th>
                    <x-table.th align="center" class="w-36">Keputusan</x-table.th>
                    <x-table.th align="center" class="min-w-[150px]">Diskresi TU</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($students as $siswa)
                    @php
                        $isTinggal = in_array((string)$siswa->id, $siswaTinggalKelas);
                    @endphp
                    <tr class="hover:bg-stone-50 transition {{ $isTinggal ? 'bg-rose-50/40' : '' }}">
                        <td class="p-3.5 text-center border-r border-stone-200">
                            <input type="checkbox" wire:model.live="selectedSiswa" value="{{ $siswa->id }}"
                                class="w-4 h-4 rounded text-emerald-700 border-stone-300 focus:ring-emerald-600 cursor-pointer" />
                        </td>
                        <td class="p-3.5 border-r border-stone-200 font-extrabold text-stone-900">
                            <div>{{ strtoupper($siswa->user->nama ?? '-') }}</div>
                            <div class="text-[10px] text-stone-500 font-medium mt-0.5">NISN: {{ $siswa->nisn ?? '-' }}</div>
                        </td>
                        <td class="p-3.5 border-r border-stone-200 font-bold space-y-1">
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] text-stone-500 font-semibold uppercase">Akademik:</span>
                                <x-badge variant="stone" size="xs">{{ $siswa->kelas->nama_kelas ?? 'Belum Set' }}</x-badge>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <span class="text-[10px] text-stone-500 font-semibold uppercase">Tahfizh:</span>
                                <x-badge variant="emerald" size="xs">{{ $siswa->kelasTahfidz->nama_kelas ?? ($siswa->kelas->guruTahfidz->user->nama ?? 'Belum Plotting') }}</x-badge>
                            </div>
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="space-y-1">
                                <x-badge variant="emerald" size="xs">✓ Target Tahfizh Terpenuhi</x-badge>
                                <div class="text-[10px] text-stone-500 font-medium">Mutaba'ah & Tajwid Lancar</div>
                            </div>
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if ($tipeKenaikan === 'tahfidz')
                                <x-badge variant="emerald" size="xs">ROTASI HALAQAH</x-badge>
                            @elseif ($aksiTujuan === 'lulus_alumni')
                                <x-badge variant="purple" size="xs">LULUS ALUMNI</x-badge>
                            @elseif ($isTinggal)
                                <x-badge variant="rose" size="xs">TINGGAL KELAS</x-badge>
                            @else
                                <x-badge variant="emerald" size="xs">NAIK KELAS</x-badge>
                            @endif
                        </td>
                        <td class="p-3.5 text-center">
                            @if ($tipeKenaikan === 'akademik' && $aksiTujuan === 'naik_kelas')
                                @if ($isTinggal)
                                    <x-button type="button" variant="secondary" size="xs" icon="check" wire:click="toggleTinggalKelas({{ $siswa->id }})">
                                        Batalkan (Naikkan)
                                    </x-button>
                                @else
                                    <x-button type="button" variant="danger" size="xs" icon="x" wire:click="toggleTinggalKelas({{ $siswa->id }})">
                                        Tandai Tinggal Kelas
                                    </x-button>
                                @endif
                            @else
                                <span class="text-stone-400 font-bold text-xs">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-stone-400">
                            <x-table.empty title="Tidak ada santri/siswa aktif yang ditemukan" subtitle="Pilih {{ $tipeKenaikan === 'tahfidz' ? 'Halaqah Tahfizh' : 'Kelas Akademik' }} asal di atas." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>
    </div>
</div>
