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
						<thead>
							<tr>
								<th></th>
								<th></th>
								<th>點數</th>
								<th>下注</th>
								<th>保險</th>
								<th>雙倍</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>莊家</td>
								<td>@foreach($oLotteryOrderData->aBankerInfo->aCards as $iCardCode) {{$iCardCode}} @endforeach</td>
								<td>implode("/",$oLotteryOrderData->aBankerInfo->aPoints)</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection