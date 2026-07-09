<x-admin-layout>
    <x-slot name="title">Manajemen User</x-slot>
    <x-slot name="subtitle">Kelola akun admin & pelanggan RentWheel</x-slot>

    @if (session('success'))
        <div class="mb-4 p-4 rounded-xl bg-green-50 text-green-700 text-sm">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 p-4 rounded-xl bg-red-50 text-red-700 text-sm">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100">
            <form method="GET" class="flex-1 max-w-sm">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama atau email..."
                       class="w-full rounded-lg border-slate-200 text-sm focus:ring-amber-400 focus:border-amber-400">
            </form>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-amber-400 text-slate-900 text-sm font-semibold hover:bg-amber-300 transition-colors">
                + Tambah User
            </a>
        </div>

        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase tracking-wide">
                <tr>
                    <th class="text-left px-6 py-3">Nama</th>
                    <th class="text-left px-6 py-3">Email</th>
                    <th class="text-left px-6 py-3">No HP</th>
                    <th class="text-left px-6 py-3">Role</th>
                    <th class="text-right px-6 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($users as $user)
                    <tr class="hover:bg-slate-50/60">
                        <td class="px-6 py-3 font-medium text-slate-900">{{ $user->name }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $user->email }}</td>
                        <td class="px-6 py-3 text-slate-600">{{ $user->no_hp ?? '-' }}</td>
                        <td class="px-6 py-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                {{ $user->hasRole('admin') ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $user->getRoleNames()->first() ?? '-' }}
                            </span>
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex justify-end gap-3">
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   class="text-amber-600 hover:text-amber-700 font-medium">Edit</a>
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      onsubmit="return confirm('Yakin hapus user ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-600 font-medium">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-10 text-center text-slate-400">Belum ada user.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
    </div>
</x-admin-layout>