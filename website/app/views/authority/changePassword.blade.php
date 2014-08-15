@extends('l.authority', array('active' => 'changePassword'))

@section('title') @parent 修改密码 @stop

@section('style')
    @parent
    .form-changePassword {
        max-width: 330px;
        padding: 15px;
        margin: 0 auto;
    }
    .form-changePassword .form-changePassword-heading,
    .form-changePassword .checkbox {
        margin-bottom: 10px;
    }
    .form-changePassword .checkbox {
        font-weight: normal;
    }
    .form-changePassword .form-control {
        position: relative;
        font-size: 16px;
        height: auto;
        padding: 10px;
        -webkit-box-sizing: border-box;
           -moz-box-sizing: border-box;
                box-sizing: border-box;
    }
    .form-changePassword input{
        margin-top: 10px;
    }
    .form-changePassword button{
        margin-top: 10px;
    }
    .form-changePassword strong.error{
        color: #b94a48;
    }
    
@stop

@section('container')

    {{ Form::open(array('class' => 'form-changePassword', 'role' => 'form')) }}
        <h2 class="form-changePassword-heading">修改密码</h2>
        <input name="old_password" type="password" class="form-control" placeholder="旧密码" required autofocus>
        {{ $errors->first('old_password', '<strong class="error">:message</strong>') }}
        <div class="input-group">
            <input name="password" type="password" class="form-control" placeholder="新密码" required>
            <span class="input-group-btn">
                <button class="btn btn-default" type="button" data-toggle="popover" data-content="请使用字母、数字、下划线、中划线。长度在6-16位之间。">?</button>
            </span>
        </div>
        {{ $errors->first('password', '<strong class="error">:message</strong>') }}
        <input name="password_confirmation" type="password" class="form-control" placeholder="确认新密码" required>
        <button class="btn btn-lg btn-info btn-block" type="submit">修改</button>
    {{ Form::close() }}

@stop


@section('end')
    @parent
    <script>
        $('[data-toggle]').popover({container:'body'});
    </script>
@stop
