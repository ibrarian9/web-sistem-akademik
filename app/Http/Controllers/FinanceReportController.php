<?php

namespace App\Http\Controllers;

use App\Models\DanaBos;
use App\Models\GajiGuru;
use App\Models\JenisTagihan;
use App\Models\KategoriPengeluaran;
use App\Models\Kelas;
use App\Models\Pembayaran;
use App\Models\Tabungan;
use App\Models\TahunAjaran;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\Finance\FinanceReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class FinanceReportController extends Controller
{
    public function slipGaji(Request $request, int $id)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $gaji = GajiGuru::with('guru.user')->findOrFail($id);
        $user = auth()->user();
        $userRole = $user->role->nama ?? '';

        // Izinkan Finance, Super Admin, Kepala Sekolah, atau Guru pemilik slip gaji ini
        $isOwnSlip = false;
        if ($userRole === 'guru' && $user->guru) {
            $isOwnSlip = ($user->guru->id === $gaji->guru_id);
        }

        if (!in_array($userRole, ['finance', 'super_admin', 'super_admin_2', 'kepala_sekolah']) && !$isOwnSlip) {
            abort(403, 'Anda tidak memiliki akses untuk melihat slip gaji ini.');
        }

        AuditLogger::log('download', "Mengunduh/melihat slip gaji guru: " . ($gaji->guru->user->nama ?? 'Guru') . " ({$gaji->bulan} {$gaji->tahun})", $gaji, [
            'log_name' => 'keuangan',
            'properties' => ['gaji_id' => $gaji->id, 'total_diterima' => $gaji->total_diterima],
        ]);

        $school = FinanceReportService::getSchoolHeader();
        $terbilangText = self::terbilang($gaji->total_diterima) . ' Rupiah';

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-slip-gaji', [
            'gaji' => $gaji,
            'namaSekolah' => $school['namaSekolah'],
            'alamatSekolah' => $school['alamatSekolah'],
            'noTelepon' => $school['noTelepon'],
            'terbilang' => $terbilangText,
        ]);

        $filename = 'slip_gaji_' . str_replace(' ', '_', strtolower($gaji->guru->user->nama ?? 'guru')) . '_' . strtolower($gaji->bulan) . '_' . $gaji->tahun . '.pdf';

        return FinanceReportService::renderPdf($pdf, $filename, $request->query('download') === '1' || request('download') === '1');
    }

    public function bulkSlipGaji(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $user = auth()->user();
        $userRole = $user->role->nama ?? '';

        if (!in_array($userRole, ['finance', 'super_admin', 'super_admin_2', 'kepala_sekolah'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk mengunduh slip gaji massal.');
        }

        $salaries = FinanceReportService::getRekapGajiData($request->all());

        if ($salaries->isEmpty()) {
            abort(404, 'Tidak ada data slip gaji yang ditemukan untuk kriteria ini.');
        }

        AuditLogger::log('download', "Mengunduh slip gaji massal ({$salaries->count()} guru) periode " . ($request->bulan ?? '') . ' ' . ($request->tahun ?? date('Y')), null, [
            'log_name' => 'keuangan',
            'properties' => ['count' => $salaries->count(), 'filter' => $request->all()],
        ]);

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-bulk-slip-gaji', [
            'salaries' => $salaries,
            'bulan' => $request->bulan ?? '',
            'tahun' => $request->tahun ?? '',
        ]);

        $periodLabel = $request->bulan ? str_replace(' ', '_', strtolower($request->bulan)) . '_' : '';
        $yearLabel = $request->tahun ?? date('Y');
        $filename = 'bulk_slip_gaji_' . $periodLabel . $yearLabel . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, ['Content-Type' => 'application/pdf']);
    }

    public function cetakResi(Request $request, int $id)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $pembayaran = Pembayaran::with(['tagihan.siswa.user', 'tagihan.siswa.kelas', 'tagihan.jenisTagihan', 'petugas'])->findOrFail($id);
        $user = auth()->user();
        $userRole = $user->role->nama ?? '';

        // Izinkan Finance, Super Admin, TU, Kepsek, atau Murid/Wali pemilik tagihan ini
        $isOwnReceipt = false;
        if ($userRole === 'murid' && $user->siswa) {
            $isOwnReceipt = ($pembayaran->tagihan && $pembayaran->tagihan->siswa_id === $user->siswa->id);
        }

        if (!in_array($userRole, ['finance', 'super_admin', 'super_admin_2', 'tata_usaha', 'kepala_sekolah']) && !$isOwnReceipt) {
            abort(403, 'Anda tidak memiliki akses untuk melihat resi ini.');
        }

        $siswa = $pembayaran->tagihan->siswa ?? null;
        AuditLogger::log('download', "Mencetak/melihat kwitansi pembayaran #{$pembayaran->no_resi} (" . ($siswa->user->nama ?? 'Siswa') . " - Rp " . number_format($pembayaran->nominal_dibayar, 0, ',', '.') . ")", $pembayaran, [
            'log_name' => 'keuangan',
            'siswa_id' => $siswa?->id,
            'properties' => ['no_resi' => $pembayaran->no_resi, 'nominal' => $pembayaran->nominal_dibayar],
        ]);

        $staffFinance = User::whereHas('role', function ($q) {
            $q->where('nama', 'finance');
        })->first();

        // 1. If user explicitly requests direct PDF download
        if ($request->query('download') === '1' || request('download') === '1') {
            $pdf = Pdf::loadView('livewire.shared.laporan.pdf-resi-pembayaran', [
                'pembayaran' => $pembayaran,
                'staffFinance' => $pembayaran->petugas ?? $staffFinance,
            ]);

            return response()->streamDownload(function () use ($pdf) {
                echo $pdf->output();
            }, 'resi_pembayaran_' . $pembayaran->id . '.pdf', ['Content-Type' => 'application/pdf']);
        }

        // 2. If user requests inline raw PDF stream
        if ($request->query('format') === 'pdf' || request('format') === 'pdf') {
            $pdf = Pdf::loadView('livewire.shared.laporan.pdf-resi-pembayaran', [
                'pembayaran' => $pembayaran,
                'staffFinance' => $pembayaran->petugas ?? $staffFinance,
            ]);

            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="resi_pembayaran_' . $pembayaran->id . '.pdf"'
            ]);
        }

        // 3. Default: Interactive Web Preview Page with Print & Download Toolbar
        return view('preview.resi-pembayaran', [
            'pembayaran' => $pembayaran,
            'staffFinance' => $pembayaran->petugas ?? $staffFinance,
        ]);
    }

    public function laporanPengeluaranPdf(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $userRole = auth()->user()->role->nama ?? '';
        if (!in_array($userRole, ['finance', 'super_admin', 'super_admin_2'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat laporan pengeluaran.');
        }

        AuditLogger::log('export', 'Mengekspor Laporan Pengeluaran Kas ke PDF', null, [
            'log_name' => 'keuangan',
            'properties' => ['format' => 'pdf', 'filter' => $request->all()],
        ]);

        $data = FinanceReportService::getPengeluaranData($request->all());
        if ($data->isEmpty()) {
            abort(404, 'Tidak ada data pengeluaran untuk kriteria ini.');
        }

        $school = FinanceReportService::getSchoolHeader();
        $cat = KategoriPengeluaran::find($request->query('kategori_pengeluaran_id'));
        $periodeText = FinanceReportService::formatPeriodeText($request->query('filter_periode'), $request->query('start_date'), $request->query('end_date'));

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-laporan-pengeluaran', [
            'data' => $data,
            'startDate' => $request->query('start_date'),
            'endDate' => $request->query('end_date'),
            'periodeText' => $periodeText,
            'bulan' => $request->query('bulan'),
            'kategori' => $cat?->nama ?? 'Semua',
            'totalPengeluaran' => $data->sum('jumlah'),
            'namaSekolah' => $school['namaSekolah'],
            'alamatSekolah' => $school['alamatSekolah'],
            'noTelepon' => $school['noTelepon'],
            'judul' => $request->query('judul') ?: 'LAPORAN PENGELUARAN KEUANGAN YAYASAN',
            'catatan' => $request->query('catatan'),
            'penandatangan' => $request->query('penandatangan'),
            'jabatanPenandatangan' => $request->query('jabatan_penandatangan') ?: 'Bendahara Yayasan',
        ])->setPaper('a4', 'landscape');

        $filename = 'laporan_pengeluaran_' . date('Ymd_His') . '.pdf';

        return FinanceReportService::renderPdf($pdf, $filename, $request->query('download') === '1' || request('download') === '1');
    }

    public function laporanPemasukanPdf(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $userRole = auth()->user()->role->nama ?? '';
        if (!in_array($userRole, ['finance', 'super_admin', 'super_admin_2'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat laporan pemasukan.');
        }

        AuditLogger::log('export', 'Mengekspor Laporan Pemasukan & Infaq ke PDF', null, [
            'log_name' => 'keuangan',
            'properties' => ['format' => 'pdf', 'filter' => $request->all()],
        ]);

        $data = FinanceReportService::getPemasukanData($request->all());
        if ($data->isEmpty()) {
            abort(404, 'Tidak ada data pemasukan untuk kriteria ini.');
        }

        $jt = JenisTagihan::find($request->query('jenis_tagihan_id'));
        $periodeText = FinanceReportService::formatPeriodeText($request->query('filter_periode'), $request->query('start_date'), $request->query('end_date'));

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-laporan-pemasukan', [
            'data' => $data,
            'startDate' => $request->query('start_date'),
            'endDate' => $request->query('end_date'),
            'periodeText' => $periodeText,
            'bulan' => $request->query('bulan'),
            'metodeBayar' => $request->query('metode_bayar') ?: 'Semua',
            'jenisTagihan' => $jt?->nama ?? 'Semua',
            'totalPemasukan' => $data->sum('nominal_dibayar'),
        ])->setPaper('a4', 'landscape');

        $filename = 'laporan_pemasukan_' . date('Ymd_His') . '.pdf';

        return FinanceReportService::renderPdf($pdf, $filename, $request->query('download') === '1' || request('download') === '1');
    }

    public function laporanTunggakanPdf(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $userRole = auth()->user()->role->nama ?? '';
        if (!in_array($userRole, ['finance', 'super_admin', 'super_admin_2', 'kepala_sekolah', 'pengawas', 'koordinator'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat laporan tunggakan.');
        }

        $data = FinanceReportService::getTunggakanData($request->all());
        if ($data->isEmpty()) {
            abort(404, 'Tidak ada data tunggakan untuk kriteria ini.');
        }

        $kelas = Kelas::find($request->query('kelas_id'));
        $ta = TahunAjaran::find($request->query('tahun_ajaran_id'));
        $periodeText = FinanceReportService::formatPeriodeText($request->query('filter_periode'), $request->query('start_date'), $request->query('end_date'));
        $school = FinanceReportService::getSchoolHeader();

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-laporan-tunggakan', [
            'data' => $data,
            'kelas' => $kelas?->nama_kelas ?? 'Semua Kelas',
            'namaKelas' => $kelas?->nama_kelas ?? 'Semua Kelas',
            'tahunAjaran' => $ta?->nama ?? 'Semua Tahun Ajaran',
            'bulan' => $request->query('bulan'),
            'periodeText' => $periodeText,
            'totalTunggakan' => $data->sum(fn($t) => $t->nominal - $t->total_dibayar),
            'namaSekolah' => $school['namaSekolah'],
            'alamatSekolah' => $school['alamatSekolah'],
            'noTelepon' => $school['noTelepon'],
        ])->setPaper('a4', 'landscape');

        $filename = 'laporan_tunggakan_' . date('Ymd_His') . '.pdf';

        return FinanceReportService::renderPdf($pdf, $filename, $request->query('download') === '1' || request('download') === '1');
    }

    public function danaBosPdf(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $userRole = auth()->user()->role->nama ?? '';
        if (!in_array($userRole, ['finance', 'super_admin', 'super_admin_2', 'founder', 'kepala_sekolah'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk mencetak laporan Dana BOS.');
        }

        AuditLogger::log('export', 'Mengekspor Laporan Pembukuan Dana BOS ke PDF', null, [
            'log_name' => 'keuangan',
            'properties' => ['format' => 'pdf', 'filter' => $request->all()],
        ]);

        $data = FinanceReportService::getDanaBosData($request->all());
        if ($data->isEmpty()) {
            abort(404, 'Tidak ada catatan transaksi dana BOS yang ditemukan.');
        }

        $totalMasuk = (float) DanaBos::where('jenis', 'masuk')->sum('nominal');
        $totalKeluar = (float) DanaBos::where('jenis', 'keluar')->sum('nominal');
        $saldoBos = $totalMasuk - $totalKeluar;

        $school = FinanceReportService::getSchoolHeader('Jl. Pendidikan Karakter Islami, Pekanbaru');
        $periodeText = FinanceReportService::formatPeriodeText($request->query('filter_periode'), $request->query('start_date'), $request->query('end_date'));
        $ta = TahunAjaran::find($request->query('tahun_ajaran_id'));

        $jenis = $request->query('jenis', 'semua');
        $jenisText = match ($jenis) {
            'masuk' => 'Penerimaan Sahaja',
            'keluar' => 'Belanja Sahaja',
            default => 'Semua Mutasi (Penerimaan & Belanja)'
        };

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-dana-bos', [
            'data' => $data,
            'saldoBos' => $saldoBos,
            'totalMasuk' => $totalMasuk,
            'totalKeluar' => $totalKeluar,
            'periodeText' => $periodeText,
            'jenis' => $jenis,
            'jenisText' => $jenisText,
            'tahunAjaran' => $ta?->nama ?? 'Semua Tahun Ajaran',
            'namaSekolah' => $school['namaSekolah'],
            'alamatSekolah' => $school['alamatSekolah'],
            'noTelepon' => $school['noTelepon'],
        ])->setPaper('a4', 'landscape');

        $filename = 'buku_kas_dana_bos_' . date('Ymd_His') . '.pdf';

        return FinanceReportService::renderPdf($pdf, $filename, $request->query('download') === '1' || request('download') === '1');
    }

    public function rekapGajiPdf(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $userRole = auth()->user()->role->nama ?? '';
        if (!in_array($userRole, ['finance', 'super_admin', 'super_admin_2', 'kepala_sekolah', 'founder'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat rekapitulasi gaji guru.');
        }

        AuditLogger::log('export', 'Mengekspor Rekapitulasi Gaji Guru & Karyawan ke PDF', null, [
            'log_name' => 'keuangan',
            'properties' => ['format' => 'pdf', 'filter' => $request->all()],
        ]);

        $salaries = FinanceReportService::getRekapGajiData($request->all());
        if ($salaries->isEmpty()) {
            abort(404, 'Tidak ada data rekap gaji yang ditemukan untuk kriteria ini.');
        }

        $school = FinanceReportService::getSchoolHeader('Jl. Pendidikan Karakter Islami, Pekanbaru');
        $status = $request->query('status');
        $statusText = match ($status) {
            'draft' => 'Draft (Belum Dibayar)',
            'dibayar' => 'Sudah Dibayar (Lunas)',
            default => 'Semua Status'
        };

        $pdf = Pdf::loadView('livewire.shared.laporan.pdf-rekap-gaji-guru', [
            'data' => $salaries,
            'salaries' => $salaries,
            'bulan' => $request->query('bulan') ?: 'Semua Bulan',
            'tahun' => $request->query('tahun') ?: date('Y'),
            'statusText' => $statusText,
            'sumberDana' => $request->query('sumber_dana') ?: 'Semua',
            'namaSekolah' => $school['namaSekolah'],
            'alamatSekolah' => $school['alamatSekolah'],
            'noTelepon' => $school['noTelepon'],
        ])->setPaper('a4', 'landscape');

        $filename = 'rekap_gaji_' . date('Ymd_His') . '.pdf';

        return FinanceReportService::renderPdf($pdf, $filename, $request->query('download') === '1' || request('download') === '1');
    }

    public function tabunganSiswaPdf(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $userRole = auth()->user()->role->nama ?? '';
        if (!in_array($userRole, ['finance', 'super_admin', 'super_admin_2', 'founder'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat laporan tabungan siswa.');
        }

        AuditLogger::log('export', 'Mengekspor Laporan Tabungan Siswa ke PDF', null, [
            'log_name' => 'keuangan',
            'properties' => ['format' => 'pdf', 'filter' => $request->all()],
        ]);

        $siswaId = $request->query('siswa_id');
        $kelasId = $request->query('kelas_id');
        $view = $request->query('view');
        $isHistory = $request->query('history') === '1' || $view === 'history';
        $school = FinanceReportService::getSchoolHeader('Jl. Pendidikan Karakter Islami, Pekanbaru');

        if ($isHistory) {
            $data = FinanceReportService::getTabunganHistoryData($request->all());
            if ($data->isEmpty()) {
                abort(404, 'Tidak ada riwayat mutasi tabungan yang ditemukan untuk kriteria filter ini.');
            }

            $kelas = Kelas::find($kelasId);
            $namaKelas = $kelas?->nama_kelas ? ('Kelas ' . $kelas->nama_kelas) : 'Semua Kelas';
            $periodeText = FinanceReportService::formatPeriodeText($request->query('filter_periode'), $request->query('start_date'), $request->query('end_date'));
            $jenisText = match ($request->query('jenis')) {
                'setor' => 'Mutasi Setor Sahaja',
                'tarik' => 'Mutasi Tarik Sahaja',
                default => 'Semua Mutasi (Setor & Tarik)'
            };

            $pdf = Pdf::loadView('livewire.shared.laporan.pdf-jurnal-tabungan-siswa', [
                'data' => $data,
                'totalSetor' => (float) $data->where('jenis', 'setor')->sum('nominal'),
                'totalTarik' => (float) $data->where('jenis', 'tarik')->sum('nominal'),
                'namaKelas' => $namaKelas,
                'periodeText' => $periodeText,
                'jenisText' => $jenisText,
                'namaSekolah' => $school['namaSekolah'],
                'alamatSekolah' => $school['alamatSekolah'],
                'noTelepon' => $school['noTelepon'],
            ])->setPaper('a4', 'landscape');

            $filename = 'jurnal_mutasi_tabungan_' . date('Ymd_His') . '.pdf';
        } elseif ($siswaId) {
            $result = FinanceReportService::getSingleSiswaTabunganMutasi((int) $siswaId);

            $pdf = Pdf::loadView('livewire.shared.laporan.pdf-buku-tabungan-siswa', [
                'siswa' => $result['siswa'],
                'mutasi' => $result['mutasi'],
                'totalSetor' => $result['totalSetor'],
                'totalTarik' => $result['totalTarik'],
                'saldoAkhir' => $result['saldoAkhir'],
                'namaSekolah' => $school['namaSekolah'],
                'alamatSekolah' => $school['alamatSekolah'],
                'noTelepon' => $school['noTelepon'],
            ])->setPaper('a4', 'portrait');

            $filename = 'buku_tabungan_' . str_replace(' ', '_', strtolower($result['siswa']->user->nama ?? 'siswa')) . '.pdf';
        } else {
            $data = FinanceReportService::getTabunganSummaryData($request->all());
            if ($data->isEmpty()) {
                abort(404, 'Tidak ada data tabungan siswa yang ditemukan.');
            }

            $totalSetorAll = (float) Tabungan::where('jenis', 'setor')->sum('nominal');
            $totalTarikAll = (float) Tabungan::where('jenis', 'tarik')->sum('nominal');
            $kelas = Kelas::find($kelasId);

            $pdf = Pdf::loadView('livewire.shared.laporan.pdf-rekap-tabungan-siswa', [
                'data' => $data,
                'namaKelas' => $kelas?->nama_kelas ?? 'Semua Kelas',
                'totalSetorAll' => $totalSetorAll,
                'totalTarikAll' => $totalTarikAll,
                'totalSaldoAll' => $totalSetorAll - $totalTarikAll,
                'namaSekolah' => $school['namaSekolah'],
                'alamatSekolah' => $school['alamatSekolah'],
                'noTelepon' => $school['noTelepon'],
            ])->setPaper('a4', 'portrait');

            $filename = 'rekap_tabungan_siswa_' . date('Ymd_His') . '.pdf';
        }

        return FinanceReportService::renderPdf($pdf, $filename, $request->query('download') === '1' || request('download') === '1');
    }

    public function arusKasPdf(Request $request)
    {
        if (!auth()->check()) {
            abort(403, 'Akses tidak sah.');
        }

        $userRole = auth()->user()->role->nama ?? '';
        if (!in_array($userRole, ['finance', 'super_admin', 'super_admin_2', 'founder'])) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat laporan arus kas.');
        }

        AuditLogger::log('export', 'Mengekspor Laporan Arus Kas Umum ke PDF', null, [
            'log_name' => 'keuangan',
            'properties' => ['format' => 'pdf', 'filter' => $request->all()],
        ]);

        $component = new \App\Livewire\Finance\ArusKas();
        $component->tab = $request->query('tab', 'semua');
        $component->stream = $request->query('stream', 'semua');
        $component->filterPeriode = $request->query('filter_periode', 'semua');
        $component->startDate = $request->query('start_date');
        $component->endDate = $request->query('end_date');
        $component->search = $request->query('search', '');

        return $component->exportPdf();
    }

    public static function terbilang($angka): string
    {
        return FinanceReportService::terbilang($angka);
    }
}
