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
					<li><h2 id="elements">下級資料</h2></li>
					@if($iTargetUserId == Auth::user()->id)
					<li style="float:right;"><a href="{{App\Libraries\Manage\ChildrenLib::sGetCreateLink()}}" class="button alt">新增下級</a></li>
					@endif
				</ul>
				<div class="table-wrapper">
					<table class="alt">
						<thead>
							<tr>
								<th>姓名</th>
								<th>電郵</th>
								<th>保留返點</th>
								<th>餘額</th>
								<th><th>
							</tr>
						</thead>
						<tbody>
							@forelse($aChildrenList as $oChildrenData)
							<tr>
								<td>{{$oChildrenData->name}}</td>
								<td>{{$oChildrenData->email}}</td>
								<td>{{$oChildrenData->keeppoint}}%</td>
								<td>{{$oChildrenData->totalmoney}}</td>
								<td>
									<a href="{{App\Libraries\Manage\ChildrenLib::sGetBetRecordLink($oChildrenData->id)}}">投注紀錄</a> / 
									<a href="{{App\Libraries\Manage\ChildrenLib::sGetGameRecordLink($oChildrenData->id)}}">遊戲紀錄</a> / 
									<a href="{{App\Libraries\Manage\ChildrenLib::sGetReturnRecordLink($oChildrenData->id)}}">返點紀錄</a> / 
									<a href="{{App\Libraries\Manage\ChildrenLib::sGetIndexLink($oChildrenData->id)}}">下級</a>
								</td>
							</tr>
							@empty
							<tr>
								<td colspan="5">無下級資料</td>
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
