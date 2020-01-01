@extends('layouts.default')

@section('content')
<style type="text/css">
.pointarea
{
	font-size: 50px;
	text-align: center;
}
.card
{
	width:20%;
	height:100%;
	font-size: 30px;
	float:left;
	text-align: center;
}
</style>
<section id="one" class="wrapper style2">
	<div class="inner">
		<div class="box">
			<div class="content">
				<div class="table-wrapper">
					<table class="alt">
						<tbody>
							<tr >
								<td id="banker" colspan="2" style="height:200px">
									<div style="width:33%;height:200px;float:left">
										<div class="namearea">banker</div>
									</div>
									<div style="width:33%;height:200px;float:left">
										<div class="cardarea" style="height:60%;width:100%;" >
											<div class="card"></div>
											<div class="card"></div>
											<div class="card"></div>
											<div class="card"></div>
											<div class="card"></div>
										</div>
										<div class="pointarea"></div>
									</div>
									<div style="width:33%;height:200px;float:left"></div>
								</td>
							</tr>
							<tr>
								<td id="player1" style="height:200px;width:50%">
									<div style="width:32%;height:200px;float:left">
										<div class="namearea">palyer1</div>
										<div class="moneyarea">餘額:<span class="money"></span></div>
										<div class="betarea">下注金額:<span class="bet"></span></div>
									</div>
									<div style="width:68%;height:200px;float:left">
										<div class="cardarea" style="height:60%;width:100%;" >
											<div class="card"></div>
											<div class="card"></div>
											<div class="card"></div>
											<div class="card"></div>
											<div class="card"></div>
										</div>
										<div class="pointarea" style=""></div>
									</div>
								</td>
								<td id="player2" style="height:200px;width:50%">
									<div style="width:68%;height:200px;float:left">
										<div class="cardarea" style="height:60%;width:100%;" >
											<div class="card"></div>
											<div class="card"></div>
											<div class="card"></div>
											<div class="card"></div>
											<div class="card"></div>
										</div>
										<div class="pointarea"></div>

									</div>
									<div style="width:32%;height:200px;float:left">
										<div class="namearea" style="float: right;clear:both">palyer2</div>
										<div class="moneyarea" style="float: right;clear:both">餘額:<span class="money"></span></div>
										<div class="betarea" style="float: right;clear:both">下注金額:<span class="bet"></span></div>
									</div>
								</td>
							</tr>
							<tr>
								<td id="user" colspan="2" style="height:200px">
									<div style="width:33%;height:200px;float:left">
										<div class="namearea">user</div>
										<div class="moneyarea">餘額:<span class="money"></span></div>
										<div class="betarea">下注金額:<span class="bet"></span></div>

									</div>
									<div style="width:33%;height:200px;float:left">
										<div class="cardarea" style="height:60%;width:100%;">
											<div class="card"></div>
											<div class="card"></div>
											<div class="card"></div>
											<div class="card"></div>
											<div class="card"></div>
										</div>
										<div class="pointarea"></div>
									</div>
									<div style="width:33%;height:200px;float:left">
										<dir class="controlarea">

										</dir>
									</div>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script src="{{ asset('js/blackjack.js') }}" defer></script>
<script type="text/javascript">

var iUserId = parseInt("{{$iUserId}}");

Notification.TOKEN = "{{$sHashKey}}";
var sHashKey = "{{$sHashKey}}";



$(document).ready(function(){
	// alert("blade.php");
	// console.log(oGameData);
	// console.log(iUserId);

	// console.log(JSON.parse(oGameData));
});


</script>
@endsection