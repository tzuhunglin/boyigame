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
.namearea
{
	font-size: 30px;
}
</style>
<section id="one" class="wrapper style2">
	<div class="inner">
		<div class="box">
			<div class="content">
				<div class="table-wrapper">
					<table class="alt">
						<tbody>
							<tr>
								<td id="banker" colspan="2" style="height:200px">
									<div style="width:33%;height:200px;float:left">
										<div class="namearea">莊家</div>
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
										<div class="insurancearea"></div>
										<div class="doublearea"></div>
										<div class="winlosearea" style="font-size: 30px;"></div>
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
										<div class="insurancearea" style="float: right;clear:both"></div>
										<div class="doublearea" style="float: right;clear:both"></div>
										<div class="winlosearea" style="font-size: 30px;float: right;clear:both"></div>
									</div>
								</td>
							</tr>
							<tr>
								<td id="user" colspan="2" style="height:200px">
									<div style="width:33%;height:200px;float:left">
										<div class="namearea">user</div>
										<div class="moneyarea">餘額:<span class="money"></span></div>
										<div class="betarea">下注金額:<span class="bet"></span></div>
										<div class="doublearea"></div>
										<div class="insurancearea"></div>
										<div class="winlosearea" style="font-size: 30px;"></div>
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
										<dir class="controlarea"></dir>
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
var sHashKey = "{{$sHashKey}}";
var bStatus = "{{$bStatus}}";
var sMessage = "{{$sMessage}}";


if(sHashKey!="")
{
	Notification.TOKEN = sHashKey;
}

$(document).ready(function(){
	if(bStatus==false)
	{
		alert(sMessage);
		window.location.href ="/";
	}
});

</script>
@endsection
