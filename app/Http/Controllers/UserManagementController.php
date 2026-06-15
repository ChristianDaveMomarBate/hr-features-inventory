<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $users = User::orderBy('name')->get();

        return view('InventoryDashboard.users', compact('users'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $validated['role'] = 'admin';

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User account updated successfully.');
    }
}
