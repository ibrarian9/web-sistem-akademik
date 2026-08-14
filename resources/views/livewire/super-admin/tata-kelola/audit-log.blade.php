<div class="space-y-6 font-sans">
    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Monitoring Audit Log Sistem"
        :steps="[
            ['title' => 'Jejak Audit Real-time', 'desc' => 'Tabel mencatat seluruh aksi entri, update, dan penghapusan data beserta IP pelakunya.'],
            ['title' => 'Filter Jenis Event', 'desc' => 'Gunakan filter event (Created, Updated, Deleted) untuk mempersempit penelusuran audit.'],
            ['title' => 'Pencarian Pengguna', 'desc' => 'Cari nama user atau alamat IP tertentu pada kotak pencarian di bagian atas.']
        ]"
    />

    <!-- Header Page -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-emerald-100 border border-emerald-300 text-emerald-900 rounded-full text-xs font-bold uppercase tracking-wider inline-block">
                SD Tahfizh F3 Activity Tracker
            </span>
            <h2 class="text-xl font-extrabold text-stone-900 tracking-tight mt-1 flex items-center gap-2">
                <x-lucide-activity class="w-5 h-5 text-emerald-600 shrink-0" />
                <span>Audit Log Aktivitas Sistem</span>
            </h2>
            <p class="text-xs text-stone-500 font-medium">Pantau seluruh riwayat aksi, perubahan data, alamat IP, dan aktivitas user pada sistem secara real-time.</p>
        </div>
    </div>

    <!-- Table Section -->
    <div class="space-y-4">
        <!-- Controls: Search & Event Selector -->
        <div class="bg-white border border-stone-200 p-4 rounded-2xl shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:max-w-xl">
                <!-- Search bar -->
                <div class="relative w-full flex-1">
                    <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3.5 top-3" />
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari aktivitas, nama user, atau IP..."
                        class="w-full pl-10 pr-4 py-2 bg-stone-50 border border-stone-200 rounded-xl text-xs font-medium text-stone-900 placeholder-stone-400 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition" />
                </div>
                
                <!-- Event selector -->
                <select wire:model.live="filterEvent" class="w-full sm:w-auto bg-stone-50 border border-stone-200 rounded-xl text-stone-900 text-xs font-medium px-3 py-2 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition">
                    <option value="">Semua Event</option>
                    @foreach ($events as $evt)
                        <option value="{{ $evt }}">{{ ucfirst($evt) }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-center gap-2 shrink-0">
                <span class="text-xs text-stone-500 font-bold">Tampilkan:</span>
                <select wire:model.live="perPage" class="bg-stone-50 border border-stone-200 rounded-xl text-stone-900 text-xs font-medium px-2.5 py-1.5 focus:ring-2 focus:ring-emerald-500">
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <x-data-table>
            <x-slot:thead>
                <th class="px-6 py-3.5">Waktu</th>
                <th class="px-6 py-3.5">Nama User (Pelaku)</th>
                <th class="px-6 py-3.5">Event</th>
                <th class="px-6 py-3.5">Deskripsi Aktivitas</th>
                <th class="px-6 py-3.5">IP Address &amp; Perangkat</th>
                <th class="px-6 py-3.5 text-center">Aksi</th>
            </x-slot:thead>
            <x-slot:tbody>
                @forelse ($logs as $log)
                    <tr class="hover:bg-stone-50/80 transition-colors cursor-pointer" wire:click="openDetail({{ $log->id }})">
                        <td class="px-6 py-4 text-xs font-mono font-semibold text-stone-600">
                            {{ date('d-m-Y H:i:s', strtotime($log->created_at)) }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-extrabold text-stone-900">{{ $log->causer_name ?? 'Sistem / Guest' }}</div>
                            @if ($log->causer_username)
                                <div class="text-[10px] text-stone-500 font-mono">@ {{ $log->causer_username }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-none text-[10px] font-black uppercase tracking-wider border
                                {{ $log->event === 'created' || $log->event === 'updated' ? 'bg-emerald-100 text-emerald-950 border-emerald-300' : '' }}
                                {{ $log->event === 'deleted' ? 'bg-rose-100 text-rose-950 border-rose-300' : '' }}
                                {{ !in_array($log->event, ['created', 'updated', 'deleted']) ? 'bg-stone-100 text-stone-800 border-stone-300' : '' }}
                            ">
                                {{ $log->event ?: 'log' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-stone-800 font-semibold max-w-sm truncate" title="{{ $log->description }}">
                            {{ $log->description }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-stone-800 text-xs font-mono">{{ $log->ip_address ?: '-' }}</div>
                            <div class="text-[10px] text-stone-500 truncate max-w-xs font-medium" title="{{ $log->user_agent }}">
                                {{ $log->user_agent ?: 'Standard Web Client' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center" @click.stop>
                            <button type="button" wire:click="openDetail({{ $log->id }})" 
                                    class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-300 rounded-none text-xs font-extrabold transition flex items-center justify-center gap-1.5 mx-auto">
                                <x-lucide-eye class="w-3.5 h-3.5 text-emerald-700" />
                                <span>Detail</span>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-stone-400 font-medium">
                            Tidak ada log aktivitas ditemukan
                        </td>
                    </tr>
                @endforelse
            </x-slot:tbody>
        </x-data-table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>

    <!-- Audit Log Detail Modal -->
    @if ($showDetailModal && $selectedLog)
        <div class="fixed inset-0 z-[99990] flex items-center justify-center bg-stone-950/65 backdrop-blur-xs p-4 sm:p-6 pt-20 sm:pt-8 pb-8 overflow-y-auto">
            <div class="bg-white border border-stone-200 rounded-none max-w-2xl w-full shadow-2xl overflow-hidden relative animate-[fadeIn_0.2s_ease-out]">
                <!-- Top Header Accent Bar -->
                <div class="h-1.5 bg-emerald-600"></div>

                <div class="p-6 space-y-5">
                    <div class="flex items-start justify-between gap-4 border-b border-stone-200 pb-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-none text-[10px] font-black uppercase tracking-wider border
                                    {{ $selectedLog['event'] === 'created' || $selectedLog['event'] === 'updated' ? 'bg-emerald-100 text-emerald-950 border-emerald-300' : '' }}
                                    {{ $selectedLog['event'] === 'deleted' ? 'bg-rose-100 text-rose-950 border-rose-300' : 'bg-stone-100 text-stone-900 border-stone-300' }}
                                ">
                                    {{ strtoupper($selectedLog['event'] ?? 'LOG') }}
                                </span>
                                <span class="text-xs text-stone-500 font-bold">ID #{{ $selectedLog['id'] }}</span>
                            </div>
                            <h3 class="text-lg font-black text-stone-900 tracking-tight flex items-center gap-2">
                                <x-lucide-file-text class="w-5 h-5 text-emerald-600 shrink-0" />
                                <span>Detail Audit Log Aktivitas</span>
                            </h3>
                        </div>

                        <button type="button" wire:click="closeDetail" class="p-1.5 rounded-none text-stone-400 hover:text-stone-800 hover:bg-stone-100 transition text-sm font-bold">
                            ✕
                        </button>
                    </div>

                    <!-- Metadata Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-none space-y-1">
                            <div class="text-[10px] uppercase font-bold text-stone-400">Waktu Kejadian</div>
                            <div class="font-mono font-bold text-stone-900">{{ date('d F Y, H:i:s', strtotime($selectedLog['created_at'])) }}</div>
                        </div>

                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-none space-y-1">
                            <div class="text-[10px] uppercase font-bold text-stone-400">Pelaku (Causer)</div>
                            <div class="font-bold text-stone-900">{{ $selectedLog['causer_name'] ?? 'Sistem / Guest' }}</div>
                            @if(!empty($selectedLog['causer_username']))
                                <div class="text-[11px] text-stone-500 font-mono">@ {{ $selectedLog['causer_username'] }}</div>
                            @endif
                        </div>

                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-none space-y-1">
                            <div class="text-[10px] uppercase font-bold text-stone-400">IP Address</div>
                            <div class="font-mono font-bold text-stone-900">{{ $selectedLog['ip_address'] ?: '-' }}</div>
                        </div>

                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-none space-y-1">
                            <div class="text-[10px] uppercase font-bold text-stone-400">User Agent / Perangkat</div>
                            <div class="text-[11px] text-stone-700 font-medium truncate" title="{{ $selectedLog['user_agent'] }}">
                                {{ $selectedLog['user_agent'] ?: 'Standard Web Browser' }}
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="space-y-1.5">
                        <div class="text-xs font-bold text-stone-800">Deskripsi Aktivitas Lengkap</div>
                        <div class="p-3.5 bg-stone-100 border border-stone-200 rounded-none text-xs font-medium text-stone-800 leading-relaxed">
                            {{ $selectedLog['description'] }}
                        </div>
                    </div>

                    <!-- JSON Properties if available -->
                    @if (!empty($selectedLog['properties_parsed']))
                        <div class="space-y-1.5">
                            <div class="text-xs font-bold text-stone-800">Data Perubahan / Context Properties</div>
                            <div class="p-3 bg-stone-900 text-emerald-400 rounded-none font-mono text-xs overflow-x-auto whitespace-pre leading-relaxed border border-stone-800 shadow-inner max-h-56">
                                {{ is_array($selectedLog['properties_parsed']) ? json_encode($selectedLog['properties_parsed'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $selectedLog['properties_parsed'] }}
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 bg-stone-50 border-t border-stone-200 flex justify-end">
                    <button type="button" wire:click="closeDetail" class="px-5 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-none text-xs font-bold transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
