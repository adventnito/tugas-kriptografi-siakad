<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Log - SIAKAD</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="max-w-5xl mx-auto py-10 px-4">

        <h1 class="text-3xl font-bold text-gray-800 mb-2">🔐 Audit Log SIAKAD</h1>
        <p class="text-gray-600 mb-6">Setiap aktivitas otomatis dicatat dengan Hash Chaining SHA-256.</p>

        {{-- Menu navigasi --}}
        <div class="flex gap-2 mb-6">
            <a href="{{ route('audit.index') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Input Aktivitas</a>
            <a href="{{ route('audit.logs') }}" class="px-4 py-2 bg-gray-700 text-white rounded">Lihat Semua Log</a>
            <a href="{{ route('audit.verify') }}" class="px-4 py-2 bg-green-600 text-white rounded">Verifikasi Rantai</a>
        </div>

        {{-- Notifikasi sukses --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4">
                ✅ {{ session('success') }}
            </div>
        @endif

        {{-- Form input aktivitas --}}
        <div class="bg-white p-6 rounded-lg shadow mb-8">
            <h2 class="text-xl font-semibold mb-4">Catat Aktivitas Baru</h2>

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-2 rounded mb-4">
                    @foreach ($errors->all() as $error)
                        <div>❌ {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('audit.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama User</label>
                    <input type="text" name="user_name" placeholder="Contoh: admin, dosen01, mahasiswa01"
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="{{ old('user_name') }}" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Aktivitas</label>
                    <input type="text" name="activity" placeholder="Contoh: Input nilai mahasiswa 22101001 pada MK Kriptografi"
                           class="w-full border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                           value="{{ old('activity') }}" required>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded">
                    Simpan & Hash
                </button>
            </form>
        </div>

        {{-- Tabel log terbaru (20 terakhir) --}}
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-xl font-semibold mb-4">20 Log Terbaru</h2>
            @if ($logs->isEmpty())
                <p class="text-gray-500">Belum ada log.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-3 py-2 text-left">ID</th>
                                <th class="px-3 py-2 text-left">Waktu</th>
                                <th class="px-3 py-2 text-left">User</th>
                                <th class="px-3 py-2 text-left">Aktivitas</th>
                                <th class="px-3 py-2 text-left">Current Hash (potongan)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($logs as $log)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-3 py-2">{{ $log->id }}</td>
                                    <td class="px-3 py-2 text-gray-600">{{ $log->created_at->format('d-m-Y H:i:s') }}</td>
                                    <td class="px-3 py-2 font-medium">{{ $log->user_name }}</td>
                                    <td class="px-3 py-2">{{ $log->activity }}</td>
                                    <td class="px-3 py-2 font-mono text-xs text-gray-500">{{ substr($log->current_hash, 0, 16) }}...</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

    </div>
</body>
</html>
