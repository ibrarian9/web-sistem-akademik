<!-- Form Floating Modal -->
<x-floating-card 
    :show="$isFormOpen ? true : false"
    :title="$jadwalId ? 'Edit Jadwal Pelajaran' : 'Tambah Jadwal Baru'"
    subtitle="Atur mata pelajaran, guru pengampu, hari, dan slot waktu."
    badge="JADWAL"
    badgeVariant="emerald"
    icon="calendar"
    maxWidth="max-w-lg"
    closeAction="closeForm"
>
    <form wire:submit.prevent="save" class="space-y-4 text-xs">
        <!-- Penugasan Mapel & Kelas (Disusun Per Kelas) -->
        <div class="space-y-1">
            <label class="text-xs font-bold text-stone-700 uppercase">Mata Pelajaran & Kelas <span class="text-rose-600">*</span></label>
            <select wire:model.live="guru_mapel_kelas_id" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" required>
                <option value="">-- Pilih Penugasan Kelas & Mapel --</option>
                @foreach ($assignmentsGrouped as $namaKelas => $group)
                    <optgroup label="KELAS {{ strtoupper($namaKelas) }}">
                        @foreach ($group as $asg)
                            <option value="{{ $asg->id }}">
                                Kelas {{ $namaKelas }} — {{ $asg->mapel->nama_mapel ?? '-' }} (Pengampu: {{ $asg->guru->user->nama ?? '-' }})
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
            @error('guru_mapel_kelas_id') 
                <span class="text-rose-600 text-[10px] font-bold block mt-1">
                    {{ $message }}
                </span> 
            @enderror
        </div>

        <div class="grid grid-cols-3 gap-3">
            <!-- Hari -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">Hari <span class="text-rose-600">*</span></label>
                <select wire:model.live="hari" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="senin">Senin</option>
                    <option value="selasa">Selasa</option>
                    <option value="rabu">Rabu</option>
                    <option value="kamis">Kamis</option>
                    <option value="jumat">Jumat</option>
                    <option value="sabtu">Sabtu</option>
                </select>
                @error('hari') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Jam Mulai -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">Jam Mulai <span class="text-rose-600">*</span></label>
                <input wire:model="jam_mulai" type="time" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" required />
                @error('jam_mulai') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Jam Selesai -->
            <div class="space-y-1">
                <label class="text-xs font-bold text-stone-700 uppercase">Jam Selesai <span class="text-rose-600">*</span></label>
                <input wire:model="jam_selesai" type="time" class="w-full px-3.5 py-2.5 bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold focus:ring-2 focus:ring-emerald-600 shadow-2xs" required />
                @error('jam_selesai') <span class="text-rose-600 text-[10px] font-bold block mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Live Helper Box: Existing Schedules for Chosen Class & Day -->
        @if (count($formExistingSchedules) > 0)
            <div class="p-3.5 bg-emerald-50 border border-emerald-200 rounded-xl space-y-1.5 text-xs">
                <div class="font-extrabold text-emerald-950 flex items-center gap-1.5">
                    <x-lucide-info class="w-4 h-4 text-emerald-700 shrink-0" />
                    <span>Jadwal Terisi Hari {{ ucfirst($hari) }} pada Kelas Ini:</span>
                </div>
                <div class="space-y-1 pl-5 text-[11px] text-stone-700 font-medium">
                    @foreach ($formExistingSchedules as $ex)
                        <div class="flex items-center justify-between">
                            <div>
                                <strong class="text-stone-900">{{ date('H:i', strtotime($ex->jam_mulai)) }} - {{ date('H:i', strtotime($ex->jam_selesai)) }} WIB</strong>:
                                <span class="text-emerald-800 font-bold">{{ $ex->guruMapelKelas->mapel->nama_mapel ?? '-' }}</span>
                            </div>
                            <span class="text-stone-500 text-[10px]">({{ $ex->guruMapelKelas->guru->user->nama ?? '-' }})</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @elseif ($guru_mapel_kelas_id)
            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-xs text-emerald-950 flex items-center gap-2 font-bold">
                <x-lucide-check-circle class="w-4 h-4 text-emerald-700 shrink-0" />
                <span>Hari <strong>{{ ucfirst($hari) }}</strong> belum ada jadwal terisi untuk kelas ini.</span>
            </div>
        @endif

        <!-- Buttons -->
        <div class="flex items-center justify-end gap-2 border-t border-stone-200 pt-3">
            <x-button type="button" variant="secondary" size="md" wire:click="closeForm">
                Batal
            </x-button>
            <x-button type="submit" variant="primary" size="md" icon="save" loadingTarget="save">
                Simpan Jadwal
            </x-button>
        </div>
    </form>
</x-floating-card>
