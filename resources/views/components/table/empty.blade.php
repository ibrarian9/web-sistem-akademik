@props([
    'colspan' => 10,
    'title' => 'Tidak ada data ditemukan',
    'message' => 'Silakan sesuaikan kata kunci pencarian atau filter yang dipilih.',
    'icon' => 'inbox',
])

<tr>
    <td colspan="{{ $colspan }}" class="p-12 text-center bg-stone-50/50">
        <div class="flex flex-col items-center justify-center max-w-sm mx-auto space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-stone-100 border border-stone-200 flex items-center justify-center text-stone-400">
                <x-dynamic-component :component="'lucide-' . $icon" class="w-6 h-6" />
            </div>
            <div>
                <h4 class="text-sm font-bold text-stone-800">{{ $title }}</h4>
                <p class="text-xs text-stone-500 font-medium mt-1">{{ $message }}</p>
            </div>
            {{ $slot }}
        </div>
    </td>
</tr>
