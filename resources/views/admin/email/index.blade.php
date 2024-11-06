@extends('admin.app')

@section('content')
    <h1>邮件发送记录</h1>
    <table class="table">
        <thead>
            <tr>
                <th>收件人</th>
                <th>主题</th>
                <th>内容</th>
                <th>附件</th>
                <th>发送时间</th>
            </tr>
        </thead>
        <tbody>
            @foreach($emails as $email)
                <tr>
                    <td>{{ $email->recipient }}</td>
                    <td>{{ $email->subject }}</td>
                    <td>{{ $email->content }}</td>
                    <td>
                        @if($email->attachment)
                            @php
                                $attachments = json_decode($email->attachment); // 解码附件
                            @endphp
                            @foreach($attachments as $file)
                                {{ $file }}<br> <!-- 强制转换为字符串 -->
                            @endforeach
                        @else
                            无附件
                        @endif
                    </td>
                    <td>{{ $email->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
