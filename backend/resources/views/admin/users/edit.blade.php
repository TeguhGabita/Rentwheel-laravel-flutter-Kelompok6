<x-admin-layout>
    <x-slot name="title">Edit User</x-slot>
    <x-slot name="subtitle">{{ $user->name }}</x-slot>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-8 max-w-2xl">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
            @csrf @method('PUT')
            @include('admin.users.partials.form')

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">Batal</a>
                <button type="submit" class="px-5 py-2.5 rounded-lg bg-amber-400 text-slate-900 text-sm font-semibold hover:bg-amber-300">Update</button>
            </div>
        </form>
    </div>
</x-admin-layout>
