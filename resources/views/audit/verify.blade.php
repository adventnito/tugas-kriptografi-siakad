<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Rantai - SIAKAD</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto py-10 px-4">
        <h1 class="text-3xl font-bold mb-2">🔍 Verifikasi Rantai Hash</h1>
        <p class="text-gray-600 mb-6">Sistem akan menghitung ulang seluruh hash dan membandingkan dengan data yang tersimpan.</p>

        <div class="flex gap-2 mb-6">
            <a href="{{ route('audit.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Input Aktivitas</a>
            <a href="{{ route('audit.logs') }}" class="px-4 py-2 bg-gray-700 text-white rounded">Lihat Semua Log</a>
            <a href="{{ route('audit.verify') }}" class="px-4 py-2 bg-green-600 text-white rounded">Verifikasi Rantai</a>
        </div>

        {{-- Hasil verifikasi --}}
        <div class="mb-6 p-6 rounded-lg shadow {{ $result['valid'] ? 'bg-green-50 border-2 border-green-400' : 'bg-red-50 border-2 border-red-400' }}">
            @if ($result['valid'])
                <h2 class="text-2xl font-bold text-green-700">✅ INTEGRITAS TERJAGA</h2>
                <p class="mt-2 text-green-800">{{ $result['message'] }}</p>
            @else
                <h2 class="text-2xl font-bold text-red-700">🚨 MANIPULASI TERDETEKSI!</h2>
                <p class="mt-2 text-red-800">{{ $result['message'] }}</p>
                <p class="mt-2 font-mono text-sm text-red-900">Log ID bermasalah: <strong>{{ $result['broken_id'] }}</strong></p>
            @endif
        </div>

        {{-- Rincian log --}}
        <div class="bg-white p-6 rounded-lg shadow overflow-x-auto">
            <h2 class="text-xl font-semibold mb-4">Rincian Rantai Log</h2>
            @if ($logs->isEmpty())
                <p class="text-gray-500">Belum ada log.</p>
            @else
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-3 py-2 text-left">ID</th>
                            <th class="px-3 py-2 text-left">Status</th>
                            <th class="px-3 py-2 text-left">User</th>
                            <th class="px-3 py-2 text-left">Aktivitas</th>
                            <th class="px-3 py-2 text-left">Previous Hash</th>
                            <th class="px-3 py-2 text-left">Current Hash</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            @php
                                $isBroken = !$result['valid'] && $result['broken_id'] == $log->id;
                            @endphp
                            <tr class="border-b align-top {{ $isBroken ? 'bg-red-100' : '' }}">
                                <td class="px-3 py-2 font-bold">{{ $log->id }}</td>
                                <td class="px-3 py-2">
                                    @if ($isBroken)
                                        <span class="px-2 py-1 bg-red-600 text-white rounded text-xs">RUSAK</span>
                                    @else
                                        <span class="px-2 py-1 bg-green-600 text-white rounded text-xs">OK</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 font-medium">{{ $log->user_name }}</td>
                                <td class="px-3 py-2">{{ $log->activity }}</td>
                                <td class="px-3 py-2 font-mono text-xs text-gray-500 break-all">{{ $log->previous_hash }}</td>
                                <td class="px-3 py-2 font-mono text-xs text-green-700 break-all">{{ $log->current_hash }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</body>
</html>
