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
					<li><h2 id="elements">遊戲紀錄</h2></li>
				</ul>
				<div class="table-wrapper">
					<table class="alt">
						<thead>
							<tr>
								<th>流水號</th>
								<th>局號</th>
								<th>玩家</th>
								<th>開始時間</th>
								<th>結束時間</th>
							</tr>
						</thead>
						<tbody>
							@forelse($aGameRecordList as $oGameRecordData)
							<tr>
								<td><a href={{App\Libraries\Manage\ChildrenLib::sGetGameRecordDetailLink($oGameRecordData->id)}}>{{$oGameRecordData->id}}</a></td>
								<td>{{$oGameRecordData->hashkey}}</td>
								<td>@foreach(json_decode($oGameRecordData->userids,true) as $iUserId) <div>{{App\Models\User::sGetUserName($iUserId)}}</div> @endforeach</td>
								<td>{{$oGameRecordData->created_at}}</td>
								<td>{{$oGameRecordData->updated_at}}</td>

							</tr>
							@empty
							<tr>
								<td colspan="5">無遊戲紀錄</td>
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