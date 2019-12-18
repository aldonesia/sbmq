<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\User;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();

        return view('user.index', compact('users'));
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->update(['approved_at' => now()]);

        return redirect()->route('user.index')->withMessage('User approved successfully');
    }

    public function approval()
    {
        return view('user.approval');
    }
}
