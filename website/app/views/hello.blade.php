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
@stop
<body>
	<div class="welcome">
		<a href="" title="PAD">{{HTML::image('img/logo.png')}}</a>
        <h1>You have arrived.</h1>
	</div>
</body>
</html>
