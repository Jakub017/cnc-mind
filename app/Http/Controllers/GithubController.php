<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GithubController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('github')->redirect();
    }

    public function callback()
    {
        try {
            $githubUser = Socialite::driver('github')->user();

            $user = User::updateOrCreate([
                'github_id' => $githubUser->id,
            ], [
                'name' => $githubUser->name,
                'email' => $githubUser->email,
                'github_token' => $githubUser->token,
                'github_refresh_token' => $githubUser->refreshToken,
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
