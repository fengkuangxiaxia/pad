@extends('l.authority', array('active' => 'home'))
@section('style')

		body {
			margin:0;
			text-align:center;
			color: #999;
		}

		.welcome {
			width: 300px;
			height: 200px;
			position: absolute;
			left: 50%;
			top: 50%;
			margin-left: -150px;
			margin-top: -100px;
		}

		a, a:visited {
			text-decoration:none;
		}
        
        .close{ background-color:black}
        
@stop
<body>
	<div class="welcome">
        @if($rand < 9)
            <a href="{{ route('home') }}" title="PAD">{{HTML::image('img/monsters/797.jpg')}}</a>
            <h1>You have arrived.</h1>
        @else
            <a href="{{ route('home') }}" title="PAD">{{HTML::image('img/monsters/1323.jpg')}}</a>
            <h1>I am watching you.</h1>
        @endif
	</div>
</body>

@section('end')
    @parent
    <script>
        if($('h1').text() == 'I am watching you.'){
            $("body div[class!='welcome']").addClass("close");
            $("body").addClass("close");
        }
    </script>
@stop
</html>
