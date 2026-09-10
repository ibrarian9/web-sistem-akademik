<!-- Table -->
<x-table loadingTarget="filterEvent, filterRole, filterPeriode, perPage, search">
    <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
        <tr>
            <x-table.th class="w-52">Hari & Waktu</x-table.th>
            <x-table.th class="min-w-[180px]">Nama User (Pelaku & Role)</x-table.th>
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
                    <div class="font-extrabold text-stone-900 text-xs">{{ $log->causer_name ?? 'Sistem' }}</div>
                    <div class="flex items-center gap-1.5 mt-0.5 flex-wrap">
                        @if ($log->causer_username)
                            <span class="text-[10px] text-stone-500 font-mono">@ {{ $log->causer_username }}</span>
                        @endif
                        @if (!empty($log->causer_role))
                            <span class="inline-block px-1.5 py-0.2 rounded text-[9px] font-black uppercase bg-stone-100 text-stone-700 border border-stone-200">
                                {{ str_replace('_', ' ', $log->causer_role) }}
                            </span>
                        @endif
                    </div>
                </td>
                <td class="p-3.5 text-center border-r border-stone-200">
                    @if ($log->event === 'created')
                        <x-badge variant="emerald" size="xs">Created</x-badge>
                    @elseif ($log->event === 'updated')
                        <x-badge variant="sky" size="xs">Updated</x-badge>
                    @elseif ($log->event === 'deleted')
                        <x-badge variant="rose" size="xs">Deleted</x-badge>
                    @elseif ($log->event === 'login')
                        <x-badge variant="emerald" size="xs">Login</x-badge>
                    @elseif ($log->event === 'logout')
                        <x-badge variant="stone" size="xs">Logout</x-badge>
                    @elseif ($log->event === 'failed_login')
                        <x-badge variant="rose" size="xs">Failed Login</x-badge>
                    @elseif ($log->event === 'export')
                        <x-badge variant="purple" size="xs">Export</x-badge>
                    @elseif ($log->event === 'download')
                        <x-badge variant="amber" size="xs">Download</x-badge>
                    @elseif ($log->event === 'verify')
                        <x-badge variant="sky" size="xs">Verify</x-badge>
                    @else
                        <x-badge variant="stone" size="xs">{{ ucfirst($log->event ?: 'log') }}</x-badge>
                    @endif
                </td>
                <td class="p-3.5 text-stone-800 font-semibold max-w-sm truncate border-r border-stone-200" title="{{ $log->clean_description ?? $log->description }}">
                    {{ $log->clean_description ?? $log->description }}
                </td>
                <td class="p-3.5 border-r border-stone-200">
                    <div class="font-bold text-stone-900 text-xs flex items-center gap-1.5">
                        <x-lucide-monitor class="w-3.5 h-3.5 text-stone-400 shrink-0" />
                        <span>{{ $log->user_agent_info['short'] ?? 'Web Browser' }}</span>
                    </div>
                    <div class="text-[11px] text-stone-500 font-mono mt-0.5 font-semibold">
                        IP: {{ $log->ip_address ?: '127.0.0.1' }}
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
