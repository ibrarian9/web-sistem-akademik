<div class="space-y-6 font-sans">
    <!-- Header Page -->
    <div class="bg-white border border-stone-200 p-6 rounded-2xl shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <span class="px-3 py-1 bg-rose-100 border border-rose-300 text-rose-800 rounded-full text-xs font-bold uppercase tracking-wider inline-block">
                SD Tahfizh F3 System Health
            </span>
            <h2 class="text-xl font-extrabold text-stone-900 tracking-tight mt-1 flex items-center gap-2">
                <x-lucide-alert-triangle class="w-5 h-5 text-rose-600 shrink-0" />
                <span>System Error Log Viewer</span>
            </h2>
            <p class="text-xs text-stone-500 font-medium">Monitoring dan analisa berkas log error sistem (storage/logs/laravel.log) secara real-time.</p>
        </div>

        <div class="flex items-center gap-2">
            <button type="button" wire:click="clearLog" 
                    wire:confirm="Apakah Anda yakin ingin mengosongkan berkas log error sistem?"
                    class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-extrabold text-xs inline-flex items-center gap-2 shadow-sm transition">
                <x-lucide-trash-2 class="w-4 h-4" />
                Bersihkan Log Error
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if (session()->has('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between">
            <div class="flex items-center gap-2 text-xs font-bold">
                <x-lucide-check-circle class="w-4 h-4 text-emerald-600" />
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>
    @endif

    <!-- Metric Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Total Entri Log</span>
                <div class="p-2 bg-stone-100 text-stone-700 rounded-xl border border-stone-200">
                    <x-lucide-file-text class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-stone-900">{{ number_format($stats['total']) }} <span class="text-xs font-bold text-stone-400">Entri</span></div>
            <div class="text-[11px] text-stone-500 font-medium">Tercatat di laravel.log</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Errors</span>
                <div class="p-2 bg-rose-50 text-rose-700 rounded-xl border border-rose-200">
                    <x-lucide-alert-octagon class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-rose-600">{{ number_format($stats['error']) }} <span class="text-xs font-bold text-stone-400">Errors</span></div>
            <div class="text-[11px] text-stone-500 font-medium">Exception runtime</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-stone-500 uppercase tracking-wider">Criticals</span>
                <div class="p-2 bg-purple-50 text-purple-700 rounded-xl border border-purple-200">
                    <x-lucide-shield-alert class="w-4 h-4" />
                </div>
            </div>
            <div class="text-2xl font-black text-purple-700">{{ number_format($stats['critical']) }} <span class="text-xs font-bold text-stone-400">Critical</span></div>
            <div class="text-[11px] text-stone-500 font-medium">Kegagalan tingkat kritis</div>
        </div>

        <div class="bg-white border border-stone-200 rounded-2xl p-5 shadow-sm space-y-2">
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

    <!-- Controls: Search & Level Filter -->
    <div class="bg-white border border-stone-200 p-4 rounded-2xl shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full md:w-auto flex-1">
            <div class="relative w-full sm:w-80">
                <x-lucide-search class="w-4 h-4 text-stone-400 absolute left-3.5 top-3" />
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari pesan error, file, atau timestamp..." 
                       class="w-full pl-10 pr-4 py-2 bg-stone-50 border border-stone-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-rose-500 focus:bg-white transition" />
            </div>

            <select wire:model.live="filterLevel" class="w-full sm:w-48 py-2 px-3 bg-stone-50 border border-stone-200 rounded-xl text-xs font-medium focus:ring-2 focus:ring-rose-500 focus:bg-white transition">
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
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-stone-700">
                <thead class="bg-stone-50 border-b border-stone-200 text-stone-500 font-extrabold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3.5 px-4 w-44">Waktu</th>
                        <th class="py-3.5 px-4 w-28 text-center">Level</th>
                        <th class="py-3.5 px-4">Pesan Error &amp; Trace Details</th>
                        <th class="py-3.5 px-4 w-28 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 font-medium" x-data="{ openTrace: null }">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-stone-50/80 transition duration-150 cursor-pointer" wire:click="openErrorDetail({{ $log['id'] }})">
                            <td class="py-3.5 px-4 font-mono text-[11px] text-stone-500 whitespace-nowrap">
                                {{ $log['timestamp'] }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @php
                                    $lvl = $log['level'];
                                    $badgeClass = match ($lvl) {
                                        'CRITICAL', 'ALERT', 'EMERGENCY' => 'bg-purple-100 text-purple-900 border-purple-300',
                                        'ERROR' => 'bg-rose-100 text-rose-900 border-rose-300',
                                        'WARNING' => 'bg-amber-100 text-amber-900 border-amber-300',
                                        default => 'bg-stone-100 text-stone-800 border-stone-300',
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 border rounded-full font-black text-[10px] uppercase inline-block {{ $badgeClass }}">
                                    {{ $lvl }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 space-y-1.5">
                                <div class="font-extrabold text-stone-900 leading-snug font-mono text-xs text-rose-700 break-all">
                                    {{ $log['message'] }}
                                </div>

                                @if (!empty($log['trace']))
                                    <div @click.stop>
                                        <button type="button" @click="openTrace = (openTrace === {{ $log['id'] }} ? null : {{ $log['id'] }})"
                                                class="text-[11px] font-bold text-stone-500 hover:text-stone-800 inline-flex items-center gap-1">
                                            <x-lucide-code class="w-3.5 h-3.5" />
                                            <span x-text="openTrace === {{ $log['id'] }} ? 'Sembunyikan Stack Trace' : 'Lihat Stack Trace'"></span>
                                        </button>

                                        <div x-show="openTrace === {{ $log['id'] }}" x-collapse class="mt-2 p-3 bg-stone-900 text-stone-200 rounded-xl font-mono text-[11px] overflow-x-auto whitespace-pre leading-relaxed border border-stone-800 shadow-inner">
                                            {{ $log['trace'] }}
                                        </div>
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center" @click.stop>
                                <button type="button" wire:click="openErrorDetail({{ $log['id'] }})"
                                        class="px-3 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5 mx-auto">
                                    <x-lucide-eye class="w-3.5 h-3.5" />
                                    <span>Detail</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-10 text-center text-stone-400 font-medium">
                                <x-lucide-check-circle-2 class="w-10 h-10 mx-auto mb-2 text-emerald-500 opacity-60" />
                                <div class="font-bold text-stone-700 text-sm">Tidak Ada Log Error Ditemukan</div>
                                <p class="text-xs text-stone-400 mt-1">Sistem berjalan dengan bersih atau berkas laravel.log masih kosong.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="p-4 border-t border-stone-200 bg-stone-50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    <!-- System Error Detail Modal -->
    @if ($showErrorModal && $selectedErrorLog)
        <div class="fixed inset-0 z-[99990] flex items-center justify-center bg-stone-950/65 backdrop-blur-xs p-4 sm:p-6 pt-20 sm:pt-8 pb-8 overflow-y-auto">
            <div class="bg-white border border-stone-200 rounded-none max-w-3xl w-full shadow-2xl overflow-hidden relative animate-[fadeIn_0.2s_ease-out]">
                <!-- Top Accent Line -->
                <div class="h-1.5 bg-rose-600"></div>

                <div class="p-6 space-y-5 max-h-[85vh] overflow-y-auto">
                    <!-- Header -->
                    <div class="flex items-start justify-between gap-4 border-b border-stone-200 pb-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                @php
                                    $lvl = $selectedErrorLog['level'];
                                    $badgeClass = match ($lvl) {
                                        'CRITICAL', 'ALERT', 'EMERGENCY' => 'bg-purple-100 text-purple-900 border-purple-300',
                                        'ERROR' => 'bg-rose-100 text-rose-900 border-rose-300',
                                        'WARNING' => 'bg-amber-100 text-amber-900 border-amber-300',
                                        default => 'bg-stone-100 text-stone-800 border-stone-300',
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-none font-black text-[10px] uppercase border {{ $badgeClass }}">
                                    {{ $lvl }}
                                </span>
                                <span class="text-xs text-stone-500 font-bold">Log Entri #{{ $selectedErrorLog['id'] }}</span>
                            </div>
                            <h3 class="text-lg font-black text-stone-900 tracking-tight flex items-center gap-2">
                                <x-lucide-alert-octagon class="w-5 h-5 text-rose-600 shrink-0" />
                                <span>Detail Exception / System Error Log</span>
                            </h3>
                        </div>

                        <button type="button" wire:click="closeErrorDetail" class="p-1.5 rounded-none text-stone-400 hover:text-stone-800 hover:bg-stone-100 transition text-sm font-bold">
                            ✕
                        </button>
                    </div>

                    <!-- Metadata -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-none space-y-1">
                            <div class="text-[10px] uppercase font-bold text-stone-400">Timestamp Log</div>
                            <div class="font-mono font-bold text-stone-900">{{ $selectedErrorLog['timestamp'] }}</div>
                        </div>

                        <div class="p-3 bg-stone-50 border border-stone-200 rounded-none space-y-1">
                            <div class="text-[10px] uppercase font-bold text-stone-400">Severity Level</div>
                            <div class="font-bold text-rose-700 uppercase">{{ $selectedErrorLog['level'] }}</div>
                        </div>
                    </div>

                    <!-- Main Exception Message -->
                    <div class="space-y-1.5">
                        <div class="text-xs font-bold text-stone-800 flex items-center gap-1.5">
                            <x-lucide-alert-triangle class="w-4 h-4 text-rose-600" />
                            <span>Pesan Error Utama (Exception Message)</span>
                        </div>
                        <div class="p-4 bg-rose-50 border border-rose-200 rounded-none font-mono text-xs text-rose-900 font-extrabold leading-relaxed break-all">
                            {{ $selectedErrorLog['message'] }}
                        </div>
                    </div>

                    <!-- Stack Trace Details -->
                    @if (!empty($selectedErrorLog['trace']))
                        <div class="space-y-1.5" x-data="{ copied: false }">
                            <div class="flex items-center justify-between">
                                <div class="text-xs font-bold text-stone-800 flex items-center gap-1.5">
                                    <x-lucide-code class="w-4 h-4 text-stone-600" />
                                    <span>Full Exception Stack Trace</span>
                                </div>
                                <button type="button" 
                                        @click="navigator.clipboard.writeText($refs.traceContent.innerText); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="px-2.5 py-1 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-none text-[11px] font-bold border border-stone-300 transition flex items-center gap-1">
                                    <x-lucide-copy class="w-3.5 h-3.5" />
                                    <span x-text="copied ? 'Tercopy!' : 'Copy Stack Trace'"></span>
                                </button>
                            </div>
                            <div x-ref="traceContent" class="p-4 bg-stone-900 text-stone-200 rounded-none font-mono text-xs overflow-x-auto whitespace-pre leading-relaxed border border-stone-800 shadow-inner max-h-72">
                                {{ $selectedErrorLog['trace'] }}
                            </div>
                        </div>
                    @endif

                    <!-- Raw Content if available -->
                    @if (!empty($selectedErrorLog['raw']) && empty($selectedErrorLog['trace']))
                        <div class="space-y-1.5">
                            <div class="text-xs font-bold text-stone-800">Raw Log Content</div>
                            <div class="p-4 bg-stone-900 text-amber-300 rounded-none font-mono text-xs overflow-x-auto whitespace-pre leading-relaxed border border-stone-800 shadow-inner max-h-72">
                                {{ $selectedErrorLog['raw'] }}
                            </div>
                        </div>
                    @endif
                </div>

                <div class="px-6 py-4 bg-stone-50 border-t border-stone-200 flex justify-end">
                    <button type="button" wire:click="closeErrorDetail" class="px-5 py-2 bg-stone-800 hover:bg-stone-900 text-white rounded-none text-xs font-bold transition">
                        Tutup Detail
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
