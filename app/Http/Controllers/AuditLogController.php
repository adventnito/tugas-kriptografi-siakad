<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogController extends Controller
{
    /**
     * Halaman utama: form input aktivitas + daftar log terbaru
     */
    public function index()
    {
        $logs = AuditLog::orderBy('id', 'desc')->limit(20)->get();
        return view('audit.index', compact('logs'));
    }

    /**
     * Simpan aktivitas baru -> otomatis tercatat dengan hash chaining
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_name' => 'required|string|max:100',
            'activity'  => 'required|string|max:255',
        ]);

        // LOGIKA HASH CHAINING (Pindahan dari Service)
        DB::transaction(function () use ($request) {
            $userName = $request->input('user_name');
            $activity = $request->input('activity');

            // 1. Ambil log terakhir
            $lastLog = AuditLog::orderBy('id', 'desc')->lockForUpdate()->first();

            // 2. Tentukan previous_hash
            $previousHash = $lastLog ? $lastLog->current_hash : str_repeat('0', 64);

            // 3. Hitung current_hash SHA-256
            $rawData     = $userName . '|' . $activity . '|' . $previousHash;
            $currentHash = hash('sha256', $rawData);

            // 4. Simpan
            AuditLog::create([
                'user_name'     => $userName,
                'activity'      => $activity,
                'previous_hash' => $previousHash,
                'current_hash'  => $currentHash,
            ]);
        });

        return redirect()->route('audit.index')
            ->with('success', 'Aktivitas berhasil dicatat dan di-hash.');
    }

    /**
     * Halaman daftar seluruh log
     */
    public function showLogs()
    {
        $logs = AuditLog::orderBy('id', 'asc')->get();
        return view('audit.logs', compact('logs'));
    }

    /**
     * Halaman verifikasi rantai log
     */
    public function verify()
    {
        $logs = AuditLog::orderBy('id', 'asc')->get();
        $result = [
            'valid'     => true,
            'message'   => 'Belum ada log untuk diverifikasi.',
            'broken_id' => null,
        ];

        if (!$logs->isEmpty()) {
            $expectedPreviousHash = str_repeat('0', 64); // genesis

            foreach ($logs as $log) {
                // Cek apakah previous_hash cocok dengan hash log sebelumnya
                if ($log->previous_hash !== $expectedPreviousHash) {
                    $result = [
                        'valid'     => false,
                        'message'   => "Rantai hash TERPUTUS di Log ID {$log->id}. Kemungkinan ada log yang dihapus atau ditambahkan.",
                        'broken_id' => $log->id,
                    ];
                    break; // Stop pengecekan jika sudah ketemu yang rusak
                }

                // Hitung ulang hash dari data log ini
                $rawData          = $log->user_name . '|' . $log->activity . '|' . $log->previous_hash;
                $recalculatedHash = hash('sha256', $rawData);

                // Bandingkan dengan current_hash yang tersimpan
                if ($recalculatedHash !== $log->current_hash) {
                    $result = [
                        'valid'     => false,
                        'message'   => "Data log telah DIMANIPULASI pada Log ID {$log->id}. Hash tidak cocok.",
                        'broken_id' => $log->id,
                    ];
                    break;
                }

                $expectedPreviousHash = $log->current_hash;
            }

            if ($result['valid'] && !$logs->isEmpty()) {
                 $result['message'] = 'Semua log VALID. Rantai hash utuh dan tidak ada manipulasi.';
            }
        }

        return view('audit.verify', compact('result', 'logs'));
    }
}
