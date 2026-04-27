<?php

namespace App\Http\Controllers;

use App\Models\Picture;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session()->has('user_id')) {
            return redirect('/');
        }

        $randomPicture = Picture::where('status', 'approved')->inRandomOrder()->first();
        $randomImage = $randomPicture ? $randomPicture->img : 'assets/images/auth/default.jpg';

        return view('auth.login', compact('randomImage'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'Введите email',
            'email.email' => 'Введите корректный email',
            'password.required' => 'Введите пароль',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()
                ->withErrors(['email' => 'Неверный email или пароль'])
                ->withInput();
        }

        session([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'user_email' => $user->email,
            'user_role' => $user->role,
            'user_img' => $user->img,
        ]);

        return redirect('/');
    }

    public function showRegister()
    {
        if (session()->has('user_id')) {
            return redirect('/');
        }

        $randomPicture = Picture::where('status', 'approved')->inRandomOrder()->first();
        $randomImage = $randomPicture ? $randomPicture->img : 'assets/images/auth/default.jpg';

        return view('auth.register', compact('randomImage'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'min:2', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6'],
            'password_confirmation' => ['required', 'same:password'],
        ], [
            'name.required' => 'Введите имя',
            'name.min' => 'Имя должно содержать минимум 2 символа',
            'name.max' => 'Имя не должно превышать 100 символов',
            'email.required' => 'Введите email',
            'email.email' => 'Введите корректный email',
            'email.unique' => 'Этот email уже зарегистрирован',
            'password.required' => 'Введите пароль',
            'password.min' => 'Пароль должен быть минимум 6 символов',
            'password_confirmation.required' => 'Повторите пароль',
            'password_confirmation.same' => 'Пароли не совпадают',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/auth')->with('success', 'Регистрация успешна! Войдите в аккаунт.');
    }

    public function logout()
    {
        session()->flush();

        return redirect('/auth');
    }
}
