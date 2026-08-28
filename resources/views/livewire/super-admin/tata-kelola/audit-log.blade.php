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
                    <x-table.th class="w-52">Hari & Waktu</x-table.th>
                    <x-table.th class="min-w-[180px]">Nama User (Pelaku)</x-table.th>
                    <x-table.th class="w-28 text-center">Event</x-table.th>
                    <x-table.th class="min-w-[220px]">Deskripsi Aktivitas</x-table.th>
                    <x-table.th class="min-w-[180px]">IP Address & Perangkat</x-table.th>
                    <x-table.th align="center" class="w-28">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white">
                @forelse ($logs as $log)
                    <tr class="hover:bg-stone-50 transition cursor-pointer" wire:click="openDetail({{ $log->id }})">
                        <td class="p-3.5 border-r border-stone-200">
                            <div class="font-extrabold text-stone-900 text-xs flex items-center gap-1.5">
                                <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-black uppercase bg-emerald-100/80 text-emerald-800 border border-emerald-200">
                                    {{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('l') }}
                                </span>
                                <span>{{ \Carbon\Carbon::parse($log->created_at)->translatedFormat('d M Y') }}</span>
                            </div>
                            <div class="text-[11px] text-stone-500 font-mono flex items-center gap-1 mt-1 font-semibold">
                                <x-lucide-clock class="w-3.5 h-3.5 text-stone-400 inline" />
                                {{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }} <span class="text-[10px] text-stone-400 font-bold">WIB</span>
                            </div>
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

    <!-- Audit Log Detail Modal with Diff Inspector -->
    <x-floating-card 
        :show="($showDetailModal && $selectedLog) ? true : false"
        title="Detail Audit Log & Diff Inspector"
        :subtitle="'ID #' . ($selectedLog['id'] ?? '') . ' - Dicatat pada ' . (isset($selectedLog['created_at']) ? \Carbon\Carbon::parse($selectedLog['created_at'])->translatedFormat('l, d F Y - H:i:s') . ' WIB' : '-')"
        badge="DIFF INSPECTOR"
        badgeVariant="emerald"
        icon="activity"
        maxWidth="max-w-3xl"
        closeAction="closeDetail"
    >
        @if ($selectedLog)
            <div class="space-y-4 text-xs">
                <!-- Metadata Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-1">
                        <div class="text-[10px] uppercase font-bold text-stone-500">Pelaku (Causer)</div>
                        <div class="font-bold text-stone-900 truncate">{{ $selectedLog['causer_name'] ?? 'Sistem / Guest' }}</div>
                        @if(!empty($selectedLog['causer_username']))
                            <div class="text-[10px] text-stone-500 font-mono">@ {{ $selectedLog['causer_username'] }}</div>
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
                        <div class="text-[10px] uppercase font-bold text-stone-500">Target Model</div>
                        <div class="font-bold text-stone-900 truncate font-mono text-[11px]">
                            {{ !empty($selectedLog['subject_type']) ? class_basename($selectedLog['subject_type']) : 'Sistem' }}
                            @if (!empty($selectedLog['subject_id']))
                                <span class="text-stone-400 font-normal">#{{ $selectedLog['subject_id'] }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-1">
                        <div class="text-[10px] uppercase font-bold text-stone-500">Waktu & Hari (WIB)</div>
                        <div class="font-bold text-stone-900 text-xs truncate">
                            {{ isset($selectedLog['created_at']) ? \Carbon\Carbon::parse($selectedLog['created_at'])->translatedFormat('l, d/m/Y') : '-' }}
                        </div>
                        <div class="font-mono text-[11px] text-stone-500 font-semibold">
                            {{ isset($selectedLog['created_at']) ? \Carbon\Carbon::parse($selectedLog['created_at'])->format('H:i:s') . ' WIB' : '-' }}
                        </div>
                    </div>
                </div>

                <!-- Description Bar -->
                <div class="p-3.5 bg-emerald-50/60 border border-emerald-200 rounded-xl flex items-start gap-2.5">
                    <x-lucide-info class="w-4 h-4 text-emerald-700 shrink-0 mt-0.5" />
                    <div>
                        <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Deskripsi Aktivitas:</span>
                        <div class="text-xs font-bold text-emerald-950 leading-relaxed mt-0.5">
                            {{ $selectedLog['description'] }}
                        </div>
                    </div>
                </div>

                <!-- Tab Switcher for Diff Inspector -->
                <div class="border-b border-stone-200 flex items-center gap-2 pt-1">
                    <button 
                        type="button" 
                        wire:click="$set('detailTab', 'diff')"
                        class="pb-2.5 px-3 text-xs font-bold transition border-b-2 flex items-center gap-1.5 cursor-pointer {{ $detailTab === 'diff' ? 'border-emerald-600 text-emerald-800' : 'border-transparent text-stone-500 hover:text-stone-800' }}"
                    >
                        <x-lucide-git-commit class="w-3.5 h-3.5" />
                        <span>Visual Diff (Perubahan Atribut)</span>
                        @if (!empty($selectedLog['changes_parsed']) && is_array($selectedLog['changes_parsed']))
                            <span class="px-1.5 py-0.2 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-black">
                                {{ count($selectedLog['changes_parsed']) }}
                            </span>
                        @endif
                    </button>

                    <button 
                        type="button" 
                        wire:click="$set('detailTab', 'properties')"
                        class="pb-2.5 px-3 text-xs font-bold transition border-b-2 flex items-center gap-1.5 cursor-pointer {{ $detailTab === 'properties' ? 'border-emerald-600 text-emerald-800' : 'border-transparent text-stone-500 hover:text-stone-800' }}"
                    >
                        <x-lucide-sliders class="w-3.5 h-3.5" />
                        <span>Konteks Request & Klien</span>
                    </button>

                    <button 
                        type="button" 
                        wire:click="$set('detailTab', 'raw')"
                        class="pb-2.5 px-3 text-xs font-bold transition border-b-2 flex items-center gap-1.5 cursor-pointer {{ $detailTab === 'raw' ? 'border-emerald-600 text-emerald-800' : 'border-transparent text-stone-500 hover:text-stone-800' }}"
                    >
                        <x-lucide-code class="w-3.5 h-3.5" />
                        <span>Raw JSON Tree</span>
                    </button>
                </div>

                <!-- Tab 1: Visual Diff Inspector -->
                @if ($detailTab === 'diff')
                    <div class="space-y-2">
                        @if (!empty($selectedLog['changes_parsed']) && is_array($selectedLog['changes_parsed']))
                            <div class="border border-stone-200 rounded-xl overflow-hidden shadow-2xs">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead class="bg-stone-100 text-stone-700 font-extrabold uppercase tracking-wider border-b border-stone-200">
                                        <tr>
                                            <th class="p-2.5 w-1/3 border-r border-stone-200">Kolom / Atribut Data</th>
                                            <th class="p-2.5">Nilai Tercatat (Value / Payload)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-stone-200 bg-white">
                                        @foreach ($selectedLog['changes_parsed'] as $key => $val)
                                            <tr class="hover:bg-stone-50 transition">
                                                <td class="p-2.5 font-mono font-bold text-stone-800 border-r border-stone-200 text-xs bg-stone-50/50">
                                                    {{ $key }}
                                                </td>
                                                <td class="p-2.5">
                                                    @if (is_null($val))
                                                        <span class="text-stone-400 italic text-[11px]">null</span>
                                                    @elseif (is_bool($val))
                                                        <span class="px-2 py-0.5 bg-blue-50 text-blue-800 border border-blue-200 rounded-md font-mono text-[11px] font-bold">
                                                            {{ $val ? 'true' : 'false' }}
                                                        </span>
                                                    @elseif (is_array($val))
                                                        <pre class="font-mono text-[11px] bg-stone-100 p-2 rounded-lg text-stone-900 overflow-x-auto whitespace-pre leading-relaxed">{{ json_encode($val, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                    @else
                                                        <span class="font-mono font-semibold text-stone-900 text-xs break-all">
                                                            {{ (string)$val }}
                                                        </span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-6 text-center bg-stone-50 border border-dashed border-stone-200 rounded-xl text-stone-500 font-medium">
                                Tidak ada rincian atribut spesifik yang tercatat untuk event ini.
                            </div>
                        @endif
                    </div>

                <!-- Tab 2: Context & Request Properties -->
                @elseif ($detailTab === 'properties')
                    <div class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-1">
                                <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">IP Address Klien</span>
                                <div class="font-mono text-xs text-stone-900 font-bold">
                                    {{ $selectedLog['ip_address'] ?: '127.0.0.1' }}
                                </div>
                            </div>

                            <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-1">
                                <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">Log Group / Channel</span>
                                <div class="font-mono text-xs text-stone-900 font-bold">
                                    {{ $selectedLog['log_name'] ?: 'default' }}
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-1">
                            <span class="text-[10px] font-bold text-stone-400 uppercase tracking-wider block">User Agent (Client Browser & OS)</span>
                            <div class="font-mono text-xs text-stone-900 break-all leading-relaxed font-semibold">
                                {{ $selectedLog['user_agent'] ?: 'Standard Web Browser' }}
                            </div>
                        </div>

                        @if (!empty($selectedLog['properties_parsed']) && is_array($selectedLog['properties_parsed']))
                            <div class="border border-stone-200 rounded-xl overflow-hidden shadow-2xs">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead class="bg-stone-100 text-stone-700 font-extrabold uppercase tracking-wider border-b border-stone-200">
                                        <tr>
                                            <th class="p-2.5 w-1/3 border-r border-stone-200">Kunci Properti</th>
                                            <th class="p-2.5">Nilai Konteks</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-stone-200 bg-white">
                                        @foreach ($selectedLog['properties_parsed'] as $pkey => $pval)
                                            <tr class="hover:bg-stone-50 transition">
                                                <td class="p-2.5 font-mono font-bold text-stone-800 border-r border-stone-200 text-xs bg-stone-50/50">
                                                    {{ $pkey }}
                                                </td>
                                                <td class="p-2.5 font-mono text-xs text-stone-900">
                                                    {{ is_array($pval) ? json_encode($pval, JSON_UNESCAPED_UNICODE) : (string)$pval }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                <!-- Tab 3: Raw JSON Tree -->
                @elseif ($detailTab === 'raw')
                    <div class="space-y-1">
                        <div class="p-4 bg-stone-950 text-emerald-400 rounded-xl font-mono text-xs overflow-x-auto whitespace-pre leading-relaxed border border-stone-800 shadow-inner max-h-72">
                            {{ json_encode($selectedLog, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}
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
