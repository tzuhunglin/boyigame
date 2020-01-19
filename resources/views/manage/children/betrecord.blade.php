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
					<li><h2 id="elements">投注紀錄</h2></li>
				</ul>
				<div class="table-wrapper">
					<table class="alt">
						<thead>
							<tr>
								<th>流水號</th>
								<th>彩種</th>
								<th>玩法</th>
								<th>獎期</th>
								<th>投注金額</th>
								<th>返點</th>
								<th>號碼</th>
								<th>中獎</th>
								<th>開獎</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							@forelse($aLotteryOrderList as $oLotteryOrderData)
							<tr>
								<td><a href={{App\Libraries\Manage\ChildrenLib::sGetBetRecordDetailLink($oLotteryOrderData->id)}}>{{$oLotteryOrderData->id}}</a></td>
								<td>{{$oLotteryOrderData->lottery}}</td>
								<td>{{$oLotteryOrderData->type}}</td>
								<td>{{$oLotteryOrderData->issue}}</td>
								<td>{{$oLotteryOrderData->betmoney}}</td>
								<td>@if($oLotteryOrderData->return==1) Y @else N @endif</td>
								<td><span>{{implode(",",json_decode($oLotteryOrderData->code,true))}}</span></td>
								<td>@if($oLotteryOrderData->win==1) Y @else N @endif</td>
								<th>@if($oLotteryOrderData->award==1) 已開 @else 未開 @endif</th>
								<td>{{$oLotteryOrderData->created_at}}</td>
							</tr>
							@empty
							<tr>
								<td colspan="8">無投注紀錄</td>
							</tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection