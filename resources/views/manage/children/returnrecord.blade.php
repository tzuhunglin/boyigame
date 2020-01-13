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
					<li><h2 id="elements">返點紀錄</h2></li>
				</ul>
				<div class="table-wrapper">
					<table class="alt">
						<thead>
							<tr>
								<th>流水號</th>
								<th>彩種</th>
								<th>投注者</th>
								<th>返點</th>
								<th>時間</th>
							</tr>
						</thead>
						<tbody>
							@forelse($aReturnRecordList as $aReturnRecordData)
							<tr>
								<td>{{$aReturnRecordData->id}}</td>
								<td>{{$aReturnRecordData->name}}</td>
								<td>{{App\Models\User::sGetUserName($aReturnRecordData->playerid)}}</td>
								<td>{{$aReturnRecordData->returnmoney}}</td>
								<td>{{$aReturnRecordData->created_at}}</td>
							</tr>
							@empty
							<tr>
								<td colspan="5">無返點紀錄</td>
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