<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MailLog extends Model
{
    protected $table = 'mail_logs'; // 对应的数据库表名

    protected $fillable = [
        'recipient',  // 收件人
        'subject',    // 邮件主题
        'content',    // 邮件内容
        'attachment', // 附件信息
    ];

    public $timestamps = true; // 自动管理 created_at 和 updated_at 字段
}
