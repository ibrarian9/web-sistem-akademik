<!-- Filters & Search Toolbar -->
<div class="bg-white p-4 rounded-2xl border border-stone-200 shadow-sm flex flex-col md:flex-row items-center gap-3">
    <!-- Search -->
    <div class="relative flex-1 w-full">
        <x-lucide-search class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-stone-400" />
        <input type="text" wire:model.live.debounce.300ms="search"
               placeholder="Cari judul, staf pemohon, atau alasan..."
               class="w-full pl-10 pr-4 py-2 text-sm bg-stone-50 border border-stone-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-green-500" />
    </div>

    <!-- Filter Status -->
    <select wire:model.live="filterStatus"
            class="w-full md:w-44 px-3 py-2 text-sm bg-stone-50 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
        <option value="semua">Semua Status</option>
        <option value="menunggu">Menunggu Approval</option>
        <option value="disetujui">Disetujui</option>
        <option value="ditolak">Ditolak</option>
        <option value="dibatalkan">Dibatalkan</option>
    </select>

    <!-- Filter Tipe Aksi -->
    <select wire:model.live="filterTipe"
            class="w-full md:w-36 px-3 py-2 text-sm bg-stone-50 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
        <option value="semua">Semua Aksi</option>
        <option value="edit">Aksi Edit</option>
        <option value="hapus">Aksi Hapus</option>
    </select>

    <!-- Filter Fitur -->
    <select wire:model.live="filterFitur"
            class="w-full md:w-44 px-3 py-2 text-sm bg-stone-50 border border-stone-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500">
        <option value="semua">Semua Modul</option>
        <option value="tagihan">Tagihan Siswa</option>
        <option value="pembayaran">Pembayaran</option>
        <option value="tabungan">Tabungan Siswa</option>
        <option value="arus_kas">Arus Kas</option>
        <option value="dana_bos">Dana BOS</option>
        <option value="gaji_guru">Gaji Guru</option>
    </select>
</div>
