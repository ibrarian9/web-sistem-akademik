<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="Audit Log Aktivitas Sistem" 
        subtitle="Pantau seluruh riwayat aksi, perubahan data, alamat IP, dan aktivitas user pada sistem secara real-time."
        badge="ACTIVITY TRACKER"
        badgeVariant="emerald"
        icon="activity"
    >
        <x-slot:actions>
            <div class="flex items-center gap-1.5 bg-stone-100 border border-stone-200 p-1 rounded-xl overflow-x-auto shadow-2xs">
                <x-button type="button" :variant="$filterPeriode === '' ? 'primary' : 'ghost'" size="xs" wire:click="$set('filterPeriode', '')">
                    Semua Waktu
                </x-button>
                <x-button type="button" :variant="$filterPeriode === 'today' ? 'primary' : 'ghost'" size="xs" wire:click="setPeriodPreset('today')">
                    Hari Ini
                </x-button>
                <x-button type="button" :variant="$filterPeriode === 'yesterday' ? 'primary' : 'ghost'" size="xs" wire:click="setPeriodPreset('yesterday')">
                    Kemarin
                </x-button>
                <x-button type="button" :variant="$filterPeriode === 'this_week' ? 'primary' : 'ghost'" size="xs" wire:click="setPeriodPreset('this_week')">
                    Minggu Ini
                </x-button>
                <x-button type="button" :variant="$filterPeriode === 'this_month' ? 'primary' : 'ghost'" size="xs" wire:click="setPeriodPreset('this_month')">
                    Bulan Ini
                </x-button>
            </div>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Monitoring Audit Log Sistem"
        :steps="[
            ['title' => 'Jejak Audit Real-time', 'desc' => 'Tabel mencatat seluruh aksi entri, update, dan penghapusan data beserta IP pelakunya.'],
            ['title' => 'Filter Periode & Event', 'desc' => 'Gunakan filter periode (Hari Ini, Kemarin, Minggu Ini, Bulan Ini) atau filter event (Created, Updated, Deleted) untuk penelusuran cepat.'],
            ['title' => 'Pencarian Pengguna', 'desc' => 'Cari nama user atau alamat IP tertentu pada kotak pencarian di bagian atas.']
        ]"
    />

    <!-- Content Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <!-- Controls: Search & Event Selector -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:max-w-xl">
                <!-- Search bar -->
                <div class="w-full flex-1">
                    <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari aktivitas, nama user, atau IP..." />
                </div>
                
                <!-- Event selector -->
                <select wire:model.live="filterEvent" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">Semua Event</option>
                    @foreach ($events as $evt)
                        <option value="{{ $evt }}">{{ ucfirst($evt) }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-center gap-2">
                <span class="text-xs text-stone-600 font-bold">Tampilkan:</span>
                <select wire:model.live="perPage" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3 py-2 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="20">20 Baris</option>
                    <option value="50">50 Baris</option>
                    <option value="100">100 Baris</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <x-table loadingTarget="filterEvent, filterPeriode, perPage, search">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="w-44">Waktu</x-table.th>
                    <x-table.th class="min-w-[180px]">Nama User (Pelaku)</x-table.th>
                    <x-table.th class="w-28 text-center">Event</x-table.th>
                    <x-table.th class="min-w-[220px]">Deskripsi Aktivitas</x-table.th>
                    <x-table.th class="min-w-[180px]">IP Address &amp; Perangkat</x-table.th>
                    <x-table.th align="center" class="w-28">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($logs as $log)
                    <tr class="hover:bg-stone-50 transition cursor-pointer" wire:click="openDetail({{ $log->id }})">
                        <td class="p-3.5 text-xs font-mono font-bold text-stone-600 border-r border-stone-200">
                            {{ date('d-m-Y H:i:s', strtotime($log->created_at)) }}
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-extrabold text-stone-900 text-xs">{{ $log->causer_name ?? 'Sistem / Guest' }}</div>
                            @if ($log->causer_username)
                                <div class="text-[10px] text-stone-500 font-mono">@ {{ $log->causer_username }}</div>
                            @endif
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @if ($log->event === 'created')
                                <x-badge variant="emerald" size="xs">Created</x-badge>
                            @elseif ($log->event === 'updated')
                                <x-badge variant="blue" size="xs">Updated</x-badge>
                            @elseif ($log->event === 'deleted')
                                <x-badge variant="rose" size="xs">Deleted</x-badge>
                            @else
                                <x-badge variant="stone" size="xs">{{ $log->event ?: 'log' }}</x-badge>
                            @endif
                        </td>
                        <td class="p-3.5 text-stone-800 font-semibold max-w-sm truncate border-r border-stone-200" title="{{ $log->description }}">
                            {{ $log->description }}
                        </td>
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-bold text-stone-900 text-xs font-mono">{{ $log->ip_address ?: '-' }}</div>
                            <div class="text-[10px] text-stone-500 truncate max-w-xs font-medium" title="{{ $log->user_agent }}">
                                {{ $log->user_agent ?: 'Standard Web Client' }}
                            </div>
                        </td>
                        <td class="p-3.5 text-center" @click.stop>
                            <x-button type="button" variant="outline" size="xs" icon="eye" wire:click="openDetail({{ $log->id }})">
                                Detail
                            </x-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-stone-400">
                            <x-table.empty title="Tidak ada log aktivitas ditemukan" subtitle="Aktivitas audit sistem akan tercatat secara otomatis saat data dimodifikasi." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        <!-- Pagination -->
        <div class="pt-2">
            {{ $logs->links() }}
        </div>
    </div>

    <!-- Audit Log Detail Modal -->
    <x-floating-card 
        :show="($showDetailModal && $selectedLog) ? true : false"
        title="Detail Audit Log Aktivitas"
        :subtitle="'ID #' . ($selectedLog['id'] ?? '') . ' - Dicatat pada ' . (isset($selectedLog['created_at']) ? date('d F Y, H:i:s', strtotime($selectedLog['created_at'])) : '-')"
        badge="LOG ACTIVITY"
        badgeVariant="emerald"
        icon="activity"
        maxWidth="max-w-2xl"
        closeAction="closeDetail"
    >
        @if ($selectedLog)
            <div class="space-y-4 text-xs">
                <!-- Metadata Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-1">
                        <div class="text-[10px] uppercase font-bold text-stone-500">Pelaku (Causer)</div>
                        <div class="font-bold text-stone-900">{{ $selectedLog['causer_name'] ?? 'Sistem / Guest' }}</div>
                        @if(!empty($selectedLog['causer_username']))
                            <div class="text-[11px] text-stone-500 font-mono">@ {{ $selectedLog['causer_username'] }}</div>
                        @endif
                    </div>

                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-1">
                        <div class="text-[10px] uppercase font-bold text-stone-500">Event Aksi</div>
                        <div>
                            @if (($selectedLog['event'] ?? '') === 'created')
                                <x-badge variant="emerald" size="xs">CREATED</x-badge>
                            @elseif (($selectedLog['event'] ?? '') === 'updated')
                                <x-badge variant="blue" size="xs">UPDATED</x-badge>
                            @elseif (($selectedLog['event'] ?? '') === 'deleted')
                                <x-badge variant="rose" size="xs">DELETED</x-badge>
                            @else
                                <x-badge variant="stone" size="xs">{{ strtoupper($selectedLog['event'] ?? 'LOG') }}</x-badge>
                            @endif
                        </div>
                    </div>

                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-1">
                        <div class="text-[10px] uppercase font-bold text-stone-500">IP Address</div>
                        <div class="font-mono font-bold text-stone-900">{{ $selectedLog['ip_address'] ?: '-' }}</div>
                    </div>

                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-1">
                        <div class="text-[10px] uppercase font-bold text-stone-500">User Agent / Perangkat</div>
                        <div class="text-[11px] text-stone-700 font-medium truncate" title="{{ $selectedLog['user_agent'] ?? '' }}">
                            {{ $selectedLog['user_agent'] ?: 'Standard Web Browser' }}
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="space-y-1">
                    <div class="text-xs font-bold text-stone-800 uppercase">Deskripsi Aktivitas Lengkap</div>
                    <div class="p-3.5 bg-stone-100 border border-stone-200 rounded-xl text-xs font-medium text-stone-900 leading-relaxed">
                        {{ $selectedLog['description'] }}
                    </div>
                </div>

                <!-- JSON Properties if available -->
                @if (!empty($selectedLog['properties_parsed']))
                    <div class="space-y-1">
                        <div class="text-xs font-bold text-stone-800 uppercase">Data Perubahan / Context Properties</div>
                        <div class="p-3 bg-stone-900 text-emerald-400 rounded-xl font-mono text-xs overflow-x-auto whitespace-pre leading-relaxed border border-stone-800 shadow-inner max-h-56">
                            {{ is_array($selectedLog['properties_parsed']) ? json_encode($selectedLog['properties_parsed'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : $selectedLog['properties_parsed'] }}
                        </div>
                    </div>
                @endif

                <div class="flex justify-end pt-3 border-t border-stone-200">
                    <x-button type="button" variant="secondary" size="md" wire:click="closeDetail">
                        Tutup
                    </x-button>
                </div>
            </div>
        @endif
    </x-floating-card>
</div>
