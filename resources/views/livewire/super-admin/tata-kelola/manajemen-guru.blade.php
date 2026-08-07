<div class="space-y-6 font-sans">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengelolaan Data Guru & Tenaga Pendidik"
        :steps="[
            ['title' => 'Input Profil & Kredensial', 'desc' => 'Klik + Tambah Guru Baru untuk mendaftarkan NIK, NIY, pendidikan terakhir, grade guru, serta status pernikahan.'],
            ['title' => 'Status Kepegawaian & Grade', 'desc' => 'Tentukan status kepegawaian (PNS/GTT/Honorer) & Grade Guru untuk penyesuaian penggajian & jenjang karir.'],
            ['title' => 'Manajemen Status Kerja', 'desc' => 'Ubah status mengajar menjadi Nonaktif jika guru yang bersangkutan sedang mutasi, berhenti, atau cuti.']
        ]"
        notes="NIY (Nomor Induk Yayasan) & NIK diisikan demi keperluan administrasi yayasan, dapodik, dan penggajian."
    />

    <!-- Hero Header Card -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-full text-xs font-bold uppercase tracking-wider inline-block mb-1">
                MANAJEMEN GURU &amp; PEGAWAI
            </span>
            <h1 class="text-2xl font-extrabold text-stone-900 tracking-tight">Kelola Data Kepegawaian Guru</h1>
            <p class="text-xs text-stone-600 font-semibold mt-1">Pencatatan NIY, NIK, pendidikan, grade guru, status pernikahan, dan kredensial login.</p>
        </div>
        <button type="button" wire:click.prevent="openCreate" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold px-5 py-2.5 rounded-xl text-xs transition shadow-sm flex items-center gap-2">
            <x-lucide-plus class="w-4 h-4" />
            <span>+ Tambah Guru Baru</span>
        </button>
    </div>

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <!-- Content Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-sm space-y-4">
        <!-- Toolbar & Filter -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="relative max-w-md w-full">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-stone-400">
                    <x-lucide-search class="w-4 h-4" />
                </span>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari NIY, NIK, nama, grade, atau pendidikan..."
                    class="w-full pl-9 pr-4 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 placeholder-stone-400 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-xs" />
            </div>
            
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-stone-600">Tampilkan:</span>
                <select wire:model.live="perPage" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-1.5 focus:ring-2 focus:ring-emerald-600 shadow-xs">
                    <option value="10">10 Baris</option>
                    <option value="25">25 Baris</option>
                    <option value="50">50 Baris</option>
                </select>
            </div>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs text-stone-800">
                <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                    <tr>
                        <th class="p-3.5 border-r border-emerald-700 w-32">NIY / NIK</th>
                        <th class="p-3.5 border-r border-emerald-700 min-w-[180px]">Nama Guru</th>
                        <th class="p-3.5 border-r border-emerald-700 min-w-[140px]">Pendidikan &amp; Grade</th>
                        <th class="p-3.5 border-r border-emerald-700 w-32 text-center">Status Nikah</th>
                        <th class="p-3.5 border-r border-emerald-700 min-w-[150px]">TTL &amp; Tgl Masuk</th>
                        <th class="p-3.5 border-r border-emerald-700 w-28">Status Pegawai</th>
                        <th class="p-3.5 border-r border-emerald-700 min-w-[120px]">No. HP</th>
                        <th class="p-3.5 border-r border-emerald-700 w-24 text-center">Status Kerja</th>
                        <th class="p-3.5 text-center min-w-[130px]">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-200 bg-white">
                    @forelse ($gurus as $guru)
                        @php
                            $statusNikahLabel = match($guru->status_pernikahan) {
                                'menikah' => ['label' => 'Menikah', 'color' => 'bg-emerald-100 text-emerald-900 border-emerald-300'],
                                'cerai_hidup' => ['label' => 'Cerai Hidup', 'color' => 'bg-amber-100 text-amber-900 border-amber-300'],
                                'cerai_mati' => ['label' => 'Cerai Mati', 'color' => 'bg-rose-100 text-rose-900 border-rose-300'],
                                default => ['label' => 'Belum Menikah', 'color' => 'bg-stone-100 text-stone-700 border-stone-300'],
                            };
                        @endphp
                        <tr class="hover:bg-emerald-50/50 transition">
                            <td class="p-3.5 border-r border-stone-200">
                                <div class="font-bold text-stone-900">NIY: {{ $guru->niy ?: ($guru->nip ?: '-') }}</div>
                                <div class="text-[10px] text-stone-500 font-medium">NIK: {{ $guru->nik ?: '-' }}</div>
                            </td>
                            <td class="p-3.5 border-r border-stone-200">
                                <div class="font-extrabold text-stone-900 text-xs">{{ strtoupper($guru->user->nama ?? '-') }}</div>
                                <div class="text-[10px] text-stone-500 font-medium">Username: {{ $guru->user->username ?? '-' }}</div>
                            </td>
                            <td class="p-3.5 border-r border-stone-200">
                                <div class="font-bold text-emerald-950">{{ $guru->pendidikan ?: '-' }}</div>
                                @if($guru->grade_guru)
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-900 border border-emerald-300 rounded text-[10px] font-extrabold uppercase inline-block mt-0.5">
                                        {{ $guru->grade_guru }}
                                    </span>
                                @else
                                    <span class="text-stone-400 italic text-[10px]">- Grade belum set -</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center border-r border-stone-200">
                                <span class="px-2.5 py-1 border rounded-full font-bold text-[10px] uppercase inline-block {{ $statusNikahLabel['color'] }}">
                                    {{ $statusNikahLabel['label'] }}
                                </span>
                            </td>
                            <td class="p-3.5 border-r border-stone-200">
                                @if($guru->tempat_lahir || $guru->tanggal_lahir)
                                    <div class="font-bold text-stone-800">{{ $guru->tempat_lahir ?: '-' }}, {{ $guru->tanggal_lahir ? $guru->tanggal_lahir->format('d M Y') : '-' }}</div>
                                @endif
                                <div class="text-[10px] text-stone-500">Masuk: {{ $guru->tanggal_masuk ? $guru->tanggal_masuk->format('d M Y') : '-' }}</div>
                            </td>
                            <td class="p-3.5 text-stone-800 font-bold border-r border-stone-200 uppercase">{{ $guru->status_kepegawaian }}</td>
                            <td class="p-3.5 text-stone-700 font-medium border-r border-stone-200">{{ $guru->user->no_hp ?: '-' }}</td>
                            <td class="p-3.5 text-center border-r border-stone-200">
                                <x-status-badge :status="$guru->status_aktif ? 'aktif' : 'nonaktif'" />
                            </td>
                            <td class="p-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button type="button" wire:click.prevent="openEdit({{ $guru->id }})" class="px-2.5 py-1 bg-amber-100 hover:bg-amber-200 text-amber-900 rounded-lg font-bold text-xs border border-amber-300 transition shadow-xs flex items-center gap-1">
                                        <x-lucide-edit class="w-3.5 h-3.5 text-amber-700" />
                                        <span>Edit</span>
                                    </button>
                                    <button type="button" wire:click.prevent="delete({{ $guru->id }})" data-confirm="Apakah Anda yakin ingin menghapus data guru ini?" class="px-2.5 py-1 bg-rose-100 hover:bg-rose-200 text-rose-800 rounded-lg font-bold text-xs border border-rose-300 transition shadow-xs flex items-center gap-1">
                                        <x-lucide-trash-2 class="w-3.5 h-3.5 text-rose-600" />
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-stone-500 font-semibold italic">
                                Tidak ada data guru ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pt-2">
            {{ $gurus->links() }}
        </div>
    </div>

    <!-- Form Modal -->
    @if ($isFormOpen)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/60 backdrop-blur-xs p-4 overflow-y-auto">
            <div class="w-full max-w-2xl bg-white border border-stone-200 rounded-3xl shadow-2xl p-6 space-y-4 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-stone-200 pb-3">
                    <h3 class="text-sm font-extrabold text-emerald-950 uppercase tracking-wider flex items-center gap-2">
                        <span class="w-6 h-6 rounded-full bg-emerald-200 text-emerald-950 text-xs flex items-center justify-center font-black">★</span>
                        <span>{{ $guruId ? 'Edit Data Profil Guru' : 'Tambah Guru Baru' }}</span>
                    </h3>
                    <button type="button" wire:click="$set('isFormOpen', false)" class="p-1 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100 font-bold">✕</button>
                </div>

                <form wire:submit.prevent="save" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nama -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Nama Lengkap <span class="text-rose-600">*</span></label>
                            <input wire:model="nama" type="text" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="Ahmad Budi, S.Pd" />
                            @error('nama') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Username -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Username Login <span class="text-rose-600">*</span></label>
                            <input wire:model="username" type="text" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="budi_guru" />
                            @error('username') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Email -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Email (Opsional)</label>
                            <input wire:model="email" type="email" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="budi@sekolah.com" />
                            @error('email') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Password -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">{{ $guruId ? 'Ganti Password (Kosongkan jika tidak)' : 'Password Awal' }}</label>
                            <input wire:model="password" type="password" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="••••••" />
                            @error('password') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- NIY -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">NIY (Nomor Induk Yayasan)</label>
                            <input wire:model="niy" type="text" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="NIY.1987..." />
                            @error('niy') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- NIK -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">NIK (KTP 16 Digit)</label>
                            <input wire:model="nik" type="text" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="3404..." />
                            @error('nik') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Pendidikan Terakhir -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Pendidikan Terakhir</label>
                            <select wire:model="pendidikan" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                                <option value="">-- Pilih Pendidikan --</option>
                                <option value="SMA/MA">SMA/MA/Sederajat</option>
                                <option value="D3">D3 / Diploma</option>
                                <option value="S1">S1 / Sarjana</option>
                                <option value="S2">S2 / Magister</option>
                                <option value="S3">S3 / Doktor</option>
                                <option value="Lc.">Lc. (Timur Tengah)</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            @error('pendidikan') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Grade Guru -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Grade Guru / Golongan</label>
                            <input wire:model="grade_guru" type="text" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="Contoh: Grade A / Guru Utama / III-A" />
                            @error('grade_guru') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status Pernikahan -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Status Pernikahan <span class="text-rose-600">*</span></label>
                            <select wire:model="status_pernikahan" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                                <option value="belum_menikah">Belum Menikah</option>
                                <option value="menikah">Menikah</option>
                                <option value="cerai_hidup">Cerai Hidup</option>
                                <option value="cerai_mati">Cerai Mati</option>
                            </select>
                            @error('status_pernikahan') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status Kepegawaian -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Status Kepegawaian</label>
                            <select wire:model="status_kepegawaian" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                                <option value="pns">PNS</option>
                                <option value="gtt">GTT (Guru Tetap Yayasan)</option>
                                <option value="honorer">Honorer</option>
                            </select>
                            @error('status_kepegawaian') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Tempat Lahir -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Tempat Lahir</label>
                            <input wire:model="tempat_lahir" type="text" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="Yogyakarta" />
                        </div>

                        <!-- Tanggal Lahir -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Tanggal Lahir</label>
                            <input wire:model="tanggal_lahir" type="date" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
                            @error('tanggal_lahir') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- No HP -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">No. HP Aktif</label>
                            <input wire:model="no_hp" type="text" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" placeholder="0812..." />
                        </div>

                        <!-- Tanggal Masuk -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-stone-700 uppercase">Tanggal Masuk (Mulai Tugas) <span class="text-rose-600">*</span></label>
                            <input wire:model="tanggal_masuk" type="date" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600" />
                            @error('tanggal_masuk') <span class="text-rose-600 text-[10px] font-bold block">{{ $message }}</span> @enderror
                        </div>

                        <!-- Status Aktif -->
                        @if ($guruId)
                            <div class="space-y-1 sm:col-span-2">
                                <label class="text-xs font-bold text-stone-700 uppercase">Status Mengajar</label>
                                <select wire:model="status_aktif" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600">
                                    <option value="1">Aktif Mengajar</option>
                                    <option value="0">Nonaktif / Cuti</option>
                                </select>
                            </div>
                        @endif
                    </div>

                    <!-- Alamat -->
                    <div class="space-y-1">
                        <label class="text-xs font-bold text-stone-700 uppercase">Alamat Lengkap</label>
                        <textarea wire:model="alamat" rows="2" class="w-full px-3.5 py-2 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 resize-none" placeholder="Alamat lengkap rumah..."></textarea>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-end gap-2 border-t border-stone-200 pt-3">
                        <button type="button" wire:click="$set('isFormOpen', false)" class="px-4 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-bold">
                            Batal
                        </button>
                        <button type="submit" class="px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow-md">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
