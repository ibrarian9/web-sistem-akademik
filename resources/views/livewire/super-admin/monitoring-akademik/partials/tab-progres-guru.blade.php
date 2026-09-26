<!-- ==================== TAB 0: MONITORING PROGRES GURU ==================== -->
@if ($activeTab === 'progres_guru')
    <div class="space-y-6">
        <!-- Filter Bar -->
        <div class="bg-white border border-stone-200 rounded-2xl shadow-xs p-5">
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Filter Rombel Kelas</label>
                    <select wire:model.live="progresKelasId" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3.5 py-2.5 text-sm font-semibold text-stone-800 shadow-xs focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500">
                        <option value="">-- Semua Kelas --</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->nama_kelas }} (Tingkat {{ $c->tingkat }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Filter Mata Pelajaran</label>
                    <select wire:model.live="progresMapelId" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3.5 py-2.5 text-sm font-semibold text-stone-800 shadow-xs focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500">
                        <option value="">-- Semua Mapel --</option>
                        @foreach ($mapels as $m)
                            <option value="{{ $m->id }}">{{ $m->nama_mapel }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Semester</label>
                    <select wire:model.live="selectedSemesterId" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3.5 py-2.5 text-sm font-semibold text-stone-800 shadow-xs focus:border-emerald-500 focus:bg-white focus:ring-1 focus:ring-emerald-500">
                        @foreach ($semesters as $s)
                            <option value="{{ $s->id }}">{{ $s->nama_semester }} ({{ $s->tahunAjaran->nama_tahun ?? '-' }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-stone-500 uppercase tracking-wider mb-2">Cari Guru / Mapel</label>
                    <x-search-input :model="'searchProgresGuru'" wire:model.live.debounce.300ms="searchProgresGuru" placeholder="Cari nama guru atau mapel..." />
                </div>
            </div>
        </div>

        <!-- Table Progres Guru -->
        <div class="bg-white border border-stone-200 rounded-2xl shadow-xs overflow-hidden">
            <div class="p-5 border-b border-stone-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-stone-50/50">
                <div>
                    <h3 class="font-extrabold text-stone-900 text-sm">Progres Supervisi Guru di Setiap Kelas</h3>
                    <p class="text-xs text-stone-500 mt-0.5">Pantau kelengkapan pengisian materi bab, capaian TP, dan progres input nilai siswa secara real-time.</p>
                </div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-bold">
                    <x-lucide-check-circle class="w-4 h-4 text-emerald-600" />
                    <span>Total Penugasan: {{ count($progresGuruData) }}</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-stone-100/70 border-b border-stone-200 text-stone-600 uppercase tracking-wider font-extrabold text-[11px]">
                            <th class="p-3.5">Kelas & Tingkat</th>
                            <th class="p-3.5">Mata Pelajaran</th>
                            <th class="p-3.5">Guru Pengampu</th>
                            <th class="p-3.5 text-center">Pengisian Bab</th>
                            <th class="p-3.5 text-center">Capaian TP</th>
                            <th class="p-3.5 min-w-[200px]">Progres Input Nilai Siswa</th>
                            <th class="p-3.5 text-center">Status</th>
                            <th class="p-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-200">
                        @forelse ($progresGuruData as $p)
                            <tr class="hover:bg-stone-50/80 transition duration-150">
                                <td class="p-3.5 font-extrabold text-stone-900">
                                    <div class="flex items-center gap-2">
                                        <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-xs">
                                            {{ $p['tingkat'] }}
                                        </span>
                                        <span>{{ $p['nama_kelas'] }}</span>
                                    </div>
                                </td>
                                <td class="p-3.5 font-bold text-stone-800">
                                    {{ $p['nama_mapel'] }}
                                </td>
                                <td class="p-3.5">
                                    <div class="font-extrabold text-stone-900">{{ $p['nama_guru'] }}</div>
                                    <div class="text-[10px] text-stone-500">NIP/NIY: {{ $p['nip_guru'] }}</div>
                                </td>
                                <td class="p-3.5 text-center">
                                    @if ($p['total_bab'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            {{ $p['total_bab'] }} Bab
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-700 border border-rose-200">
                                            0 Bab
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3.5 text-center">
                                    @if ($p['total_tp'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-blue-100 text-blue-800 border border-blue-200">
                                            {{ $p['total_tp'] }} TP
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-black bg-stone-100 text-stone-600 border border-stone-200">
                                            0 TP
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3.5">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center justify-between text-[11px]">
                                            <span class="font-bold text-stone-700">{{ $p['graded_siswa_count'] }} dari {{ $p['total_siswa'] }} Siswa</span>
                                            <span class="font-extrabold {{ $p['persen_nilai'] >= 100 ? 'text-emerald-700' : ($p['persen_nilai'] > 0 ? 'text-blue-700' : 'text-stone-400') }}">{{ $p['persen_nilai'] }}%</span>
                                        </div>
                                        <div class="w-full bg-stone-200 rounded-full h-2 overflow-hidden">
                                            <div class="h-2 rounded-full transition-all duration-300 {{ $p['persen_nilai'] >= 100 ? 'bg-emerald-500' : ($p['persen_nilai'] > 0 ? 'bg-blue-500' : 'bg-stone-300') }}" 
                                                style="width: {{ min(100, $p['persen_nilai']) }}%">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3.5 text-center">
                                    @if ($p['total_bab'] === 0)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase bg-rose-50 text-rose-700 border border-rose-200">
                                            Belum Ada Bab
                                        </span>
                                    @elseif ($p['total_tp'] === 0)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase bg-amber-50 text-amber-700 border border-amber-200">
                                            Belum Ada TP
                                        </span>
                                    @elseif ($p['persen_nilai'] >= 100)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Lengkap
                                        </span>
                                    @elseif ($p['persen_nilai'] > 0)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase bg-blue-50 text-blue-700 border border-blue-200">
                                            Sebagian
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-extrabold uppercase bg-stone-100 text-stone-600 border border-stone-200">
                                            Belum Dinilai
                                        </span>
                                    @endif
                                </td>
                                <td class="p-3.5 text-center">
                                    <button wire:click="inspectNilai({{ $p['kelas_id'] }}, {{ $p['mapel_id'] }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-stone-100 hover:bg-emerald-50 text-stone-700 hover:text-emerald-700 font-extrabold text-[11px] border border-stone-300 hover:border-emerald-300 transition duration-150">
                                        <x-lucide-eye class="w-3.5 h-3.5" />
                                        <span>Lihat Rincian</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="p-12 text-center text-stone-400">
                                    <x-lucide-inbox class="w-10 h-10 mx-auto text-stone-300 mb-2" />
                                    <div class="font-extrabold text-stone-600 text-sm">Tidak Ada Data Penugasan Guru</div>
                                    <p class="text-xs text-stone-400 mt-1">Coba ubah filter rombel kelas, mapel, atau kata kunci pencarian.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
