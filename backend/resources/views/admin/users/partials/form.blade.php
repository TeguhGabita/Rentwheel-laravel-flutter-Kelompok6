@php $user = $user ?? null; @endphp

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1">Nama</label>
    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}"
           class="w-full rounded-lg border-slate-200 focus:ring-amber-400 focus:border-amber-400">
    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}"
           class="w-full rounded-lg border-slate-200 focus:ring-amber-400 focus:border-amber-400">
    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1">
        Password {{ $user ? '(kosongkan jika tidak diubah)' : '' }}
    </label>
    <input type="password" name="password"
           class="w-full rounded-lg border-slate-200 focus:ring-amber-400 focus:border-amber-400">
    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>

<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">No HP</label>
        <input type="text" name="no_hp" value="{{ old('no_hp', $user->no_hp ?? '') }}"
               class="w-full rounded-lg border-slate-200 focus:ring-amber-400 focus:border-amber-400">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">No KTP</label>
        <input type="text" name="no_ktp" value="{{ old('no_ktp', $user->no_ktp ?? '') }}"
               class="w-full rounded-lg border-slate-200 focus:ring-amber-400 focus:border-amber-400">
    </div>
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1">Alamat</label>
    <textarea name="alamat" rows="2"
              class="w-full rounded-lg border-slate-200 focus:ring-amber-400 focus:border-amber-400">{{ old('alamat', $user->alamat ?? '') }}</textarea>
</div>

<div>
    <label class="block text-sm font-medium text-slate-700 mb-1">Role</label>
    <select name="role" class="w-full rounded-lg border-slate-200 focus:ring-amber-400 focus:border-amber-400">
        @foreach ($roles as $role)
            <option value="{{ $role }}" @selected(old('role', $user?->getRoleNames()->first()) === $role)>
                {{ ucfirst($role) }}
            </option>
        @endforeach
    </select>
    @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
</div>