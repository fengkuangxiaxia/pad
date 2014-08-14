<?php
$is_active = function ($name='') use ($active)
{
    if ($active === $name)
        return ' class="active"';
    else
        return '';
}
?>

<!-- Fixed navbar -->
<div class="navbar navbar-default navbar-fixed-top" role="navigation"
            <div class="navbar-inner">
                <div class="container">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                            <span class="sr-only">切换菜单栏</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="{{ route('home') }}">PAD</a>
                    </div>
                    <div class="navbar-collapse collapse">
                        <ul class="nav navbar-nav">
                            <li{{ $is_active('home') }}><a href="{{ route('home') }}">首页</a></li>
                            @if(Auth::user()){{--用户--}}
                                <li{{ $is_active('monster') }}><a href="{{ route('monster') }}">我的宠物</a></li>
                                <li{{ $is_active('team') }}><a href="{{ route('team') }}">队伍匹配</a></li>
                            @endif
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            @if(Auth::guest()){{--游客--}}
                                <li{{ $is_active('signin') }}><a href="{{ route('signin') }}">登录</a></li>
                                <li{{ $is_active('signup') }}><a href="{{ route('signup') }}">注册</a></li>
                            @elseif(! Auth::user()->isAdmin){{--普通登录用户--}}
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        [ {{ Auth::user()->username }} ]
                                        <b class="caret"></b>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li class="divider"></li>
                                        <li><a href="{{ route('logout') }}">退出</a></li>
                                    </ul>
                                </li>
                            @elseif(Auth::user()->isAdmin){{--管理员--}}
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                                        [ {{ Auth::user()->username }} ]
                                        <b class="caret"></b>
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li class="divider"></li>
                                        <li><a href="{{ route('logout') }}">退出</a></li>
                                    </ul>
                                </li>
                            @endif
                        </ul>
                    </div><!--/.nav-collapse -->
                </div>
            </div>
        </div>