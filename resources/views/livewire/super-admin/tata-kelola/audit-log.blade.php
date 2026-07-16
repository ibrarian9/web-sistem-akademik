<div class="space-y-6">
    <div>
        <h2 class="text-xl font-bold text-white tracking-tight">Audit Log Aktivitas</h2>
        <p class="text-xs text-slate-500">Pantau seluruh riwayat aksi, perubahan data, alamat IP, dan aktivitas user pada sistem secara real-time.</p>
    </div>

    <!-- Table Section -->
    <div class="space-y-4">
        <!-- Filters -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2 w-full sm:max-w-xl">
                <!-- Search bar -->
                <div class="relative flex-1">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-500">
                        <x-lucide-search class="w-4 h-4" />
                    </span>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari aktivitas, nama user, atau IP..."
                        class="w-full pl-9 pr-4 py-2 bg-slate-900 border border-slate-800 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition duration-200 text-xs" />
                </div>
                
                <!-- Event selector -->
                <select wire:model.live="filterEvent" class="bg-slate-900 border border-slate-800 rounded-xl text-white text-xs px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
                    <option value="">Semua Event</option>
                    @foreach ($events as $evt)
                        <option value="{{ $evt }}">{{ ucfirst($evt) }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-center gap-2 shrink-0">
                <span class="text-xs text-slate-500">Tampilkan</span>
                <select wire:model.live="perPage" class="bg-slate-900 border border-slate-800 rounded-xl text-white text-xs px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/50">
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
                <th class="px-6 py-3.5">IP Address</th>
            </x-slot:thead>
            <x-slot:tbody>
                @forelse ($logs as $log)
                    <tr class="hover:bg-slate-905 transition-colors">
                        <td class="px-6 py-4 text-xs font-semibold text-slate-400">
                            {{ date('d-m-Y H:i:s', strtotime($log->created_at)) }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-white">{{ $log->causer_name ?? 'Sistem / Guest' }}</div>
                            @if ($log->causer_username)
                                <div class="text-[10px] text-slate-500">@ {{ $log->causer_username }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                {{ $log->event === 'created' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : '' }}
                                {{ $log->event === 'updated' ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : '' }}
                                {{ $log->event === 'deleted' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : '' }}
                                {{ !in_array($log->event, ['created', 'updated', 'deleted']) ? 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20' : '' }}
                            ">
                                {{ $log->event ?: 'log' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-slate-200 font-medium max-w-sm truncate" title="{{ $log->description }}">
                            {{ $log->description }}
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-400 text-xs">
                            {{ $log->ip_address ?: '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate-500 font-medium">
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
</div>
