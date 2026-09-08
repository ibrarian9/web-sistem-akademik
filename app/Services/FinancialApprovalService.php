<?php

namespace App\Services;

use App\Models\ApprovalKeuangan;
use App\Models\Notifikasi;
use App\Models\Role;
use App\Models\User;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Models\Tabungan;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FinancialApprovalService
{
    /**
     * Create a new financial approval request.
     */
    public static function createRequest(
        User $pemohon,
        string $actionType,
        string $feature,
        Model $model,
        ?array $newData,
        string $reason,
        string $title
    ): ApprovalKeuangan {
        $oldData = $model->getAttributes();

        $approval = ApprovalKeuangan::create([
            'pemohon_id' => $pemohon->id,
            'tipe_aksi' => $actionType,
            'fitur' => $feature,
            'model_type' => get_class($model),
            'model_id' => $model->getKey(),
            'judul' => $title,
            'data_lama' => $oldData,
            'data_baru' => $newData,
            'alasan' => $reason,
            'status' => 'menunggu',
        ]);

        // Dispatch notifications to Super Admin and Super Admin 2
        $approverRoles = Role::whereIn('nama', ['super_admin', 'super_admin_2', 'founder'])->pluck('id');
        $approvers = User::whereIn('role_id', $approverRoles)->where('status', 'aktif')->get();

        foreach ($approvers as $approver) {
            Notifikasi::create([
                'user_id' => $approver->id,
                'judul' => 'Pengajuan Approval: ' . $title,
                'isi_pesan' => "Staf keuangan {$pemohon->nama} mengajukan " . strtoupper($actionType) . " pada {$feature}. Alasan: {$reason}.",
                'jenis' => 'keuangan',
                'channel' => 'in_app',
                'status_kirim' => 'terkirim',
                'dikirim_pada' => now(),
                'tabel_terkait' => 'approval_keuangan',
                'data_id_terkait' => $approval->id,
            ]);
        }

        AuditLogger::log(
            'created',
            "Mengajukan persetujuan {$actionType} {$feature}: {$title}",
            $approval,
            ['log_name' => 'keuangan']
        );

        return $approval;
    }

    /**
     * Approve a financial approval request and execute the change.
     */
    public static function approve(ApprovalKeuangan $approval, User $approver, ?string $note = null): bool
    {
        $approverRole = $approver->role->nama ?? '';
        if (!in_array($approverRole, ['super_admin', 'super_admin_2', 'founder'])) {
            throw new \Exception('Akses Ditolak: Hanya Super Admin atau Super Admin 2 yang berhak menyetujui.');
        }

        if ($approval->status !== 'menunggu') {
            throw new \Exception('Pengajuan ini sudah diproses sebelumnya.');
        }

        return DB::transaction(function () use ($approval, $approver, $note) {
            $modelClass = $approval->model_type;
            $modelId = $approval->model_id;
            $actionType = $approval->tipe_aksi;

            if ($actionType === 'edit') {
                $target = $modelClass::find($modelId);
                if ($target && !empty($approval->data_baru)) {
                    $target->update($approval->data_baru);

                    // Post-update recalculation logic
                    if ($target instanceof Tagihan) {
                        $newStatus = 'belum_bayar';
                        if ($target->nominal <= 0 || $target->total_dibayar >= $target->nominal) {
                            $newStatus = 'lunas';
                        } elseif ($target->total_dibayar > 0) {
                            $newStatus = 'sebagian';
                        }
                        $target->update(['status' => $newStatus]);
                    } elseif ($target instanceof Tabungan) {
                        self::recalculateStudentTabungan($target->siswa_id);
                    } elseif ($target instanceof \App\Models\GajiGuru) {
                        if ($target->status === 'dibayar' && $target->pengeluaran_id) {
                            $pengeluaran = \App\Models\Pengeluaran::find($target->pengeluaran_id);
                            if ($pengeluaran) {
                                $pengeluaran->update([
                                    'keterangan' => "Honorarium Pegawai Yayasan: " . ($target->guru->user->nama ?? 'Guru') . " - Periode {$target->bulan} {$target->tahun}",
                                    'jumlah' => $target->total_diterima,
                                    'tanggal' => $target->tanggal_bayar ?: $pengeluaran->tanggal,
                                ]);
                            }
                        }
                    }
                }
            } elseif ($actionType === 'hapus') {
                $target = $modelClass::find($modelId);
                if ($target) {
                    if ($target instanceof Pembayaran) {
                        $tagihan = $target->tagihan;
                        $siswa = $tagihan ? $tagihan->siswa : null;
                        $nominalDibayar = floatval($target->nominal_dibayar);
                        $kelebihan = floatval($target->kelebihan_bayar);
                        $metode = $target->metode_bayar;

                        if ($siswa) {
                            if (strtolower($metode) === 'deposit') {
                                $siswa->increment('saldo_deposit', $nominalDibayar);
                            }
                            if ($kelebihan > 0) {
                                $siswa->decrement('saldo_deposit', min(floatval($siswa->saldo_deposit), $kelebihan));
                            }
                        }

                        $target->delete();

                        if ($tagihan) {
                            $newTotal = floatval($tagihan->pembayarans()->sum('nominal_dibayar'));
                            $newStatus = 'belum_bayar';
                            if ($tagihan->nominal <= 0 || $newTotal >= $tagihan->nominal) {
                                $newStatus = 'lunas';
                            } elseif ($newTotal > 0) {
                                $newStatus = 'sebagian';
                            }
                            $tagihan->update([
                                'total_dibayar' => $newTotal,
                                'status' => $newStatus,
                            ]);
                        }
                    } elseif ($target instanceof Tagihan) {
                        $siswa = $target->siswa;
                        if ($target->pembayarans && $target->pembayarans->count() > 0) {
                            foreach ($target->pembayarans as $pembayaran) {
                                if ($pembayaran->metode_bayar === 'Deposit' && $pembayaran->nominal_dibayar > 0 && $siswa) {
                                    $siswa->increment('saldo_deposit', $pembayaran->nominal_dibayar);
                                }
                                if ($pembayaran->kelebihan_bayar > 0 && $siswa) {
                                    $siswa->decrement('saldo_deposit', min(floatval($siswa->saldo_deposit), floatval($pembayaran->kelebihan_bayar)));
                                }
                                $pembayaran->delete();
                            }
                        }
                        $target->delete();
                    } elseif ($target instanceof Tabungan) {
                        $siswaId = $target->siswa_id;
                        $target->delete();
                        self::recalculateStudentTabungan($siswaId);
                    } elseif ($target instanceof \App\Models\GajiGuru) {
                        if ($target->status === 'dibayar') {
                            if ($target->pengeluaran_id) {
                                $pengeluaran = \App\Models\Pengeluaran::find($target->pengeluaran_id);
                                if ($pengeluaran) {
                                    $pengeluaran->delete();
                                }
                            }
                            if ($target->potongan_peminjaman > 0) {
                                $loan = \App\Models\Peminjaman::where('guru_id', $target->guru_id)->first();
                                if ($loan) {
                                    $loan->update([
                                        'sisa_pinjaman' => $loan->sisa_pinjaman + $target->potongan_peminjaman,
                                        'status' => 'berjalan'
                                    ]);
                                }
                            }
                        }
                        $target->delete();
                    } else {
                        $target->delete();
                    }
                }
            }

            // Update approval status
            $approval->update([
                'status' => 'disetujui',
                'disetujui_oleh' => $approver->id,
                'tanggal_disetujui' => now(),
                'catatan_approval' => $note,
            ]);

            // Notify requester
            Notifikasi::create([
                'user_id' => $approval->pemohon_id,
                'judul' => 'Pengajuan Disetujui: ' . $approval->judul,
                'isi_pesan' => "Pengajuan " . strtoupper($actionType) . " data {$approval->fitur} telah disetujui oleh {$approver->nama}." . ($note ? " Catatan: {$note}" : ''),
                'jenis' => 'keuangan',
                'channel' => 'in_app',
                'status_kirim' => 'terkirim',
                'dikirim_pada' => now(),
                'tabel_terkait' => 'approval_keuangan',
                'data_id_terkait' => $approval->id,
            ]);

            AuditLogger::log(
                'updated',
                "Menyetujui permohonan {$actionType} {$approval->fitur}: {$approval->judul}",
                $approval,
                ['log_name' => 'keuangan']
            );

            return true;
        });
    }

    /**
     * Reject a financial approval request.
     */
    public static function reject(ApprovalKeuangan $approval, User $approver, string $reason): bool
    {
        $approverRole = $approver->role->nama ?? '';
        if (!in_array($approverRole, ['super_admin', 'super_admin_2', 'founder'])) {
            throw new \Exception('Akses Ditolak: Hanya Super Admin atau Super Admin 2 yang berhak menolak.');
        }

        if ($approval->status !== 'menunggu') {
            throw new \Exception('Pengajuan ini sudah diproses sebelumnya.');
        }

        return DB::transaction(function () use ($approval, $approver, $reason) {
            $approval->update([
                'status' => 'ditolak',
                'disetujui_oleh' => $approver->id,
                'tanggal_disetujui' => now(),
                'catatan_approval' => $reason,
            ]);

            // Notify requester
            Notifikasi::create([
                'user_id' => $approval->pemohon_id,
                'judul' => 'Pengajuan Ditolak: ' . $approval->judul,
                'isi_pesan' => "Pengajuan " . strtoupper($approval->tipe_aksi) . " data {$approval->fitur} ditolak oleh {$approver->nama}. Alasan: {$reason}",
                'jenis' => 'keuangan',
                'channel' => 'in_app',
                'status_kirim' => 'terkirim',
                'dikirim_pada' => now(),
                'tabel_terkait' => 'approval_keuangan',
                'data_id_terkait' => $approval->id,
            ]);

            AuditLogger::log(
                'updated',
                "Menolak permohonan {$approval->tipe_aksi} {$approval->fitur}: {$approval->judul}",
                $approval,
                ['log_name' => 'keuangan']
            );

            return true;
        });
    }

    /**
     * Cancel a financial approval request.
     */
    public static function cancel(ApprovalKeuangan $approval, User $canceller, ?string $reason = null): bool
    {
        $cancellerRole = $canceller->role->nama ?? '';
        $isPemohon = $approval->pemohon_id === $canceller->id;
        $isAllowedRole = in_array($cancellerRole, ['finance', 'super_admin', 'super_admin_2', 'founder']);

        if (!$isPemohon && !$isAllowedRole) {
            throw new \Exception('Akses Ditolak: Anda tidak memiliki izin untuk membatalkan permohonan persetujuan ini.');
        }

        if ($approval->status !== 'menunggu') {
            throw new \Exception("Permohonan persetujuan tidak dapat dibatalkan karena sudah berstatus {$approval->status}.");
        }

        return DB::transaction(function () use ($approval, $canceller, $reason) {
            $catatan = $reason ?: 'Dibatalkan oleh staf keuangan';

            $approval->update([
                'status' => 'dibatalkan',
                'catatan_approval' => $catatan,
            ]);

            // Notify parties
            if ($canceller->id !== $approval->pemohon_id) {
                Notifikasi::create([
                    'user_id' => $approval->pemohon_id,
                    'judul' => 'Pengajuan Dibatalkan: ' . $approval->judul,
                    'isi_pesan' => "Pengajuan " . strtoupper($approval->tipe_aksi) . " data {$approval->fitur} telah dibatalkan oleh {$canceller->nama}." . ($reason ? " Catatan: {$reason}" : ''),
                    'jenis' => 'keuangan',
                    'channel' => 'in_app',
                    'status_kirim' => 'terkirim',
                    'dikirim_pada' => now(),
                    'tabel_terkait' => 'approval_keuangan',
                    'data_id_terkait' => $approval->id,
                ]);
            } else {
                $approverRoles = Role::whereIn('nama', ['super_admin', 'super_admin_2', 'founder'])->pluck('id');
                $approvers = User::whereIn('role_id', $approverRoles)->where('status', 'aktif')->get();

                foreach ($approvers as $approver) {
                    Notifikasi::create([
                        'user_id' => $approver->id,
                        'judul' => 'Pengajuan Dibatalkan: ' . $approval->judul,
                        'isi_pesan' => "Staf keuangan {$canceller->nama} membatalkan pengajuan " . strtoupper($approval->tipe_aksi) . " pada {$approval->fitur}." . ($reason ? " Catatan: {$reason}" : ''),
                        'jenis' => 'keuangan',
                        'channel' => 'in_app',
                        'status_kirim' => 'terkirim',
                        'dikirim_pada' => now(),
                        'tabel_terkait' => 'approval_keuangan',
                        'data_id_terkait' => $approval->id,
                    ]);
                }
            }

            AuditLogger::log(
                'updated',
                "Membatalkan permohonan {$approval->tipe_aksi} {$approval->fitur}: {$approval->judul}",
                $approval,
                ['log_name' => 'keuangan']
            );

            return true;
        });
    }


    /**
     * Recalculate student savings balances sequentially.
     */
    public static function recalculateStudentTabungan(int $siswaId): void
    {
        $transactions = Tabungan::where('siswa_id', $siswaId)
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $runningBalance = 0.00;
        foreach ($transactions as $tx) {
            if ($tx->jenis === 'setor') {
                $runningBalance += floatval($tx->nominal);
            } else {
                $runningBalance -= floatval($tx->nominal);
            }
            $tx->saldo_akhir = $runningBalance;
            $tx->saveQuietly();
        }
    }
}
