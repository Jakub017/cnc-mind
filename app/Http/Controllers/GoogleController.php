<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request)
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
                'password' => bcrypt(Str::random(16)),
                'has_set_password' => false,
            ]);
            
            if($user->two_factor_secret != null) {
               $request->session()->put([
                    'login.id' => $user->id,
                    'login.remember' => true,
                ]);
                return redirect()->route('two-factor.login');
            } else {
                Auth::login($user);
            }

            return redirect()->route('dashboard');
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            return redirect()->route('login')->with('error', __('This email address is already associated with another account.'));
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', __('Something went wrong. Please try again later.'));
        }
    }
}
