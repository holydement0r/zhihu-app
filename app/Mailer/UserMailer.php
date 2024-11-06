<?php


namespace App\Mailer;

use App\User;
use Auth;
class UserMailer extends Mailer
{
    protected $mailer;
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer; // 在构造函数中初始化
    }
    //新用户关注
    public function followNotifyEmail($email)
    {
        $data = [
            'url' => url('http://laravel.dementor.cn'),
            'name'=> Auth::guard('api')->user()->name,
        ];

        $this->mailer->sendTo('新用户关注', $email, $data);
    }

    //密码重置
    public function passwordReset($email,$token)
    {
        $data = [
            'url' => url('password/reset', $token)
        ];

        $this->mailer->sendTo('密码重置', $user->email, $data);
    }

    //用户注册
    public function welcome(User $user)
    {
        $data = [
            'url' => route('email.verify',['token'=>$user->confirmation_token]),
            'name'=>$user->name
        ];

        $this->mailer->sendTo('欢迎注册', $user->email, $data);
    }

    //发邮件
    public function sendCustomEmail($email, $subject, array $data,$attachments)
    {
        // 使用不同的模板，例如 'mail.custom_template'
        $data['subject'] = $subject;
        $this->mailer->sendTo($subject, $email, $data, 'mail.custom_template',$attachments);
    }

}