<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            'role' => ['required', Rule::in(['admin', 'staff', 'viewer'])],
            'is_active' => ['required', 'boolean'],
        ]);

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'User account updated successfully.');
    }
}
