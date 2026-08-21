<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Kelola Data Kepegawaian Guru" 
        subtitle="Pencatatan NIY, NIK, pendidikan, grade guru, status pernikahan, dan kredensial login."
        badge="MANAJEMEN GURU"
        badgeVariant="emerald"
        icon="user-check"
    >
        <x-slot:actions>
            <x-button type="button" variant="primary" size="md" icon="plus" wire:click.prevent="openCreate">
                Tambah Guru Baru
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Pengelolaan Data Guru & Tenaga Pendidik"
        :steps="[
            ['title' => 'Input Profil & Kredensial', 'desc' => 'Klik Tambah Guru Baru untuk mendaftarkan NIK, NIY, pendidikan terakhir, grade guru, serta status pernikahan.'],
            ['title' => 'Status Kepegawaian & Grade', 'desc' => 'Tentukan status kepegawaian (PNS/GTT/Honorer) & Grade Guru untuk penyesuaian penggajian & jenjang karir.'],
            ['title' => 'Manajemen Status Kerja', 'desc' => 'Ubah status mengajar menjadi Nonaktif jika guru yang bersangkutan sedang mutasi, berhenti, atau cuti.']
        ]"
        notes="NIY (Nomor Induk Yayasan) & NIK diisikan demi keperluan administrasi yayasan, dapodik, dan penggajian."
    />

    @if (session()->has('message'))
        <x-alert-banner type="success" :message="session('message')" />
    @endif

    <!-- Content Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <!-- Toolbar & Filter -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="max-w-md w-full">
                <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari NIY, NIK, nama, grade, atau pendidikan..." />
            </div>
            
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-stone-600">Tampilkan:</span>
                <select wire:model.live="perPage" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-2 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="10">10 Baris</option>
                    <option value="25">25 Baris</option>
                    <option value="50">50 Baris</option>
                </select>
            </div>
        </div>

        <!-- Data Table -->
        <x-table loadingTarget="search, perPage">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="w-36">NIY / NIK</x-table.th>
                    <x-table.th class="min-w-[180px]">Nama Guru</x-table.th>
                    <x-table.th class="min-w-[150px]">Pendidikan &amp; Grade</x-table.th>
                    <x-table.th align="center" class="w-32">Status Nikah</x-table.th>
                    <x-table.th class="min-w-[150px]">TTL &amp; Tgl Masuk</x-table.th>
                    <x-table.th class="w-32">Status Pegawai</x-table.th>
                    <x-table.th class="min-w-[120px]">No. HP</x-table.th>
                    <x-table.th align="center" class="w-28">Status Kerja</x-table.th>
                    <x-table.th align="center" class="w-36">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($gurus as $guru)
                    @php
                        $statusNikahVariant = match($guru->status_pernikahan) {
                            'menikah' => 'emerald',
                            'cerai_hidup' => 'amber',
                            'cerai_mati' => 'rose',
                            default => 'stone',
                        };
                        $statusNikahText = match($guru->status_pernikahan) {
                            'menikah' => 'Menikah',
                            'cerai_hidup' => 'Cerai Hidup',
                            'cerai_mati' => 'Cerai Mati',
                            default => 'Belum Menikah',
                        };
                    @endphp
                    <tr class="hover:bg-stone-50 transition">
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
                                <x-badge variant="emerald" size="xs">
                                    {{ $guru->grade_guru }}
                                </x-badge>
                            @else
                                <span class="text-stone-400 italic text-[10px]">- Grade belum set -</span>
                            @endif
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            <x-badge :variant="$statusNikahVariant" size="xs">
                                {{ $statusNikahText }}
                            </x-badge>
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
                                <x-button type="button" variant="secondary" size="xs" icon="edit" wire:click.prevent="openEdit({{ $guru->id }})">
                                    Edit
                                </x-button>
                                <x-button type="button" variant="danger" size="xs" icon="trash-2" wire:click.prevent="delete({{ $guru->id }})" data-confirm="Apakah Anda yakin ingin menghapus data guru ini?">
                                    Hapus
                                </x-button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-stone-400">
                            <x-table.empty title="Tidak ada data guru ditemukan" subtitle="Gunakan tombol Tambah Guru Baru di atas untuk mendaftarkan tenaga pendidik." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        <!-- Pagination -->
        <div class="pt-2">
            {{ $gurus->links() }}
        </div>
    </div>

    <!-- Form Floating Modal -->
    <x-floating-card 
        :show="$isFormOpen ? true : false"
        :title="$guruId ? 'Edit Data Profil Guru' : 'Tambah Guru Baru'"
        subtitle="Lengkapi data profil kepegawaian dan akun guru."
        badge="PROFIL GURU"
        badgeVariant="emerald"
        icon="user-check"
        maxWidth="max-w-2xl"
        closeAction="$set('isFormOpen', false)"
    >
        @if ($errors->any())
            <div class="p-3.5 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl space-y-1.5 text-xs shadow-2xs mb-4">
                <div class="flex items-center gap-2 font-extrabold text-rose-900">
                    <x-lucide-alert-triangle class="w-4 h-4 text-rose-600 shrink-0" />
                    <span>Mohon Perbaiki Isian Formulir:</span>
                </div>
                <ul class="list-disc list-inside text-[11px] font-bold text-rose-700 space-y-0.5 pl-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form wire:submit.prevent="save" action="javascript:void(0);" class="space-y-4 text-xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Nama -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Nama Lengkap <span class="text-rose-600">*</span></label>
                    <input wire:model="nama" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Ahmad Budi, S.Pd" required />
                    @error('nama') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Username -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Username Login <span class="text-rose-600">*</span></label>
                    <input wire:model="username" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="budi_guru" required />
                    @error('username') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Email -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Email (Opsional)</label>
                    <input wire:model="email" type="email" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="budi@sekolah.com" />
                    @error('email') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Password -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">{{ $guruId ? 'Ganti Password (Kosongkan jika tidak)' : 'Password Awal *' }}</label>
                    <input wire:model="password" type="password" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="••••••" />
                    @error('password') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- NIY -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">NIY (Nomor Induk Yayasan)</label>
                    <input wire:model="niy" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="NIY.1987..." />
                    @error('niy') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- NIK -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">NIK (KTP 16 Digit)</label>
                    <input wire:model="nik" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="3404..." />
                    @error('nik') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Pendidikan Terakhir -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Pendidikan Terakhir</label>
                    <select wire:model="pendidikan" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="">-- Pilih Pendidikan --</option>
                        <option value="SMA/MA">SMA/MA/Sederajat</option>
                        <option value="D3">D3 / Diploma</option>
                        <option value="S1">S1 / Sarjana</option>
                        <option value="S2">S2 / Magister</option>
                        <option value="S3">S3 / Doktor</option>
                        <option value="Lc.">Lc. (Timur Tengah)</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                    @error('pendidikan') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Grade Guru -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Grade Guru / Golongan</label>
                    <input wire:model="grade_guru" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Contoh: Grade A / Guru Utama / III-A" />
                    @error('grade_guru') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Status Pernikahan -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Status Pernikahan <span class="text-rose-600">*</span></label>
                    <select wire:model="status_pernikahan" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="belum_menikah">Belum Menikah</option>
                        <option value="menikah">Menikah</option>
                        <option value="cerai_hidup">Cerai Hidup</option>
                        <option value="cerai_mati">Cerai Mati</option>
                    </select>
                    @error('status_pernikahan') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Status Kepegawaian -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Status Kepegawaian</label>
                    <select wire:model="status_kepegawaian" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="honorer">Honorer</option>
                        <option value="tetap_yayasan">Tetap Yayasan</option>
                        <option value="gtt">GTT (Guru Tidak Tetap)</option>
                        <option value="gty">GTY (Guru Tetap Yayasan)</option>
                        <option value="pns">PNS / ASN</option>
                    </select>
                    @error('status_kepegawaian') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Jenis Guru / Peran Pengampu -->
                <div class="space-y-1 sm:col-span-2 p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl">
                    <label class="text-xs font-extrabold text-emerald-950 uppercase block">Peran Guru / Jenis Pengampu <span class="text-rose-600">*</span></label>
                    <select wire:model="jenis_guru" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                        <option value="umum">Guru Mata Pelajaran (Umum)</option>
                        <option value="tahfidz">Guru Halaqah Tahfizh (Ustadz/ah Tahfizh)</option>
                        <option value="keduanya">Keduanya (Guru Mapel &amp; Pengampu Halaqah Tahfizh)</option>
                    </select>
                    <p class="text-[10px] font-medium text-emerald-800 mt-1">Pilih <b>Guru Halaqah Tahfizh</b> atau <b>Keduanya</b> agar nama guru ini dapat dipilih sebagai Pengampu Halaqah pada Manajemen Kelas &amp; Tahfizh.</p>
                    @error('jenis_guru') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Tempat Lahir -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Tempat Lahir</label>
                    <input wire:model="tempat_lahir" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="Yogyakarta" />
                </div>

                <!-- Tanggal Lahir -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Tanggal Lahir</label>
                    <input wire:model="tanggal_lahir" type="date" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                    @error('tanggal_lahir') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- No HP -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">No. HP Aktif</label>
                    <input wire:model="no_hp" type="text" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" placeholder="0812..." />
                </div>

                <!-- Tanggal Masuk -->
                <div class="space-y-1">
                    <label class="text-xs font-bold text-stone-700 uppercase">Tanggal Masuk (Mulai Tugas) <span class="text-rose-600">*</span></label>
                    <input wire:model="tanggal_masuk" type="date" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" />
                    @error('tanggal_masuk') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Status Aktif -->
                @if ($guruId)
                    <div class="space-y-1 sm:col-span-2">
                        <label class="text-xs font-bold text-stone-700 uppercase">Status Mengajar</label>
                        <select wire:model="status_aktif" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                            <option value="1">Aktif Mengajar</option>
                            <option value="0">Nonaktif / Cuti</option>
                        </select>
                    </div>
                @endif
            </div>

            <!-- Alamat -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">Alamat Lengkap</label>
                <textarea wire:model="alamat" rows="2" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-medium focus:ring-2 focus:ring-emerald-600 shadow-2xs resize-none" placeholder="Alamat lengkap rumah..."></textarea>
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end gap-2 border-t border-stone-200 pt-3">
                <x-button type="button" variant="secondary" size="md" wire:click="$set('isFormOpen', false)">
                    Batal
                </x-button>
                <x-button type="submit" variant="primary" size="md" icon="save" loadingTarget="save">
                    Simpan Perubahan
                </x-button>
            </div>
        </form>
    </x-floating-card>
</div>
