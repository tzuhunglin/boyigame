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
								<th>雙倍</th>
								<th>下注結算</th>
								<th>保險</th>
								<th>保險結算</th>
								<th>總結算</th>
								<th>輸贏</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>莊家</td>
								<td>@foreach($oGameRecord->aDetail['aBankerInfo']['aCards'] as $iCode) <span>{{App\Libraries\Product\Card\Poke\BlackjackLib::sGetCard($iCode)}}</span> @endforeach</td>
								<td>{{implode("/",$oGameRecord->aDetail['aBankerInfo']['aPoints'])}}</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
								<td>-</td>
							</tr>
							@foreach($oGameRecord->aDetail['aUserInfoList'] as $aUserInfoData)
							<tr>

								<td>{{App\Models\User::sGetUserName($aUserInfoData['iUserId'])}}</td>
								<td>@foreach($aUserInfoData['aCards'][0] as $iCode) <span>{{App\Libraries\Product\Card\Poke\BlackjackLib::sGetCard($iCode)}}</span> @endforeach</td>
								<td>{{implode("/",$aUserInfoData['aPoints'][0])}}</td>
								<td>{{$aUserInfoData['iBetAmount']}}</td>
								<td>@if($aUserInfoData['iDouble']==3) Y @else N @endif</td>
								<td>{{App\Libraries\Product\Card\Poke\BlackjackLib::iGetUserBetSumUp($aUserInfoData['iUserId'],$aUserInfoData['iBetAmount'],$aUserInfoData['iDouble'],$aUserInfoData['iWinLose'])}}</td>
								<td>@if($aUserInfoData['iInsurance']==3) Y @else N @endif</td>

								<td>{{App\Libraries\Product\Card\Poke\BlackjackLib::iGetUserInsuranceSumUp($aUserInfoData['iUserId'],$aUserInfoData['iBetAmount'],$aUserInfoData['iInsurance'],$oGameRecord->aDetail['aBankerInfo']['aCards'],$oGameRecord->aDetail['aBankerInfo']['aPoints'])}}</td>


								<td>{{App\Libraries\Product\Card\Poke\BlackjackLib::iGetUserTotalSumUp($aUserInfoData,$oGameRecord->aDetail['aBankerInfo']['aCards'],$oGameRecord->aDetail['aBankerInfo']['aPoints'])}}</td>
								<td>@if($aUserInfoData['iWinLose']==1) 贏 @else 輸 @endif</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>
@endsection