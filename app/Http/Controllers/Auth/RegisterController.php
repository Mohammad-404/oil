<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest');
    }

    const INVITE_CODE = 'RBS@2026';

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'invite_code' => ['required', 'in:' . self::INVITE_CODE],
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
            'role'        => ['required', 'in:cashier,user'],
        ], [
            'invite_code.required' => 'كود الدعوة مطلوب.',
            'invite_code.in'       => 'كود الدعوة غير صحيح.',
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => $data['role'],
        ]);
    }

    protected function registered($request, $user)
    {
        if ($user->role === 'user') {
            return redirect()->route('receipts.index');
        }
        return redirect()->intended('/');
    }
}
