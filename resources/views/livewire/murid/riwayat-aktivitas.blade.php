<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h2 class="text-xl font-bold text-stone-800 tracking-tight">Riwayat Aktivitas</h2>
        <p class="text-sm text-stone-500 font-medium">Jejak aktivitas akademik, absensi, dan keuangan Anda yang tercatat oleh sistem.</p>
    </div>

    <!-- Timeline Wrapper Card -->
    <div class="bg-white border border-stone-200 rounded-2xl shadow-sm p-6 md:p-8">
        @if ($activities && $activities->count() > 0)
            <div class="relative pl-6 md:pl-8 space-y-8 before:absolute before:left-[19px] before:top-2 before:bottom-2 before:w-0.5 before:bg-stone-200">
                @foreach ($activities as $log)
                    @php
                        $formatted = $this->formatLog($log);
                    @endphp
                    <!-- Timeline Item -->
                    <div class="relative flex flex-col md:flex-row md:items-center gap-4 group">
                        <!-- Icon Circle -->
                        <div class="absolute -left-[32px] md:-left-[36px] w-9 h-9 rounded-xl border bg-white flex items-center justify-center shrink-0 shadow-sm z-10 {{ $formatted['color'] }}">
                            @switch($formatted['icon'])
                                @case('edit-3') <x-lucide-edit-3 class="w-4 h-4" /> @break
                                @case('calendar') <x-lucide-calendar class="w-4 h-4" /> @break
                                @case('credit-card') <x-lucide-credit-card class="w-4 h-4" /> @break
                                @case('file-text') <x-lucide-file-text class="w-4 h-4" /> @break
                                @case('award') <x-lucide-award class="w-4 h-4" /> @break
                                @case('layers') <x-lucide-layers class="w-4 h-4" /> @break
                                @default <x-lucide-activity class="w-4 h-4" />
                            @endswitch
                        </div>

                        <!-- Content details -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-stone-700 leading-relaxed">
                                {{ $formatted['text'] }}
                            </p>
                            <!-- Technical context description (fallback if available) -->
                            @if ($formatted['icon'] === 'activity' && $log->description)
                                <p class="text-xs text-stone-500 italic mt-0.5">
                                    Detail: {{ $log->description }}
                                </p>
                            @endif
                        </div>

                        <!-- Timestamp -->
                        <div class="md:w-36 text-left md:text-right shrink-0">
                            <span class="text-xs text-stone-400 font-bold block" title="{{ $log->created_at->format('d M Y, H:i') }}">
                                {{ $log->created_at->diffForHumans() }}
                            </span>
                            <span class="text-[10px] text-stone-300 font-medium block">
                                {{ $log->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination Footer -->
            @if ($activities->hasPages())
                <div class="pt-6 mt-6 border-t border-stone-200">
                    {{ $activities->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="py-16 text-center">
                <div class="w-12 h-12 rounded-full bg-stone-100 flex items-center justify-center mx-auto text-stone-400 mb-3">
                    <x-lucide-activity class="w-6 h-6" />
                </div>
                <h3 class="text-sm font-bold text-stone-700">Belum ada riwayat aktivitas</h3>
                <p class="text-xs text-stone-400 mt-1">Jejak aktivitas Anda yang terdaftar akan muncul di sini secara kronologis.</p>
            </div>
        @endif
    </div>
</div>
