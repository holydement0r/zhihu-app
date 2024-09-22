<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UsersController extends Controller
{
    //用户头像
    public function avatar()
    {
        return view('users.avatar');
    }

    //修改用户头像
    public function changeAvatar(Request $request)
    {
        // 获取上传的文件
        $file = $request->file('img');
        
        // 设置文件保存路径和文件名
        $filename = 'uploads/heads/' . md5(time() . user()->id) . '.' . $file->getClientOriginalExtension();
        
        // 将文件保存到 public/uploads/heads 目录中
        $file->move(public_path('uploads/heads'), $filename);
        
        // 更新用户头像路径
        user()->avatar = '/uploads/heads/' . basename($filename);
        user()->save();
        
        // 返回保存后的头像 URL
        return ['url' => user()->avatar];
    }
}
