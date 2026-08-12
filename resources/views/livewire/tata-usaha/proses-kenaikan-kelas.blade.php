<div class="space-y-6 font-sans">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Dual Kenaikan Kelas (Akademik & Halaqah Tahfizh)"
        :steps="[
            ['title' => 'Pilih Tipe Kenaikan', 'desc' => 'Gunakan tab di atas untuk memproses Kenaikan Kelas Akademik (Jenjang SD 1-6) atau Plotting Halaqah Tahfizh Lintas Kelas.'],
            ['title' => 'Cek Evaluasi Tahfizh', 'desc' => 'Tabel menampilkan status capaian hafalan santri sebagai bahan pertimbangan diskresi Tata Usaha.'],
            ['title' => 'Diskresi Manual TU', 'desc' => 'Gunakan tombol Tandai Tinggal Kelas jika siswa dinilai belum memenuhi kriteria kenaikan akademik/tahfizh.']
        ]"
        notes="Pada SD Tahfizh, murid wajib memiliki 2 kelas: Kelas Akademik (Jenjang SD) dan Halaqah Tahfizh (Kelompok Hafalan Ustaz)."
    />

    <!-- Hero Header Card -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                DUAL KENAIKAN KELAS &amp; HALAQAH TAHFIZH
            </span>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Kenaikan Kelas &amp; Kelulusan SD Tahfizh</h1>
            <p class="text-xs text-stone-600 font-semibold mt-1">Pemindahan Kelas Akademik Rombel &amp; Plotting Kelompok Halaqah Tahfizh Lintas Kelas.</p>
        </div>

        <!-- Mode Switcher Tabs -->
        <div class="flex items-center gap-1.5 bg-stone-100 border border-stone-200 p-1.5 rounded-2xl shrink-0 shadow-xs">
            <button wire:click="$set('tipeKenaikan', 'akademik')" 
                class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap {{ $tipeKenaikan === 'akademik' ? 'bg-emerald-700 text-white shadow-sm' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-200/60' }}">
                <x-lucide-graduation-cap class="w-4 h-4" />
                <span>1. Kelas Akademik (1-6 SD)</span>
            </button>
            <button wire:click="$set('tipeKenaikan', 'tahfidz')" 
                class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap {{ $tipeKenaikan === 'tahfidz' ? 'bg-emerald-700 text-white shadow-sm' : 'text-stone-600 hover:text-stone-900 hover:bg-stone-200/60' }}">
                <x-lucide-award class="w-4 h-4" />
                <span>2. Halaqah Tahfizh (Hafalan)</span>
            </button>
        </div>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif
    @if (session()->has('error'))
        <x-alert-banner type="error" :message="session('error')" />
    @endif

    <!-- Control Panel / Settings Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 space-y-4 shadow-sm">
        <div class="flex items-center justify-between border-b border-stone-200 pb-3">
            <h3 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2">
                <x-lucide-arrow-right-left class="w-4 h-4 text-emerald-700" />
                <span>
                    @if($tipeKenaikan === 'tahfidz') Pengaturan Pemindahan Halaqah Tahfizh Lintas Kelas
                    @else Pengaturan Rombel Akademik &amp; Kenaikan Kelas
                    @endif
                </span>
            </h3>
            <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-full text-[10px] font-bold uppercase">
                Mode Active: {{ strtoupper($tipeKenaikan) }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- 1. Kelas / Halaqah Asal -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">
                    1. {{ $tipeKenaikan === 'tahfidz' ? 'Pilih Halaqah Tahfizh Asal' : 'Pilih Kelas Akademik Asal' }} <span class="text-rose-600">*</span>
                </label>
                <select wire:model.live="kelasAsalId" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
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
                    <select wire:model.live="aksiTujuan" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
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
                    <select wire:model="kelasTujuanId" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
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
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-900 border border-emerald-300 text-xs font-bold">
                        Naik: {{ count($selectedSiswa) - count($siswaTinggalKelas) }}
                    </span>
                    @if (count($siswaTinggalKelas) > 0)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-900 border border-rose-300 text-xs font-bold">
                            Tinggal Kelas: {{ count($siswaTinggalKelas) }}
                        </span>
                    @endif
                @endif
            </div>
            <button type="button" wire:click="prosesKenaikan" data-confirm="Apakah Anda yakin ingin memproses aksi pemindahan/kenaikan untuk santri/siswa terpilih ini?"
                class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-md inline-flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                @if(count($selectedSiswa) === 0) disabled @endif>
                <x-lucide-check class="w-4 h-4" />
                <span>Proses {{ $tipeKenaikan === 'tahfidz' ? 'Pemindahan Halaqah Tahfizh' : ($aksiTujuan === 'naik_kelas' ? 'Kenaikan Kelas' : 'Kelulusan') }}</span>
            </button>
        </div>
    </div>

    <!-- Student Table Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs text-stone-800">
                <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                    <tr>
                        <th class="p-3.5 text-center w-12 border-r border-emerald-700">
                            <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 rounded text-emerald-700 border-stone-300 focus:ring-emerald-600 cursor-pointer" />
                        </th>
                        <th class="p-3.5 border-r border-emerald-700 min-w-[180px]">Nama Santri / Siswa</th>
                        <th class="p-3.5 border-r border-emerald-700 min-w-[160px]">Kelas Akademik &amp; Halaqah</th>
                        <th class="p-3.5 border-r border-emerald-700 min-w-[160px]">Status Evaluasi Tahfizh</th>
                        <th class="p-3.5 border-r border-emerald-700 w-32 text-center">Keputusan</th>
                        <th class="p-3.5 text-center min-w-[140px]">Diskresi TU</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse ($students as $siswa)
                        @php
                            $isTinggal = in_array((string)$siswa->id, $siswaTinggalKelas);
                        @endphp
                        <tr class="hover:bg-emerald-50/50 transition {{ $isTinggal ? 'bg-rose-50/40' : '' }}">
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
                                    <span class="px-2 py-0.5 bg-stone-100 text-stone-900 border border-stone-300 rounded font-extrabold text-[10px]">
                                        {{ $siswa->kelas->nama_kelas ?? 'Belum Set' }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-[10px] text-stone-500 font-semibold uppercase">Tahfizh:</span>
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded font-extrabold text-[10px]">
                                        {{ $siswa->kelasTahfidz->nama_kelas ?? ($siswa->kelas->guruTahfidz->user->nama ?? 'Belum Plotting') }}
                                    </span>
                                </div>
                            </td>
                            <td class="p-3.5 border-r border-stone-200">
                                <div class="space-y-1">
                                    <span class="px-2.5 py-1 bg-emerald-100 text-emerald-900 border border-emerald-300 text-[10px] font-extrabold rounded-md inline-flex items-center gap-1">
                                        <span>✓ Target Tahfizh Terpenuhi</span>
                                    </span>
                                    <div class="text-[10px] text-stone-500 font-medium">Mutaba'ah &amp; Tajwid Lancar</div>
                                </div>
                            </td>
                            <td class="p-3.5 text-center border-r border-stone-200">
                                @if ($tipeKenaikan === 'tahfidz')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-900 border border-emerald-300 uppercase inline-block">
                                        ROTASI HALAQAH
                                    </span>
                                @elseif ($aksiTujuan === 'lulus_alumni')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-purple-100 text-purple-900 border border-purple-300 uppercase inline-block">
                                        LULUS ALUMNI
                                    </span>
                                @elseif ($isTinggal)
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-900 border border-rose-300 uppercase inline-block">
                                        TINGGAL KELAS
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-900 border border-emerald-300 uppercase inline-block">
                                        NAIK KELAS
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center">
                                @if ($tipeKenaikan === 'akademik' && $aksiTujuan === 'naik_kelas')
                                    @if ($isTinggal)
                                        <button wire:click="toggleTinggalKelas({{ $siswa->id }})" 
                                            class="px-2.5 py-1 bg-stone-100 hover:bg-stone-200 text-stone-800 rounded-lg text-xs font-bold border border-stone-300 transition shadow-xs inline-flex items-center gap-1">
                                            <x-lucide-check class="w-3.5 h-3.5 text-emerald-700" />
                                            <span>Batalkan (Naikkan)</span>
                                        </button>
                                    @else
                                        <button wire:click="toggleTinggalKelas({{ $siswa->id }})" 
                                            class="px-2.5 py-1 bg-rose-100 hover:bg-rose-200 text-rose-800 rounded-lg text-xs font-bold border border-rose-300 transition shadow-xs inline-flex items-center gap-1">
                                            <x-lucide-x class="w-3.5 h-3.5 text-rose-600" />
                                            <span>Tandai Tinggal Kelas</span>
                                        </button>
                                    @endif
                                @else
                                    <span class="text-stone-400 font-bold text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-stone-500 font-semibold italic">
                                Tidak ada santri/siswa aktif yang ditemukan pada {{ $tipeKenaikan === 'tahfidz' ? 'Halaqah Tahfizh' : 'Kelas Akademik' }} asal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
