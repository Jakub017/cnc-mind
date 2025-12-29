<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::updateOrCreate([
                'google_id' => $googleUser->id,
            ], [
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'google_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
                'password' =>  bcrypt(Str::random(16)),
            ]);

            Auth::login($user);

            return redirect()->route('dashboard');
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return redirect()->route('login')->with('error', __('This email address is already associated with another account.'));
        }
        catch (\Exception $e) {
            return redirect()->route('login')->with('error', __('Something went wrong. Please try again later.'));
        }
    }
}
