@props(['status'])

@php
    $classes = match (strtolower(str_replace(' ', '_', $status))) {
        'aktif', 'lunas', 'hadir', 'terkirim', 'berjalan' => 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20',
        'nonaktif', 'belum_bayar', 'tidak_hadir', 'gagal', 'keluar' => 'bg-rose-500/10 text-rose-400 border border-rose-500/20',
        'sebagian', 'izin', 'telat', 'pindah', 'draft' => 'bg-amber-500/10 text-amber-400 border border-amber-500/20',
        'lulus', 'naik_kelas' => 'bg-indigo-500/10 text-indigo-400 border border-indigo-500/20',
        default => 'bg-slate-500/10 text-slate-400 border border-slate-500/20',
    };

    $label = match (strtolower(str_replace(' ', '_', $status))) {
        'aktif' => 'Aktif',
        'nonaktif' => 'Nonaktif',
        'lunas' => 'Lunas',
        'belum_bayar' => 'Belum Lunas',
        'sebagian' => 'Sebagian',
        'hadir' => 'Hadir',
        'tidak_hadir' => 'Absen',
        'izin' => 'Izin',
        'telat' => 'Terlambat',
        'terkirim' => 'Terkirim',
        'gagal' => 'Gagal',
        'pending' => 'Pending',
        'berjalan' => 'Berjalan',
        'draft' => 'Draft',
        'lulus' => 'Lulus',
        'pindah' => 'Pindah',
        'keluar' => 'Keluar',
        'naik_kelas' => 'Naik Kelas',
        default => ucwords(str_replace('_', ' ', $status)),
    };
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold tracking-wide {{ $classes }}">
    <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
    <span>{{ $label }}</span>
</span>
