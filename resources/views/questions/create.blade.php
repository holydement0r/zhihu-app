@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">发布问题</div>
                    <div class="panel-body">
                        <form action="/questions" method="post">
                            {!! csrf_field() !!}
                            <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                <label for="title">标题</label>
                                <input type="text" value="{{old('title')}}" name="title" class="form-control"
                                       placeholser="标题" id="title">
                                @if ($errors->has('title'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <div class="form-group">
                                <select name="topics[]" class="js-example-placeholder-multiple js-data-example-ajax form-control" multiple="multiple">
                                    <option value="AL"></option>
                                    <option value="WY"></option>
                                </select>
                            </div>

                            <div class="form-group{{ $errors->has('body') ? ' has-error' : '' }}">
                                <label for="body">描述</label>

                                <script id="container" name="body" type="text/plain" style="height:200px;">{!! old('body') !!}</script>

                                @if ($errors->has('body'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('body') }}</strong>
                                    </span>
                                @endif
                            </div>

                            <button type="submit" class="ui button teal pull-right">发布问题</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>

@section('js')
    <!-- 实例化编辑器 -->
    <script type="text/javascript">

        var ue = UE.getEditor('container', {
            toolbars: [
                ['bold', 'italic', 'underline', 'strikethrough', 'blockquote', 'insertunorderedlist', 'insertorderedlist', 'justifyleft', 'justifycenter', 'justifyright', 'link', 'insertimage', 'fullscreen']
            ],
            elementPathEnabled: false,
            enableContextMenu: false,
            autoClearEmptyNode: true,
            wordCount: false,
            imagePopup: false,
            autotypeset: {indent: true, imageBlockLine: 'center'}
        });
        ue.ready(function () {
            ue.execCommand('serverparam', '_token', '{{ csrf_token() }}'); // 设置 CSRF token.
        });

        $(function () {
    // 格式化选择的topic
    function formatTopic(topic) {
        return "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title'>" +
            (topic.text ? topic.text : "Laravel") +
            "</div></div></div>";
    }

    function formatTopicSelection(topic) {
        return topic.text || topic.name || topic.id;
    }

    // 初始化Select2
    $(".js-example-placeholder-multiple").select2({
        tags: true,
        placeholder: '选择相关话题',
        minimumInputLength: 0,
        ajax: {
            url: '/api/topics',
            dataType: 'json',
            delay: 50,
            data: function (params) {
                return {
                    q: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: data.map(function (item) {
                        return {
                            id: item.id,
                            text: item.name // Select2 需要 `text` 属性来显示选项
                        };
                    })
                };
}
,
            cache: true
        },
        templateResult: formatTopic,
        templateSelection: formatTopicSelection,
        escapeMarkup: function (markup) { return markup; }
    });

    // 表单验证逻辑
    $('form').on('submit', function (e) {
        var isValid = true;
        
        // 验证标题
        var title = $('input[name="title"]').val();
        if (title.trim().length < 8) {
            isValid = false;
            displayError('title', '标题不能少于8个字符');
        } else {
            clearError('title');
        }

        // 验证话题
        var topics = $('.js-example-placeholder-multiple').val();
        if (!topics || topics.length === 0) {
            isValid = false;
            displayError('topics', '请选择至少一个话题');
        } else {
            clearError('topics');
        }

        // 验证描述
        var body = UE.getEditor('container').getContent();
        if (body.trim().length < 26) {
            isValid = false;
            displayError('body', '描述不能少于26个字符');
        } else {
            clearError('body');
        }

        // 如果验证失败，阻止表单提交
        if (!isValid) {
            e.preventDefault(); // 阻止表单提交
        }
    });

    // 显示错误信息
    function displayError(fieldName, message) {
        var field = $('input[name="' + fieldName + '"], select[name="' + fieldName + '"], textarea[name="' + fieldName + '"]');
        field.closest('.form-group').addClass('has-error');
        if (field.closest('.form-group').find('.help-block').length === 0) {
            field.after('<span class="help-block"><strong>' + message + '</strong></span>');
        }
    }

    // 清除错误信息
    function clearError(fieldName) {
        var field = $('input[name="' + fieldName + '"], select[name="' + fieldName + '"], textarea[name="' + fieldName + '"]');
        field.closest('.form-group').removeClass('has-error');
        field.closest('.form-group').find('.help-block').remove();
    }
});

    </script>
@endsection
@endsection

