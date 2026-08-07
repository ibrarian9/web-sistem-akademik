<div class="space-y-6 font-sans">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Kenaikan Kelas & Diskresi Tata Usaha"
        :steps="[
            ['title' => 'Kenaikan Otomatis', 'desc' => 'Secara bawaan sistem, seluruh siswa aktif tercentang dan diproses Naik Kelas / Lulus.'],
            ['title' => 'Diskresi Manual TU', 'desc' => 'Gunakan tombol Tandai Tinggal Kelas untuk menahan siswa tertentu jika dinilai belum layak naik.'],
            ['title' => 'Eksekusi Kenaikan', 'desc' => 'Siswa naik kelas akan berpindah rombel, sedangkan siswa tinggal kelas akan dipertahankan di rombel asal.']
        ]"
        notes="Sistem menganut kebijakan otomatis naik kelas. Namun, Staf Tata Usaha memiliki wewenang manual untuk menetapkan siswa tertentu Tinggal Kelas."
    />

    <!-- Hero Header Card -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                KENAIKAN KELAS &amp; KELULUSAN
            </span>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Kenaikan Kelas &amp; Kelulusan Massal</h1>
            <p class="text-xs text-stone-600 font-semibold mt-1">Proses pemindahan kelas atau kelulusan siswa secara otomatis per rombongan belajar.</p>
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
        <h3 class="text-xs font-extrabold text-stone-900 uppercase tracking-wider flex items-center gap-2 border-b border-stone-200 pb-3">
            <x-lucide-arrow-right-left class="w-4 h-4 text-emerald-700" />
            <span>Pengaturan Rombel &amp; Aksi Tujuan</span>
        </h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- 1. Kelas Asal -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">1. Pilih Kelas Asal <span class="text-rose-600">*</span></label>
                <select wire:model.live="kelasAsalId" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                    <option value="">-- Pilih Kelas Asal --</option>
                    @foreach ($kelases as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }} (Tingkat {{ $k->tingkat }})</option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Jenis Aksi -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">2. Jenis Aksi <span class="text-rose-600">*</span></label>
                <select wire:model.live="aksiTujuan" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                    <option value="naik_kelas">Kenaikan Kelas (Pindah Rombel)</option>
                    <option value="lulus_alumni">Kelulusan (Pindah ke Data Alumni)</option>
                </select>
            </div>

            <!-- 3. Kelas Tujuan (jika naik_kelas) -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">3. Target Tujuan</label>
                @if ($aksiTujuan === 'naik_kelas')
                    <select wire:model="kelasTujuanId" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                        <option value="">-- Pilih Kelas Tujuan --</option>
                        @foreach ($kelases as $k)
                            @if ($k->id != $kelasAsalId)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }} (Tingkat {{ $k->tingkat }})</option>
                            @endif
                        @endforeach
                    </select>
                @else
                    <div class="px-3.5 py-2 bg-amber-50 border border-amber-300 rounded-xl text-amber-900 text-xs font-bold flex items-center gap-2">
                        <x-lucide-check-circle class="w-4 h-4 text-amber-700 shrink-0" />
                        <span>Status Siswa ➔ ALUMNI (Tahun Lulus {{ date('Y') }})</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-t border-stone-200 pt-4 gap-3">
            <div class="text-xs text-stone-600 font-medium space-x-2">
                <span>Terpilih: <strong class="text-stone-900 font-extrabold">{{ count($selectedSiswa) }}</strong> dari {{ count($students) }} siswa</span>
                @if ($aksiTujuan === 'naik_kelas')
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
            <button type="button" wire:click="prosesKenaikan" data-confirm="Apakah Anda yakin ingin memproses aksi kenaikan/kelulusan untuk siswa terpilih ini?"
                class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow-md inline-flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                @if(count($selectedSiswa) === 0) disabled @endif>
                <x-lucide-check class="w-4 h-4" />
                <span>Proses {{ $aksiTujuan === 'naik_kelas' ? 'Kenaikan Kelas' : 'Kelulusan' }}</span>
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
                        <th class="p-3.5 border-r border-emerald-700 min-w-[180px]">Nama Siswa</th>
                        <th class="p-3.5 border-r border-emerald-700 w-32">NIS / NISN</th>
                        <th class="p-3.5 border-r border-emerald-700 w-28">Jenis Kelamin</th>
                        <th class="p-3.5 border-r border-emerald-700 w-28">Kelas Asal</th>
                        <th class="p-3.5 border-r border-emerald-700 w-36 text-center">Keputusan</th>
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
                                {{ strtoupper($siswa->user->nama ?? '-') }}
                            </td>
                            <td class="p-3.5 border-r border-stone-200 font-bold text-stone-700">
                                {{ $siswa->nis ?? '-' }} / {{ $siswa->nisn ?? '-' }}
                            </td>
                            <td class="p-3.5 border-r border-stone-200 font-semibold text-stone-600">
                                {{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </td>
                            <td class="p-3.5 border-r border-stone-200 font-extrabold text-emerald-900">
                                {{ $siswa->kelas->nama_kelas ?? '-' }}
                            </td>
                            <td class="p-3.5 text-center border-r border-stone-200">
                                @if ($aksiTujuan === 'lulus_alumni')
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
                                @if ($aksiTujuan === 'naik_kelas')
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
                            <td colspan="7" class="p-8 text-center text-stone-500 font-semibold italic">
                                Tidak ada siswa aktif yang ditemukan pada kelas asal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
