<?php

namespace App\Http\Controllers;

use App\User;
use App\MailLog;
use Auth;
use Illuminate\Http\Request;
use App\Mailer\UserMailer;

class EmailController extends Controller
{

    protected $userMailer;

    public function __construct(UserMailer $userMailer)
    {
        $this->userMailer = $userMailer; // 注入 UserMailer
    }

    public function index()
    {
        // 获取邮件发送记录
        $emails = MailLog::all(); // 获取所有邮件记录

        return view('admin.email.index', compact('emails')); // 返回视图
    }

    public function send(Request $request)
    {
        // 验证输入
        $this->validate($request, [
            'emailto' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // 可选附件，最大10MB
        ]);
    
        $attachments = [];
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachments[] = [
                'path' => $file->getRealPath(), // 附件真实路径
                'name' => $file->getClientOriginalName(), // 附件原始名称
                'mime' => $file->getClientMimeType(), // 附件 MIME 类型
            ];
        }
    
        $data = [
            'write' => $request->input('message'),
            // 可以添加其他数据
        ];
        
        $this->userMailer->sendCustomEmail($request->input('emailto'), $request->input('subject'), $data, $attachments);
    
        MailLog::create([
            'recipient' => $request->input('emailto'),
            'subject' => $request->input('subject'),
            'content' => $request->input('message'), // 保存邮件内容
            'attachment' => $attachments ? json_encode(array_map(function ($file) {
                return $file['name']; // 只提取文件名
            }, $attachments)) : null,
        ]);
    
        return redirect()->back()->with('success', '邮件已成功发送！');
    }
    

    //邮箱验证
    public function verify($token)
    {
        $user = User::where('confirmation_token',$token)->first();

        if(is_null($user)){
            flash('邮箱验证失败','danger');
            return redirect('/');
        }
        $user->is_active = 1;
        $user->confirmation_token = str_random(40);
        $user->save();

        flash('邮箱验证成功','success');
        Auth::login($user);
        return redirect('/');
    }
}
