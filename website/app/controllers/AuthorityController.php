<?php

class AuthorityController extends BaseController
{
    /**
     * 页面：登录
     * @return Response
     */
    public function getSignin()
    {
        $data['title'] = "登录";
		$data['heading'] = "登录";
        $data['blank'] = 1;
        return View::make('authority.signin', $data);
    }
    
    /**
     * 动作：登录
     * @return Response
     */
    public function postSignin()
    {
        // 凭证
        $credentials = array('username'=>Input::get('username'), 'password'=>Input::get('password'));
        // 是否记住登录状态
        $remember    = Input::get('remember-me', 0);
        // 验证登录
        if (Auth::validate($credentials)) {
            Auth::attempt($credentials, $remember);
            return Redirect::intended();
        } else {
            // 登录失败，跳回
            return Redirect::back()
                ->withInput()
                ->withErrors(array('attempt' => '“用户名”或“密码”错误，请重新登录。'));
        }
    }

    /**
     * 动作：退出
     * @return Response
     */
    public function getLogout()
    {
        Auth::logout();
        return Redirect::to('/');
    }
    
    /**
     * 页面：注册
     * @return Response
     */
    public function getSignup()
    {
        return View::make('authority.signup');
    }
    
    /**
     * 动作：注册
     * @return Response
     */
    public function postSignup()
    {
        // 获取所有表单数据.
        $data = Input::all();
        // 创建验证规则
        $rules = array(
            'username'    => 'required|unique:users',
            'password' => 'required|alpha_dash|between:6,16|confirmed',
        );
        // 自定义验证消息
        $messages = array(
            'username.required'      => '请输入用户名。',
            'username.unique'        => '此用户名已被使用。',
            'password.required'   => '请输入密码。',
            'password.alpha_dash' => '密码格式不正确。',
            'password.between'    => '密码长度请保持在:min到:max位之间。',
            'password.confirmed'  => '两次输入的密码不一致。',
        );
        // 开始验证
        $validator = Validator::make($data, $rules, $messages);
        if ($validator->passes()) {
            // 验证成功，添加用户
            $user = new User;
            $user->username = Input::get('username');
            $user->password = Hash::make(Input::get('password'));
            if ($user->save()) {
                // 添加成功
                return Redirect::route('signin');
            } else {
                // 添加失败
                return Redirect::back()
                    ->withInput()
                    ->withErrors(array('add' => '注册失败。'));
            }
        } else {
            // 验证失败，跳回
            return Redirect::back()
                ->withInput()
                ->withErrors($validator);
        }
    }

    /**
     * 页面：进行密码重置
     * @return Response
     */
    public function getReset($token)
    {
        // 数据库中无令牌，抛出404
        is_null(PassowrdReminder::where('token', $token)->first()) AND App::abort(404);
        return View::make('authority.password.reset')->with('token', $token);
    }

    /**
     * 动作：进行密码重置
     * @return Response
     */
    public function postReset()
    {
        // 调用系统自带密码重置流程
        $credentials = Input::only(
            'username', 'password', 'password_confirmation', 'token'
        );

        $response = Password::reset($credentials, function ($user, $password) {
            // 保存新密码
            $user->password = $password;
            $user->save();
            // 登录用户
            Auth::attempt($credentials);
        });

        switch ($response) {
            case Password::INVALID_PASSWORD:
                // no break
            case Password::INVALID_TOKEN:
                // no break
            case Password::INVALID_USER:
                return Redirect::back()->with('error', Lang::get($response));
            case Password::PASSWORD_RESET:
                return Redirect::to('/');
        }
    }

}
