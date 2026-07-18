<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\InventoryItem;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except(['logout', 'showLoginForm']);
    }

    public function showLoginForm()
    {
        $items = InventoryItem::orderBy('category')
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'category', 'type', 'unit', 'stock_unit', 'issue_unit', 'units_per_stock_unit', 'stock', 'minimum', 'description', 'location']);

        $divisions = [
            'Administrative Division',
            'Compensation & Benefits Division',
            'Office of the OIC-PHRMDO',
            'Performance Management Learning & Development/Wellness Division',
        ];

        $request_items = InventoryItem::orderBy('category')->orderBy('name')->get();

        $submittedRequest = null;
        if (session('show_receipt_modal') && session('new_request_id')) {
            $submittedRequest = \App\Models\ItemRequest::with('item')->find(session('new_request_id'));
        }

        return view('auth.login', compact('items', 'divisions', 'request_items', 'submittedRequest'));
    }

    protected function authenticated(Request $request, $user)
    {
        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account is deactivated. Please contact an administrator.',
            ]);
        }

        if ($request->has('remember')) {
            \Cookie::queue('saved_email', $request->email, 43200);
            \Cookie::queue('saved_password', $request->password, 43200);
            \Cookie::queue('saved_remember', 'checked', 43200);
        } else {
            \Cookie::queue(\Cookie::forget('saved_email'));
            \Cookie::queue(\Cookie::forget('saved_password'));
            \Cookie::queue(\Cookie::forget('saved_remember'));
        }

        return redirect()->route('dashboard')->with('login_success', true);
    }
}
