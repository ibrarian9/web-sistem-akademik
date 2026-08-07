# Standar Desain UI Komponen (Buttons, Cards, Modals & Alerts) — SIAKAD

Dokumen ini berisi pedoman penyeragaman antarmuka pengguna (*Design Tokens & UI Component Standardization*) untuk seluruh modul Sistem Informasi Akademik & Keuangan Yayasan.

---

## 1. Standar Desain Kartu (Cards)

Seluruh kontainer kartu di dalam aplikasi harus menggunakan kelas Tailwind CSS standar berikut:

### **A. Primary Content Card**
- **Classes**: `bg-white border border-stone-200 rounded-2xl p-6 shadow-sm`
- **Kegunaan**: Kontainer utama halaman, tabel data, form input, dan panel ringkasan.

### **B. Hero / Header Banner Card**
- **Classes**: `bg-white border border-stone-200 p-6 md:p-8 rounded-2xl shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6`
- **Kegunaan**: Header utama di bagian atas halaman yang memuat judul modul, deskripsi singkat, dan filter utama.

### **C. Nested Sub-Card / Inner Box**
- **Classes**: `bg-stone-50 border border-stone-200 rounded-xl p-4 space-y-2`
- **Kegunaan**: Kartu sekunder di dalam kartu utama (contoh: simulasi preview, filter tambahan, item riwayat).

---

## 2. Standar Desain Tombol (Buttons)

Seluruh tombol tindakan di dalam aplikasi harus menggunakan ukuran dan warna terstandar berikut:

| Kategori Tombol | Tailwind CSS Classes | Contoh Penggunaan |
| :--- | :--- | :--- |
| **Primary (Utama)** | `bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-5 py-2.5 rounded-xl text-xs transition shadow-sm flex items-center gap-2` | Simpan Data, Hitung Rapor, Terbitkan |
| **Secondary (Sekunder)** | `bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold px-4 py-2.5 rounded-xl text-xs border border-stone-300 transition flex items-center gap-2` | Batal, Reset, Filter, Kembali |
| **Warning / Edit** | `bg-amber-50 hover:bg-amber-100 text-amber-800 font-bold px-3.5 py-2 rounded-xl text-xs border border-amber-200 transition flex items-center gap-1.5` | Edit Bab, Sunting Nilai, Koreksi |
| **Danger / Hapus** | `bg-rose-50 hover:bg-rose-100 text-rose-800 font-bold px-3.5 py-2 rounded-xl text-xs border border-rose-200 transition flex items-center gap-1.5` | Hapus Data, Pembatalan (Void) |
| **Info / Action** | `bg-cyan-700 hover:bg-cyan-800 text-white font-bold px-5 py-2.5 rounded-xl text-xs transition shadow-sm flex items-center gap-2` | Penilaian P5, Cetak PDF |

---

## 3. Integrasi MicroModal.js untuk Alert & Konfirmasi Dialog

Untuk konfirmasi penghapusan data, peringatan bahaya, dan dialog modal yang ramah aksesibilitas (WCAG AA), aplikasi menggunakan **MicroModal.js**.

### **A. Struktur HTML MicroModal (`resources/views/components/layouts/app.blade.php`)**

```html
<!-- Modal Alert / Confirm Standard (MicroModal.js) -->
<div class="modal micromodal-slide" id="modal-alert" aria-hidden="true">
    <div class="modal__overlay fixed inset-0 bg-stone-900/60 backdrop-blur-xs z-50 flex items-center justify-center p-4" tabindex="-1" data-micromodal-close>
        <div class="modal__container bg-white border border-stone-200 rounded-3xl p-6 shadow-2xl max-w-md w-full space-y-4" role="dialog" aria-modal="true" aria-labelledby="modal-alert-title">
            <header class="flex items-center justify-between border-b border-stone-200 pb-3">
                <h2 class="text-sm font-extrabold text-stone-900 uppercase tracking-wider" id="modal-alert-title">
                    Konfirmasi Tindakan
                </h2>
                <button class="modal__close p-1 rounded-lg text-stone-400 hover:text-stone-700 hover:bg-stone-100" aria-label="Close modal" data-micromodal-close>
                    ✕
                </button>
            </header>
            <main class="text-xs text-stone-700 leading-relaxed font-medium" id="modal-alert-content">
                Apakah Anda yakin ingin melakukan tindakan ini?
            </main>
            <footer class="flex items-center justify-end gap-2 pt-2 border-t border-stone-200">
                <button class="px-4 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 rounded-xl text-xs font-bold" data-micromodal-close>
                    Batal
                </button>
                <button id="modal-alert-confirm-btn" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm">
                    Lanjutkan
                </button>
            </footer>
        </div>
    </div>
</div>
```

### **B. Cara Penggunaan di JavaScript / Alpine.js**

```javascript
// Membuka modal konfirmasi dengan MicroModal
window.showAlertModal = function(title, message, onConfirmCallback) {
    document.getElementById('modal-alert-title').innerText = title;
    document.getElementById('modal-alert-content').innerText = message;
    
    const confirmBtn = document.getElementById('modal-alert-confirm-btn');
    confirmBtn.onclick = function() {
        if (typeof onConfirmCallback === 'function') {
            onConfirmCallback();
        }
        MicroModal.close('modal-alert');
    };

    MicroModal.show('modal-alert');
};
```

---

## 4. Standar Warna Status (Status Badges)

- **Emerald / Green (`bg-emerald-100 border-emerald-300 text-emerald-800`)**: Status Lunas, Hadir, Terbit, Sah & Terverifikasi.
- **Amber / Yellow (`bg-amber-100 border-amber-300 text-amber-900`)**: Status Sebagian, Izin, Mode Edit, Menunggu Persetujuan.
- **Rose / Red (`bg-rose-100 border-rose-300 text-rose-900`)**: Status Tunggakan, Belum Bayar, Alpa, Ditolak, Terblokir.
- **Cyan / Blue (`bg-cyan-100 border-cyan-300 text-cyan-900`)**: Status Kokurikuler P5, Informasi Sistem.
