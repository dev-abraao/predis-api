<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Throwable;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            return back()->with('message', 'Login successful');
        }

        return back()->with('message', 'Invalid credentials');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        return back()->with('message', 'Registration successful');
    }

    public function addToCart(Request $request)
    {
        // Implementation for adding item to cart

        try {
            $data = $request->validate([
                'name' => 'required|string',
            ]);

            $cart = $request->session()->get('cart', []);
            $cart[] = [
                'product_id' => $request->input('product_id'),
                'quantity'   => $request->input('quantity', 1),
            ];
            $request->session()->put('cart', $cart);

            return back()->with('message', 'Item added to cart');
        } catch (Throwable $e) {
            return back()->withErrors($e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back()->with('message', 'Logged out successfully');
    }
}
