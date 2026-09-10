<!-- Audit Log Detail Modal (Human Readable Inspector) -->
<x-floating-card 
    :show="($showDetailModal && $selectedLog) ? true : false"
    title="Rincian Audit Log Aktivitas"
    :subtitle="'Dicatat pada ' . (isset($selectedLog['created_at']) ? \Carbon\Carbon::parse($selectedLog['created_at'])->translatedFormat('l, d F Y - H:i:s') . ' WIB' : '-')"
    badge="LOG ACTIVITY"
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
                    <div class="text-[10px] uppercase font-bold text-stone-500">Pelaku Aktivitas</div>
                    <div class="font-bold text-stone-900 truncate">{{ $selectedLog['causer_name'] ?? 'Sistem' }}</div>
                    @if(!empty($selectedLog['causer_username']))
                        <div class="text-[10px] text-stone-500 font-mono">@ {{ $selectedLog['causer_username'] }}</div>
                    @endif
                </div>

                <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-1">
                    <div class="text-[10px] uppercase font-bold text-stone-500">Jenis Aksi</div>
                    <div>
                        @if (($selectedLog['event'] ?? '') === 'created')
                            <x-badge variant="emerald" size="xs">Penambahan Data</x-badge>
                        @elseif (($selectedLog['event'] ?? '') === 'updated')
                            <x-badge variant="blue" size="xs">Perubahan Data</x-badge>
                        @elseif (($selectedLog['event'] ?? '') === 'deleted')
                            <x-badge variant="rose" size="xs">Penghapusan Data</x-badge>
                        @elseif (($selectedLog['event'] ?? '') === 'login')
                            <x-badge variant="emerald" size="xs">Login Berhasil</x-badge>
                        @elseif (($selectedLog['event'] ?? '') === 'logout')
                            <x-badge variant="stone" size="xs">Logout</x-badge>
                        @elseif (($selectedLog['event'] ?? '') === 'failed_login')
                            <x-badge variant="rose" size="xs">Percobaan Login Gagal</x-badge>
                        @elseif (($selectedLog['event'] ?? '') === 'export')
                            <x-badge variant="purple" size="xs">Ekspor Berkas</x-badge>
                        @elseif (($selectedLog['event'] ?? '') === 'download')
                            <x-badge variant="amber" size="xs">Unduh Dokumen</x-badge>
                        @elseif (($selectedLog['event'] ?? '') === 'verify')
                            <x-badge variant="sky" size="xs">Verifikasi Dokumen</x-badge>
                        @else
                            <x-badge variant="stone" size="xs">{{ strtoupper($selectedLog['event'] ?? 'AKTIVITAS') }}</x-badge>
                        @endif
                    </div>
                </div>

                <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-1">
                    <div class="text-[10px] uppercase font-bold text-stone-500">Modul Terkait</div>
                    <div class="font-bold text-stone-900 truncate text-xs">
                        {{ $selectedLog['clean_model_name'] ?? 'Sistem' }}
                    </div>
                </div>

                <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-1">
                    <div class="text-[10px] uppercase font-bold text-stone-500">Waktu & Hari (WIB)</div>
                    <div class="font-bold text-stone-900 text-xs truncate">
                        {{ isset($selectedLog['created_at']) ? \Carbon\Carbon::parse($selectedLog['created_at'])->translatedFormat('l, d M Y') : '-' }}
                    </div>
                    <div class="font-mono text-[11px] text-stone-500 font-semibold">
                        {{ isset($selectedLog['created_at']) ? \Carbon\Carbon::parse($selectedLog['created_at'])->format('H:i:s') . ' WIB' : '-' }}
                    </div>
                </div>
            </div>

            <!-- Description Bar -->
            <div class="p-3.5 bg-emerald-50/70 border border-emerald-200 rounded-xl flex items-start gap-2.5">
                <x-lucide-info class="w-4 h-4 text-emerald-700 shrink-0 mt-0.5" />
                <div>
                    <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Ringkasan Aktivitas:</span>
                    <div class="text-xs font-bold text-emerald-950 leading-relaxed mt-0.5">
                        {{ $selectedLog['clean_description'] ?? $selectedLog['description'] }}
                    </div>
                </div>
            </div>

            <!-- Tab Switcher (Human-friendly Tabs, NO Raw JSON) -->
            <div class="border-b border-stone-200 flex items-center gap-2 pt-1">
                <button 
                    type="button" 
                    wire:click="$set('detailTab', 'diff')"
                    class="pb-2.5 px-3 text-xs font-bold transition border-b-2 flex items-center gap-1.5 cursor-pointer {{ $detailTab === 'diff' ? 'border-emerald-600 text-emerald-800' : 'border-transparent text-stone-500 hover:text-stone-800' }}"
                >
                    <x-lucide-file-text class="w-3.5 h-3.5" />
                    <span>Rincian Data yang Tercatat</span>
                    @if (!empty($selectedLog['changes_formatted']) && is_array($selectedLog['changes_formatted']))
                        <span class="px-1.5 py-0.2 bg-emerald-100 text-emerald-800 rounded-full text-[10px] font-black">
                            {{ count($selectedLog['changes_formatted']) }}
                        </span>
                    @endif
                </button>

                <button 
                    type="button" 
                    wire:click="$set('detailTab', 'properties')"
                    class="pb-2.5 px-3 text-xs font-bold transition border-b-2 flex items-center gap-1.5 cursor-pointer {{ $detailTab === 'properties' ? 'border-emerald-600 text-emerald-800' : 'border-transparent text-stone-500 hover:text-stone-800' }}"
                >
                    <x-lucide-shield-check class="w-3.5 h-3.5" />
                    <span>Informasi Perangkat & Sesi</span>
                </button>
            </div>

            <!-- Tab 1: Human-Readable Data Attributes (No JSON, No Raw IDs) -->
            @if ($detailTab === 'diff')
                <div class="space-y-2">
                    @if (!empty($selectedLog['changes_formatted']) && count($selectedLog['changes_formatted']) > 0)
                        <div class="border border-stone-200 rounded-xl overflow-hidden shadow-2xs">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead class="bg-stone-100 text-stone-700 font-extrabold uppercase tracking-wider border-b border-stone-200">
                                    <tr>
                                        <th class="p-2.5 w-2/5 border-r border-stone-200">Keterangan Informasi</th>
                                        <th class="p-2.5">Data Tercatat</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stone-200 bg-white">
                                    @foreach ($selectedLog['changes_formatted'] as $item)
                                        <tr class="hover:bg-stone-50 transition">
                                            <td class="p-2.5 font-bold text-stone-800 border-r border-stone-200 text-xs bg-stone-50/60">
                                                {{ $item['label'] }}
                                            </td>
                                            <td class="p-2.5">
                                                @if ($item['type'] === 'currency')
                                                    <span class="font-bold text-emerald-900 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200 font-mono text-xs">
                                                        {{ $item['value'] }}
                                                    </span>
                                                @elseif ($item['type'] === 'badge')
                                                    <x-badge :variant="$item['badge_variant'] ?? 'emerald'" size="xs">
                                                        {{ $item['value'] }}
                                                    </x-badge>
                                                @elseif ($item['type'] === 'text_highlight')
                                                    <span class="font-bold text-stone-900 bg-stone-100 px-2 py-0.5 rounded-md border border-stone-200 text-xs inline-block">
                                                        {{ $item['value'] }}
                                                    </span>
                                                @elseif ($item['type'] === 'list')
                                                    <div class="space-y-1 py-0.5">
                                                        @foreach ($item['sub_items'] ?? [] as $sub)
                                                            <div class="flex items-center gap-1.5 text-stone-800 font-medium">
                                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                                                <span>{{ $sub }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="font-semibold text-stone-900 text-xs break-words">
                                                        {{ $item['value'] }}
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
                            Tidak ada rincian data spesifik yang dimodifikasi pada aksi ini.
                        </div>
                    @endif
                </div>

            <!-- Tab 2: Clean Context & Client Properties -->
            @elseif ($detailTab === 'properties')
                <div class="space-y-3">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-1">
                            <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Alamat IP Pengguna</span>
                            <div class="font-mono text-xs text-stone-900 font-bold flex items-center gap-1.5">
                                <x-lucide-globe class="w-3.5 h-3.5 text-stone-400 shrink-0" />
                                <span>{{ $selectedLog['ip_address'] ?: '127.0.0.1' }}</span>
                            </div>
                        </div>

                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-1">
                            <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Kategori Log</span>
                            <div class="text-xs text-stone-900 font-bold capitalize flex items-center gap-1.5">
                                <x-lucide-tag class="w-3.5 h-3.5 text-stone-400 shrink-0" />
                                <span>{{ $selectedLog['log_name'] ?: 'Umum' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-1">
                        <span class="text-[10px] font-bold text-stone-500 uppercase tracking-wider block">Perangkat & Browser Pengguna</span>
                        <div class="text-xs text-stone-900 font-bold flex items-center gap-1.5">
                            <x-lucide-monitor class="w-4 h-4 text-emerald-600 shrink-0" />
                            <span>{{ $selectedLog['user_agent_info']['short'] ?? 'Web Browser' }}</span>
                        </div>
                        <div class="text-[11px] text-stone-500 font-medium mt-0.5">
                            {{ $selectedLog['user_agent_info']['browser'] ?? 'Browser' }} pada {{ $selectedLog['user_agent_info']['platform'] ?? 'Komputer' }} ({{ $selectedLog['user_agent_info']['device'] ?? 'Desktop' }})
                        </div>
                    </div>

                    @if (!empty($selectedLog['properties_formatted']) && count($selectedLog['properties_formatted']) > 0)
                        <div class="border border-stone-200 rounded-xl overflow-hidden shadow-2xs">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead class="bg-stone-100 text-stone-700 font-extrabold uppercase tracking-wider border-b border-stone-200">
                                    <tr>
                                        <th class="p-2.5 w-2/5 border-r border-stone-200">Parameter Tambahan</th>
                                        <th class="p-2.5">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-stone-200 bg-white">
                                    @foreach ($selectedLog['properties_formatted'] as $prop)
                                        <tr class="hover:bg-stone-50 transition">
                                            <td class="p-2.5 font-bold text-stone-800 border-r border-stone-200 text-xs bg-stone-50/60">
                                                {{ $prop['label'] }}
                                            </td>
                                            <td class="p-2.5 font-semibold text-stone-900 text-xs">
                                                {{ $prop['value'] }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
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
