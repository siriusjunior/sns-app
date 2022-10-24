<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

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
        $this->middleware('guest')->except('logout');
    }

    public function redirectToProvider(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(Request $request, string $provider)
    {
        // Googleから取得するユーザー情報
        $providerUser = Socialite::driver($provider)->stateless()->user();
        // Googleから取得するユーザー情報からメールアドレスを取出し、同メールに紐づくユーザー抽出
        $user = User::where('email', $providerUser->getEmail())->first();
        if ($user) {
            // 当該ユーザーのログイン処理,ログイン状態維持(true)
            $this->guard()->login($user, true);
            // ログイン後の画⾯へ遷移(cf.laravel/vendor/laravel/framework/src/Illuminate/Foundation/Auth/AuthenticatesUsers.php)
            return $this->sendLoginResponse($request);
        }
        // $providerUserはいるが本サービス上に紐づく$userがnullの場合
        return redirect()->route('register.{provider}', [
            'provider' => $provider,
            'email' => $providerUser->getEmail(),
            'token' => $providerUser->token,
        ]);
    }
}
