<?php


namespace App\Mailer;

use Mail;
use Naux\Mail\SendCloudTemplate;

class Mailer
{
    public function sendTo($subject,$email,array $data,$template='mail.verify_template', $attachments = [])
    {

        // file_put_contents("./log",var_export($data,true));
        Mail::send($template, $data, function ($message) use ($email, $subject, $attachments) {
            $message->from(env('MAIL_USERNAME'), 'joker');
            $message->to($email)->subject($subject);
            // 添加附件
            foreach ($attachments as $attachment) {
                $message->attach($attachment['path'], [
                    'as' => $attachment['name'], // 附件原始名称
                    'mime' => $attachment['mime'], // 附件 MIME 类型
                ]);
            }
        });
    }
}