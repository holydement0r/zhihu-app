<?php

namespace App\Http\Controllers\Auth;

use App\Mailer\UserMailer;
use Mail;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Naux\Mail\SendCloudTemplate;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RegisterController extends Controller
{

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';
    protected $userMailer;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(UserMailer $userMailer)
    {
        $this->middleware('guest');
        $this->userMailer = $userMailer;
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'avatar' => '/images/avatars/wallhaven.jpg',
            'confirmation_token' => str_random(40),
            'password' => bcrypt($data['password']),
            'api_token'=>str_random(60),
            'setting'=> ['city'=>'','site'=>'','github'=>'','bio'=>'']
        ]);
        $user->assignRole('member');
        $this->sendVerifyEmailTo($user);
        return $user;
    }

    //发送验证邮件
    public function sendVerifyEmailTo($user)
    {
        // 模板变量
        $this->userMailer->welcome($user);
    }
}
