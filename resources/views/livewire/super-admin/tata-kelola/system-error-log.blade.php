<div class="space-y-6 font-sans">
    <!-- Header Title Bar -->
    <x-page-header 
        title="System Error Log Viewer" 
        subtitle="Monitoring dan analisa berkas log error sistem (storage/logs/laravel.log) secara real-time."
        badge="SYSTEM HEALTH"
        badgeVariant="rose"
        icon="alert-triangle"
    >
        <x-slot:actions>
            <x-button type="button" variant="danger" size="md" icon="trash-2" wire:click="clearLog" data-confirm="Apakah Anda yakin ingin mengosongkan berkas log error sistem?">
                Bersihkan Log Error
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <!-- Info & Tutorial Box -->
    <x-info-tutorial-box 
        title="Petunjuk Monitoring Log Error Sistem"
        :steps="[
            ['title' => 'Monitoring Real-time', 'desc' => 'Tabel membaca langsung berkas laravel.log untuk mencatat exception, warning, dan critical runtime error.'],
            ['title' => 'Filter Severity Level', 'desc' => 'Gunakan filter level untuk memilah error berkategori ERROR, CRITICAL, WARNING, atau INFO.'],
            ['title' => 'Bersihkan Berkas', 'desc' => 'Gunakan tombol Bersihkan Log Error untuk mengosongkan berkas log setelah pemeliharaan sistem.']
        ]"
    />

    <!-- Alert Notifications -->
    @if (session()->has('success'))
        <x-alert-banner type="success" :message="session('success')" />
    @endif

    <!-- Metric Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Entri Log</span>
                <div class="p-2 bg-stone-100 text-stone-700 rounded-xl border border-stone-200">
                    <x-lucide-file-text class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-stone-900">{{ number_format($stats['total']) }} <span class="text-xs font-bold text-stone-400">Entri</span></div>
            <div class="text-[11px] text-stone-500 font-medium">Tercatat di laravel.log</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Errors</span>
                <div class="p-2 bg-rose-50 text-rose-700 rounded-xl border border-rose-200">
                    <x-lucide-alert-octagon class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-rose-600">{{ number_format($stats['error']) }} <span class="text-xs font-bold text-stone-400">Errors</span></div>
            <div class="text-[11px] text-stone-500 font-medium">Exception runtime</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Criticals</span>
                <div class="p-2 bg-purple-50 text-purple-700 rounded-xl border border-purple-200">
                    <x-lucide-shield-alert class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-purple-700">{{ number_format($stats['critical']) }} <span class="text-xs font-bold text-stone-400">Critical</span></div>
            <div class="text-[11px] text-stone-500 font-medium">Kegagalan tingkat kritis</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Warnings</span>
                <div class="p-2 bg-amber-50 text-amber-700 rounded-xl border border-amber-200">
                    <x-lucide-alert-triangle class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-amber-600">{{ number_format($stats['warning']) }} <span class="text-xs font-bold text-stone-400">Warnings</span></div>
            <div class="text-[11px] text-stone-500 font-medium">Peringatan sistem</div>
        </div>
    </div>

    <!-- Content Card -->
    <div class="bg-white border border-stone-200 rounded-2xl p-6 shadow-xs space-y-4">
        <!-- Controls: Search & Level Filter -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:max-w-xl">
                <div class="w-full flex-1">
                    <x-search-input wire:model.live.debounce.300ms="search" placeholder="Cari pesan error, file, atau timestamp..." />
                </div>

                <select wire:model.live="filterLevel" class="bg-white border border-stone-300 rounded-xl text-stone-900 text-xs font-bold px-3.5 py-2.5 focus:ring-2 focus:ring-emerald-600 shadow-2xs">
                    <option value="">Semua Level Log</option>
                    <option value="ERROR">ERROR</option>
                    <option value="CRITICAL">CRITICAL</option>
                    <option value="WARNING">WARNING</option>
                    <option value="INFO">INFO</option>
                    <option value="DEBUG">DEBUG</option>
                </select>
            </div>
        </div>

        <!-- Log Table -->
        <x-table loadingTarget="filterLevel, search">
            <thead class="bg-emerald-800 text-white font-extrabold uppercase tracking-wider border-b border-emerald-900">
                <tr>
                    <x-table.th class="w-44">Waktu</x-table.th>
                    <x-table.th class="w-28 text-center">Level</x-table.th>
                    <x-table.th class="min-w-[240px]">Pesan Error &amp; Trace Details</x-table.th>
                    <x-table.th align="center" class="w-28">Aksi</x-table.th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-200 bg-white" x-data="{ openTrace: null }">
                @forelse ($logs as $log)
                    <tr class="hover:bg-stone-50 transition cursor-pointer" wire:click="openErrorDetail({{ $log['id'] }})">
                        <td class="p-3.5 font-mono text-[11px] text-stone-600 border-r border-stone-200 whitespace-nowrap">
                            {{ $log['timestamp'] }}
                        </td>
                        <td class="p-3.5 text-center border-r border-stone-200">
                            @php
                                $lvl = $log['level'];
                                $badgeVariant = match ($lvl) {
                                    'CRITICAL', 'ALERT', 'EMERGENCY' => 'purple',
                                    'ERROR' => 'rose',
                                    'WARNING' => 'amber',
                                    default => 'stone',
                                };
                            @endphp
                            <x-badge :variant="$badgeVariant" size="xs">{{ $lvl }}</x-badge>
                        </td>
                        <td class="p-3.5 space-y-1.5 border-r border-stone-200">
                            <div class="font-extrabold text-rose-700 leading-snug font-mono text-xs break-all">
                                {{ $log['message'] }}
                            </div>

                            @if (!empty($log['trace']))
                                <div @click.stop>
                                    <button type="button" @click="openTrace = (openTrace === {{ $log['id'] }} ? null : {{ $log['id'] }})"
                                            class="text-[11px] font-bold text-stone-500 hover:text-stone-800 inline-flex items-center gap-1 cursor-pointer">
                                        <x-lucide-code class="w-3.5 h-3.5" />
                                        <span x-text="openTrace === {{ $log['id'] }} ? 'Sembunyikan Stack Trace' : 'Lihat Stack Trace'"></span>
                                    </button>

                                    <div x-show="openTrace === {{ $log['id'] }}" x-collapse class="mt-2 p-3 bg-stone-900 text-stone-200 rounded-xl font-mono text-[11px] overflow-x-auto whitespace-pre leading-relaxed border border-stone-800 shadow-inner">
                                        {{ $log['trace'] }}
                                    </div>
                                </div>
                            @endif
                        </td>
                        <td class="p-3.5 text-center" @click.stop>
                            <x-button type="button" variant="outline" size="xs" icon="eye" wire:click="openErrorDetail({{ $log['id'] }})">
                                Detail
                            </x-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-12 text-center text-stone-400">
                            <x-table.empty title="Tidak ada log error sistem ditemukan" subtitle="Sistem berjalan dengan lancar atau berkas log kosong." />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </x-table>

        @if ($logs->hasPages())
            <div class="pt-2">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    <!-- System Error Detail Modal -->
    <x-floating-card 
        :show="($showErrorModal && $selectedErrorLog) ? true : false"
        title="Detail Exception / System Error Log"
        :subtitle="'Entri #' . ($selectedErrorLog['id'] ?? '') . ' - ' . ($selectedErrorLog['timestamp'] ?? '')"
        badge="ERROR LOG"
        badgeVariant="rose"
        icon="alert-octagon"
        maxWidth="max-w-3xl"
        closeAction="closeErrorDetail"
    >
        @if ($selectedErrorLog)
            <div class="space-y-4 text-xs">
                <!-- Metadata -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-1">
                        <div class="text-[10px] uppercase font-bold text-stone-500">Timestamp Log</div>
                        <div class="font-mono font-bold text-stone-900">{{ $selectedErrorLog['timestamp'] }}</div>
                    </div>

                    <div class="p-3 bg-stone-50 border border-stone-200 rounded-xl space-y-1">
                        <div class="text-[10px] uppercase font-bold text-stone-500">Severity Level</div>
                        <div>
                            @php
                                $lvl = $selectedErrorLog['level'] ?? 'ERROR';
                                $badgeVariant = match ($lvl) {
                                    'CRITICAL', 'ALERT', 'EMERGENCY' => 'purple',
                                    'ERROR' => 'rose',
                                    'WARNING' => 'amber',
                                    default => 'stone',
                                };
                            @endphp
                            <x-badge :variant="$badgeVariant" size="xs">{{ $lvl }}</x-badge>
                        </div>
                    </div>
                </div>

                <!-- Main Exception Message -->
                <div class="space-y-1">
                    <div class="text-xs font-bold text-stone-800 uppercase flex items-center gap-1.5">
                        <x-lucide-alert-triangle class="w-4 h-4 text-rose-600" />
                        <span>Pesan Error Utama (Exception Message)</span>
                    </div>
                    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl font-mono text-xs text-rose-900 font-extrabold leading-relaxed break-all">
                        {{ $selectedErrorLog['message'] }}
                    </div>
                </div>

                <!-- Stack Trace Details -->
                @if (!empty($selectedErrorLog['trace']))
                    <div class="space-y-1" x-data="{ copied: false }">
                        <div class="flex items-center justify-between">
                            <div class="text-xs font-bold text-stone-800 uppercase flex items-center gap-1.5">
                                <x-lucide-code class="w-4 h-4 text-stone-600" />
                                <span>Full Exception Stack Trace</span>
                            </div>
                            <button type="button" 
                                    @click="navigator.clipboard.writeText($refs.traceContent.innerText); copied = true; setTimeout(() => copied = false, 2000)"
                                    class="px-2.5 py-1 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-lg text-[11px] font-bold border border-stone-300 transition flex items-center gap-1 cursor-pointer">
                                <x-lucide-copy class="w-3.5 h-3.5" />
                                <span x-text="copied ? 'Tercopy!' : 'Copy Stack Trace'"></span>
                            </button>
                        </div>
                        <div x-ref="traceContent" class="p-4 bg-stone-900 text-stone-200 rounded-xl font-mono text-xs overflow-x-auto whitespace-pre leading-relaxed border border-stone-800 shadow-inner max-h-72">
                            {{ $selectedErrorLog['trace'] }}
                        </div>
                    </div>
                @endif

                <div class="flex justify-end pt-3 border-t border-stone-200">
                    <x-button type="button" variant="secondary" size="md" wire:click="closeErrorDetail">
                        Tutup Detail
                    </x-button>
                </div>
            </div>
        @endif
    </x-floating-card>
</div>
