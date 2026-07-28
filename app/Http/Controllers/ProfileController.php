<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        $request->validateWithBag('profile', [
            'name'              => 'required|string|max:255',
            'profile_picture'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password'  => ['nullable', 'required_with:password', 'current_password'],
            'password'          => ['nullable', 'confirmed', Password::defaults()],
        ]);

        $user->name = $request->name;

        if ($request->hasFile('profile_picture')) {

            if (
                $user->profile_picture &&
                Storage::disk('public')->exists($user->profile_picture)
            ) {
                Storage::disk('public')->delete($user->profile_picture);
            }

            $user->profile_picture = $request
                ->file('profile_picture')
                ->store('profile_pictures', 'public');
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}