@extends('l.base')

@section('title') PAD @parent @stop

@section('beforeStyle')
    @include('css')
@parent @stop

@section('style')
body
{
    padding-bottom: 0;
    background-color: #FFFFFF;
}
@parent @stop

@section('body')

    @include('_header')

    <div class="container panel" style="margin-top:5em; padding-bottom:1em;">
        @yield('container')
    </div>

@stop

@section('end')
    @include('js')
    {{ HTML::script('js/jquery.optionTree.js') }}
@parent @stop
