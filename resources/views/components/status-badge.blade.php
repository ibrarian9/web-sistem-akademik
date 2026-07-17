@props(['status'])

@php
    $classes = match (strtolower(str_replace(' ', '_', $status))) {
        'aktif', 'lunas', 'hadir', 'terkirim', 'berjalan' => 'bg-green-50 text-green-700 border border-green-200',
        'nonaktif', 'belum_bayar', 'tidak_hadir', 'gagal', 'keluar' => 'bg-red-50 text-red-700 border border-red-200',
        'sebagian', 'izin', 'telat', 'pindah', 'draft' => 'bg-amber-50 text-amber-700 border border-amber-200',
        'lulus', 'naik_kelas' => 'bg-blue-50 text-blue-700 border border-blue-200',
        default => 'bg-stone-100 text-stone-600 border border-stone-200',
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
