<!-- Modal Atur Jadwal Ekstrakurikuler (Admin) -->
@if ($isEkskulFormOpen)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-stone-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-stone-200 overflow-hidden">
            <div class="p-5 bg-stone-50 border-b border-stone-200 flex items-center justify-between">
                <h3 class="font-extrabold text-stone-900 text-base">Atur Jadwal Ekstrakurikuler</h3>
                <button wire:click="closeEkskulSchedule" class="text-stone-400 hover:text-stone-600">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>
            <form wire:submit.prevent="saveEkskulSchedule" class="p-6 space-y-4 text-xs">
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Hari Pelaksanaan *</label>
                    <select wire:model="ekskulHari" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm font-semibold text-stone-900 focus:ring-2 focus:ring-emerald-600">
                        <option value="senin">Senin</option>
                        <option value="selasa">Selasa</option>
                        <option value="rabu">Rabu</option>
                        <option value="kamis">Kamis</option>
                        <option value="jumat">Jumat</option>
                        <option value="sabtu">Sabtu</option>
                        <option value="minggu">Minggu</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Jam Mulai *</label>
                        <input type="time" wire:model="ekskulJamMulai" class="w-full rounded-xl border border-stone-300 px-3.5 py-2 text-sm font-semibold text-stone-900 focus:ring-2 focus:ring-emerald-600">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Jam Selesai *</label>
                        <input type="time" wire:model="ekskulJamSelesai" class="w-full rounded-xl border border-stone-300 px-3.5 py-2 text-sm font-semibold text-stone-900 focus:ring-2 focus:ring-emerald-600">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Tempat / Ruangan / Lapangan</label>
                    <input type="text" wire:model="ekskulTempat" placeholder="Contoh: Lapangan Utama, Aula Al-Hikmah, Lab Komputer" class="w-full rounded-xl border border-stone-300 px-3.5 py-2.5 text-sm font-semibold text-stone-900 focus:ring-2 focus:ring-emerald-600">
                </div>
                <div class="pt-3 border-t border-stone-200 flex items-center justify-end gap-2">
                    <button type="button" wire:click="closeEkskulSchedule" class="px-4 py-2.5 rounded-xl border border-stone-300 text-stone-700 hover:bg-stone-100 text-xs font-bold">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs">
                        Simpan Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
