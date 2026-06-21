<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request, \App\Filters\UserFilter $filters)
    {
        $query = User::with(['roles', 'pegawai'])->filter($filters);

        if ($request->export === 'excel') {
            return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\UserExport($query->get()), 'user.xlsx');
        }

        if ($request->export === 'pdf') {
            $users = $query->get();
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('user.pdf', compact('users'))->setPaper('a4', 'portrait');
            return $pdf->download('user.pdf');
        }

        $perPage = $request->get('per_page', 10);
        $users = $query->paginate($perPage)->withQueryString();

        if ($request->ajax()) {
            return view('user._table', compact('users'))->render();
        }

        $roles = Role::all();
        $ruangans = \App\Models\Ruangan::all();

        return view('user.index', compact('users', 'roles', 'ruangans'));
    }

    public function create()
    {
        $roles = Role::all();
        $pegawai = Pegawai::all();
        return view('user.create', compact('roles', 'pegawai'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'username' => 'nullable|string|unique:users,username',
            'password' => 'required|min:6|confirmed',
            'roles' => 'required|array',
            'pegawai_id' => 'required|exists:pegawai,id|unique:users,pegawai_id',
        ], [
            'pegawai_id.required' => 'User harus dihubungkan dengan data pegawai.',
            'pegawai_id.unique' => 'Pegawai ini sudah memiliki akun user.',
        ]);

        // Get pegawai data to access NIP
        $pegawai = Pegawai::find($validated['pegawai_id']);
        
        // Check if role includes kepala_ruangan
        $isKepalaRuangan = in_array('kepala_ruangan', $validated['roles']);
        
        // If kepala_ruangan, use NIP as password
        $password = $validated['password'];
        if ($isKepalaRuangan) {
            $password = $pegawai->nip;
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => $validated['username'] ?? null,
            'nip' => $pegawai->nip,
            'password' => Hash::make($password),
            'pegawai_id' => $validated['pegawai_id'],
        ]);

        $user->assignRole($validated['roles']);

        return redirect()->route('user.index')->with('success', 'User berhasil dibuat.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $pegawai = Pegawai::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('user.edit', compact('user', 'roles', 'userRoles', 'pegawai'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'username' => 'nullable|string|unique:users,username,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'roles' => 'required|array',
            'pegawai_id' => 'required|exists:pegawai,id|unique:users,pegawai_id,' . $user->id,
            'reset_password_to_nip' => 'nullable|boolean',
        ]);

        // Get pegawai data to access NIP
        $pegawai = Pegawai::find($validated['pegawai_id']);
        
        // Check if role includes kepala_ruangan
        $isKepalaRuangan = in_array('kepala_ruangan', $validated['roles']);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'username' => $validated['username'] ?? null,
            'nip' => $pegawai->nip,
            'pegawai_id' => $validated['pegawai_id'],
        ]);

        // Handle password update
        if ($request->filled('reset_password_to_nip') && $isKepalaRuangan) {
            // Reset password to NIP for kepala_ruangan
            $user->update(['password' => Hash::make($pegawai->nip)]);
        } elseif ($request->filled('password')) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles($validated['roles']);

        return redirect()->route('user.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus diri sendiri.');
        }

        $user->delete();
        return redirect()->route('user.index')->with('success', 'User berhasil dihapus.');
    }
}
