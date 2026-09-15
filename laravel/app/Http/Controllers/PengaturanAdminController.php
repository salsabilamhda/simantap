<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PengaturanAdminController extends Controller
{
    public function index()
    {
        $admins = User::all();
        return view('pengaturan.admin', compact('admins'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:6',
            'role' => 'required|in:superadmin,admin',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = 'Aktif';

        User::create($validated);

        return redirect()->back()->with('success', "Akun admin {$validated['name']} berhasil dibuat!");
    }
}
