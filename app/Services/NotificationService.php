<?php

namespace App\Services;

use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Send a notification to a specific user using multiple channels.
     *
     * @param int $userId
     * @param string $title
     * @param string $message
     * @param string $type
     * @param array $channels
     * @param int|null $siswaId
     * @param string|null $tabelTerkait
     * @param int|null $dataIdTerkait
     * @return array
     */
    public static function send(
        int $userId,
        string $title,
        string $message,
        string $type = 'sistem',
        array $channels = ['in_app'],
        ?int $siswaId = null,
        ?string $tabelTerkait = null,
        ?int $dataIdTerkait = null
    ): array {
        $results = [];
        $user = User::find($userId);

        if (!$user) {
            Log::warning("Notification failed: User ID {$userId} not found.");
            return [];
        }

        foreach ($channels as $channel) {
            $statusKirim = 'pending';
            $dikirimPada = null;

            if ($channel === 'in_app') {
                $statusKirim = 'terkirim';
            }

            $notification = Notifikasi::create([
                'user_id' => $userId,
                'siswa_id' => $siswaId,
                'judul' => $title,
                'isi_pesan' => $message,
                'jenis' => $type,
                'channel' => $channel,
                'status_kirim' => $statusKirim,
                'dikirim_pada' => $dikirimPada,
                'tabel_terkait' => $tabelTerkait,
                'data_id_terkait' => $dataIdTerkait,
            ]);

            try {
                if ($channel === 'email' && $user->email) {
                    Mail::raw($message, function ($mail) use ($user, $title) {
                        $mail->to($user->email)
                            ->subject($title);
                    });
                    $notification->update([
                        'status_kirim' => 'terkirim',
                        'dikirim_pada' => now(),
                    ]);
                } elseif ($channel === 'whatsapp') {
                    $payload = [
                        'to' => $user->no_hp ?? 'No phone number',
                        'message' => "*{$title}*\n\n{$message}",
                        'timestamp' => now()->toIso8601String(),
                    ];
                    
                    Log::channel('single')->info('WhatsApp Notification Simulation: ' . json_encode($payload));
                    
                    if (!is_dir(storage_path('logs'))) {
                        mkdir(storage_path('logs'), 0755, true);
                    }
                    
                    file_put_contents(
                        storage_path('logs/whatsapp_simulation.log'),
                        json_encode($payload) . PHP_EOL,
                        FILE_APPEND
                    );

                    $notification->update([
                        'status_kirim' => 'terkirim',
                        'dikirim_pada' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Failed to send notification via {$channel}: " . $e->getMessage());
                $notification->update([
                    'status_kirim' => 'gagal',
                ]);
            }

            $results[] = $notification;
        }

        return $results;
    }
}
