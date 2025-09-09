<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Web\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserController extends BaseController
{
    public function __construct()
    {
        // Middleware akan dihandle di routes
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        // Assign role using Spatie
        $user->assignRole($request->role);

        return $this->redirectToRouteWithParams('users.index', $request, 'User berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['penagih']);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Update role using Spatie
        $user->syncRoles([$request->role]);

        return $this->redirectToRouteWithParams('users.index', $request, 'User berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user)
    {
        // Prevent deleting own account
        if ($user->id === Auth::id()) {
            return $this->redirectBackWithParams($request, 'Tidak dapat menghapus akun sendiri.', 'error');
        }

        $user->delete();

        return $this->redirectToRouteWithParams('users.index', $request, 'User berhasil dihapus.');
    }
}
