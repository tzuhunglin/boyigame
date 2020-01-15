@extends('layouts.default')

@section('content')
<style>
#formsubmit
{
	font-size: 20px;
	width:60px;
	height:40px;line-height: 40px;
	background-color: rgba(0, 0, 0, 0.15);
	border-radius: 8px;
	color:black;
	cursor: pointer;
}

</style>
<section id="one" class="wrapper style2">
	<div class="inner">
		<div class="box">
			<div class="content">
				<h2 class="align-center">建立下級</h2>
				<hr />
				<form action="#" method="post">
					{{ csrf_field()}}
					<div class="field half first">
						<label for="name">暱稱</label>
						<input name="name" id="name" type="text" placeholder="Name">
					</div>
					<div class="field half">
						<label for="email">電郵</label>
						<input name="email" id="email" type="email" placeholder="Email">
					</div>
					<div class="field half first">
						<label for="password">密碼</label>
						<input name="password" id="password" type="password" placeholder="password">
					</div>
					<div class="field half">
						<label for="password-confirm">密碼確認</label>
						<input name="password-confirm" id="password-confirm" type="password" placeholder="password-confirm">
					</div>
				</form>
				<ul class="actions align-center">
					<li><div id="formsubmit">建立</div></li>
				</ul>
			</div>
		</div>
	</div>
</section>
<script type="text/javascript">
var sPostUrl = "{{ url('/register') }}" ;
var sRedirectUrl = "{{App\Libraries\Manage\ChildrenLib::sGetIndexLink(Auth::user()->id)}}";

$(document).ready(function(){
	$('#formsubmit').click(function() {
		vPostCreate();
	});
});

function vPostCreate()
{
	if(bPostCheck()==false)
	{
		return;
	}
	vAjax();
}

function vAjax()
{
	$.ajax({
		type: "POST",
		url: sPostUrl,
		data: {
			'name' : $('#name').val(),
			'password' : $('#password').val(),
			'password-confirm' : $('#password-confirm').val(),
			'keeppoint' : $('#keeppoint :selected').val(),
			'email' : $('#email').val(),
			'_token' : $('input[name=_token]').val()
		},
		success: function(data){
			if(data.status==true)
			{
				window.location.replace(sRedirectUrl);
			}
			else
			{
				alert(data.message);
			}
		}
	});
}

function bPostCheck()
{
	if(!validateEmail($('#email').val()))
	{
		alert("email error");
		return false;
	}

	if($('#password').val()!==$('#password-confirm').val())
	{
		alert("password confirm error");
		return false;
	}

	if($('#name').val().length < 1 || $('#name').val().length > 50)
	{
		alert("name error");
		return false;
	}

	return true;
}

function validateEmail(email) {
    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}
</script>
@endsection