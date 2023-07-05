<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\Log;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $users = User::all();
        $roles = Role::all();

        return view('home', compact('users', 'roles'));
    }

    public function changeRole(User $user, Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $role = Role::findOrFail($request->role_id);

        // Update the user's role ID
        $user->role_id = $role->id;
        $user->save();
        $this->logAction('change-role', $user, "Changed {$user->username} to {$role->name}");

        // Redirect back to the home page
        return redirect('/home');
    }

    private function logAction(string $action, ?User $user, string $description): void
    {
        $log = new Log();
        if (auth()->user()) {
            $log->user_id = auth()->user()->id;
        } else {
            $log->user_id = 1;
        }
        $log->action = $action;
        $log->endpoint = '/home';
        // if ($user) {
        //     $log->user_id = $user->id;
        // }
        $log->description = $description;
        $log->save();
    }
}
