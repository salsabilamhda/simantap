@extends('layouts.app')

@section('title', 'Kelola Admin')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Kelola Akun Admin</h1>
            <p class="text-xs text-gray-400 font-medium">Manajemen pengguna berwenang untuk akses sistem database SIMANTAP</p>
        </div>
        <button onclick="document.getElementById('add-admin-modal').classList.remove('hidden')" class="btn-gold-primary px-4 py-2.5 text-xs uppercase tracking-wider flex items-center gap-2">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Tambah Admin</span>
        </button>
    </div>

    <!-- Admin List -->
    <div class="bg-white rounded-3xl p-6 border border-teal-100 shadow-card-custom">
        <div class="divide-y divide-gray-100">
            @foreach($admins as $admin)
                <div class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-2xl bg-teal-50 text-primary font-black text-sm flex items-center justify-center">
                            {{ strtoupper(substr($admin->name, 0, 2)) }}
                        </div>
                        <div>
                            <div class="font-extrabold text-gray-900 text-sm">{{ $admin->name }}</div>
                            <div class="text-xs text-gray-400 flex items-center gap-1 mt-0.5">
                                <i data-lucide="mail" class="w-3.5 h-3.5"></i>
                                <span>{{ $admin->email }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 rounded-full text-xs font-black bg-amber-50 text-amber-800 border border-amber-300">
                            {{ $admin->role == 'superadmin' ? 'Super Admin' : 'Admin Operasional' }}
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700">
                            {{ $admin->status ?: 'Aktif' }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Modal Tambah Admin -->
<div id="add-admin-modal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl w-full max-w-md p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
            <h3 class="font-black text-gray-900 text-base">Tambah Akun Admin</h3>
            <button onclick="document.getElementById('add-admin-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('pengaturan.admin.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" required placeholder="Contoh: Admin ULP Pacitan" class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Alamat Email</label>
                <input type="email" name="email" required placeholder="admin@simantap.id" class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required minlength="6" placeholder="Minimal 6 karakter" class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs">
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Role Hak Akses</label>
                <select name="role" class="w-full px-3 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold">
                    <option value="admin">Admin Operasional</option>
                    <option value="superadmin">Super Admin</option>
                </select>
            </div>
            <button type="submit" class="btn-gold-primary py-2.5 text-xs uppercase tracking-wider w-full mt-4">
                Buat Akun Admin
            </button>
        </form>
    </div>
</div>
@endsection
