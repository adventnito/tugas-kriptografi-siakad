<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Semua Log - SIAKAD</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-7xl mx-auto py-10 px-4">
        <h1 class="text-3xl font-bold mb-2">📋 Semua Audit Log</h1>
        <p class="text-gray-600 mb-6">Total: {{ $logs->count() }} log tercatat.</p>

        <div class="flex gap-2 mb-6">
            <a href="{{ route('audit.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Input Aktivitas</a>
            <a href="{{ route('audit.logs') }}" class="px-4 py-2 bg-gray-700 text-white rounded">Lihat Semua Log</a>
            <a href="{{ route('audit.verify') }}" class="px-4 py-2 bg-green-600 text-white rounded">Verifikasi Rantai</a>
        </div>

        <div class="bg-white p-6 rounded-lg shadow overflow-x-auto">
            @if ($logs->isEmpty())
                <p class="text-gray-500">Belum ada log.</p>
            @else
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-3 py-2 text-left">ID</th>
                            <th class="px-3 py-2 text-left">Waktu</th>
                            <th class="px-3 py-2 text-left">User</th>
                            <th class="px-3 py-2 text-left">Aktivitas</th>
                            <th class="px-3 py-2 text-left">Previous Hash</th>
                            <th class="px-3 py-2 text-left">Current Hash</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr class="border-b hover:bg-gray-50 align-top">
                                <td class="px-3 py-2">{{ $log->id }}</td>
                                <td class="px-3 py-2 text-gray-600 whitespace-nowrap">{{ $log->created_at->format('d-m-Y H:i:s') }}</td>
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
