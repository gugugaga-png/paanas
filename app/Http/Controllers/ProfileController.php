<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Menampilkan formulir profil pengguna.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        $user = Auth::user(); // Ambil pengguna yang sedang login
        return view('profile.show', compact('user'));
    }

    /**
     * Menampilkan formulir edit profil pengguna.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = Auth::user(); // Ambil pengguna yang sedang login
        return view('profile.editprofile', compact('user'));
    }

    /**
     * Memperbarui profil pengguna.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validasi data input
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            // Pastikan email unik, kecuali untuk email pengguna saat ini
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'], // Max 2MB
            'bio' => ['nullable', 'string', 'max:1000'],
        ]);

        // Tangani unggahan gambar profil
        if ($request->hasFile('profile_picture')) {
            // Hapus gambar lama jika ada
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
            // Simpan gambar baru
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $user->profile_picture = $path;
        }

        // Update informasi profil lainnya
        $user->name = $request->name;
        $user->email = $request->email;
        $user->bio = $request->bio;
        // Tambahkan kolom lain yang ingin diupdate di sini

        $user->save();

        return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui!');
    }
}