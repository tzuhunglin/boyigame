@extends('layouts.default')

@section('content')
<style>
.alt
{
	text-align:center;
}
.alt th
{
	text-align:center;
}
</style>
<section id="one" class="wrapper style2">
	<div class="inner">
		<div class="box">
			<div class="content">
				<ul class="actions">
					<li><h2 id="elements">投注內容</h2></li>
				</ul>
				<div class="table-wrapper">
					<table class="alt">
						<tbody>
							<tr>
								<td>彩種</td>
								<td>{{$oLotteryOrderData->lottery}}</td>
								<tr>
								<td>玩法</td>
								<td>{{$oLotteryOrderData->type}}</td>						</tr>

							<tr>
								<td>獎期</td>
								<td>{{$oLotteryOrderData->issue}}</td>
							</tr>
							<tr>
								<td>開獎號碼</td>
								<td><span>@if(!empty($oIssueInfo)){{implode(",",json_decode($oIssueInfo->code,true))}}@endif</span></td>
							</tr>
							<tr>
								<td>投注號碼</td>
								<td><span>{{implode(",",json_decode($oLotteryOrderData->code,true))}}</span></td>
							</tr>
							<tr>
								<td>投注金額</td>
								<td><span>{{$oLotteryOrderData->betmoney}}</span></td>
							</tr>
							<tr>
								<td>賠率</td>
								<td><span>{{$fOdds}}</span></td>
							</tr>
							<tr>
								<td>返點</td>
								<td>@if($oLotteryOrderData->return==1) {{$oLotteryOrderData->betmoney * Auth::user()->keeppoint / 100}} @else 0 @endif</td>
							</tr>
							<tr>
								<td>中獎金額</td>
								<td><span>@if($oLotteryOrderData->win==1){{$oLotteryOrderData->realbetmoney * $fOdds}} @else 0 @endif</span></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection
