@extends('layouts.default')

@section('content')
<style>
    .field .issuecodearea
    {
		width:600px;
		margin: 0px auto;
    }
    .field .issuecodearea .issuecodebox
    {
		float:left;
		margin-left:50px;
		margin-right:50px;
    }
    .field .issuecodearea .issuecodebox .issuecode
    {
		border: solid 2px;
		width:100px;
		height:100px;
		text-align:center;
		font-size: 50px;
		line-height: 100px;
		border-radius: 50px;
		color:red;
    }
    .clearboth
    {
    	clear:both;
    }
    .field .betcodearea
    {
		width:1000px;
		margin: 0px auto;
    }
    .field .betcodearea .betcodebox
    {
		float:left;
		margin-left:20px;
		margin-right:20px;
    }
    .field .betcodearea .betcodebox .betcode
    {
		border: solid 2px;
		width:50px;
		height:50px;
		text-align:center;
		font-size: 20px;
		line-height: 50px;
		border-radius: 50px;
		color:black;
		cursor:pointer;
    }
    .field .betcodearea .betcodebox .betdigit
    {
		width:50px;
		height:50px;
		text-align:center;
		font-size: 20px;
		line-height: 50px;
		color:black;
    }
    .gametypearea
    {
    	height:140px;
    	margin: 0 auto;
    }
    .gametypebox
    {
		padding-top: 40px;
    	width:300px;
    	float:right;
    }
    .gametypename
    {
    	color:black;
    }

    .betinfoarea
    {
		width:1000px;
		margin: 0px auto;
    }
    .betinfoarea .betinfobox
    {
		float:left;
		margin-left:20px;
		margin-right:20px;
		width:200px;
    }

    .betinfoarea .select-wrapper
    {
    	float:right;
    	width:130px;
    }

    .betinfoarea .betinfobox span
    {
		font-size: 22px;
		color:black;
    }

    #multiple
    {
    	text-align:center;
    }
</style>
<section id="one" class="wrapper style2">
	<div class="inner">
		<div class="box">
			<div class="content">
				<h2 class="align-center">{{ trans('messages.product.lottery.3d.jisupailie3.name') }}</h2>
				<h3 class="align-center"><span id="issue">{{$oJisupailie3->sIssue}}</span><span> 期</span><span>&emsp; &emsp;</span><span id="minute">12</span><span> : </span><span id="second">34</span></h3>
				<hr />
				<form action="" method="post">
					{{ csrf_field()}}
					<div class="issuearea">
						<div class="field" >
							<div class="issuecodearea">
								<div class="issuecodebox">
									<div class="issuecode" id="code0">{{$oJisupailie3->aCode[0]}}</div>
								</div>
								<div class="issuecodebox">
									<div class="issuecode" id="code1">{{$oJisupailie3->aCode[1]}}</div>
								</div>
								<div class="issuecodebox">
									<div class="issuecode" id="code2">{{$oJisupailie3->aCode[2]}}</div>
								</div>
								<div class="clearboth"></div>
							</div>
						</div>
					</div>
					<div class="betarea">
						<div class="gametypearea">
							<div class="gametypebox">
								<input type="radio" id="yimabudingdan" name="gametype" odds="{{$aAllGameTypeOdds['yimabudingdan']}}">
								<label for="yimabudingdan" ><span class="gametypename" >一码不定胆</span></label>
							</div>
							<div class="gametypebox">
								<input type="radio" id="sanmatzushiuan" name="gametype" odds="{{$aAllGameTypeOdds['sanmatzushiuan']}}">
								<label for="sanmatzushiuan" ><span class="gametypename">三碼組選</span></label>
							</div>
							<div class="gametypebox">
								<input type="radio" id="sanmajrshiuan" name="gametype" odds="{{$aAllGameTypeOdds['sanmajrshiuan']}}" checked >
								<label for="sanmajrshiuan" ><span class="gametypename">三碼直選</span></label>
							</div>
							<div class="clearboth"></div>
						</div>

						<div name="gametypecodearea">
							<div name="sanmajrshiuan" style="display:none">
								<div class="field" id="hundreds">
									<div class="betcodearea">
										<div class="betcodebox">
											<div class="betdigit">百位</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">0</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">1</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">2</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">3</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">4</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">5</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">6</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">7</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">8</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">9</div>
										</div>
										<div class="clearboth"></div>
									</div>
								</div>
								<div class="field" id="tens">
									<div class="betcodearea">
										<div class="betcodebox">
											<div class="betdigit">十位</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">0</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">1</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">2</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">3</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">4</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">5</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">6</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">7</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">8</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">9</div>
										</div>
										<div class="clearboth"></div>
									</div>
								</div>
								<div class="field" id="ones">
									<div class="betcodearea">
										<div class="betcodebox">
											<div class="betdigit">個位</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">0</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">1</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">2</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">3</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">4</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">5</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">6</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">7</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">8</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">9</div>
										</div>
										<div class="clearboth"></div>
									</div>
								</div>
							</div>
							<div name="sanmatzushiuan" style="display:none">
								<div class="field" id="tzushiuan">
									<div class="betcodearea">
										<div class="betcodebox">
											<div class="betdigit">組選</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">0</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">1</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">2</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">3</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">4</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">5</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">6</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">7</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">8</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">9</div>
										</div>
										<div class="clearboth"></div>
									</div>
								</div>
							</div>
							<div name="yimabudingdan" style="display:none">
								<div class="field" id="dan">
									<div class="betcodearea">
										<div class="betcodebox">
											<div class="betdigit"></div>
										</div>
										<div class="betcodebox">
											<div class="betcode">0</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">1</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">2</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">3</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">4</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">5</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">6</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">7</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">8</div>
										</div>
										<div class="betcodebox">
											<div class="betcode">9</div>
										</div>
										<div class="clearboth"></div>
									</div>
								</div>
							</div>
						</div>

						<div class="betinfoarea">

							<div class="betinfobox">
								<input id="multiple" type="number" value="1" >
								<span>倍</span>
							</div>



							<div class="betinfobox">
								<span >奖金 : </span>
								<div class="select-wrapper" >
									<select name="bonus" id="bonus">
										<option value="0">-0%</option>
										<option value="1">-{{$oUser->keeppoint}}%</option>
									</select>
								</div>
							</div>

							<div class="betinfobox">
									<span>已選</span>
									<span id="betamount">0</span>
									<span>注 共</span>
									<span id="sumamount">0</span>
									<span>元</span>

							</div>

							<div class="betinfobox">
								<div id="bet" class="button special">投注</div>
							</div>
							<div class="clearboth"></div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
@endsection
@section('scripts')
<script type="text/javascript">


var aBetCodes = [];
var sGameTypeName;
var iBasicRate = parseInt("{{$iBasicOdds}}");
var iKeepPoint = parseInt("{{$oUser->keeppoint}}");
var iOdds = 0;
var sPostUrl = "{{App\Libraries\Product\Lottery\Threed\Jisupailie3Lib::sGetbetLink()}}";


$(document).ready(function(){
	vSetCountDownClockerIni();
	setInterval("vSetCountDownClocker()",1000);
	vSetBetCodesInit();
	vSetBetOdds();
	vSetBonus();
	sGameTypeName = $("input[type='radio']:checked").attr('id');
	vSetGameTypeCodeShow(sGameTypeName);
	$('input[type=radio][name=gametype]').change(function() {
		vGameTypeOnChange($(this));
		vSetBetOdds();
		vSetBonus();
	});

	$('#multiple').change(function() {
		vBetAmountCaculate();
	});

	$('.betcode').click(function() {
		vSetBetCodeOnClicked($(this))
	});

	$('#bet').click(function() {
		vPostBet();
	});
});

function vSetCountDownClockerIni()
{
	$("#second").text(iGetCountDownSecond()-1);
	$("#minute").text(iGetRestMinute());
}

function vSetCountDownClocker()
{
	if(parseInt($("#second").text())==30)
	{
		vSetCountDownClockerIni();
		return ;
	}
	var iCountDownSecond = parseInt($("#second").text()) - 1;
	var iCountDownMinute = parseInt($("#minute").text());

	if(iCountDownSecond<0)
	{
		iCountDownSecond = 59;
		iCountDownMinute = parseInt($("#minute").text()) - 1;
		if(iCountDownMinute<0)
		{
			iCountDownMinute = 29;
		}

	}
	$("#second").text(sFillZero(iCountDownSecond));
	$("#minute").text(sFillZero(iCountDownMinute));
}

function iGetRestMinute()
{
	var sCurrentDateTime = new Date();
	var iRestMinute = 0;
	var sToadyStartTime = sCurrentDateTime.getFullYear()+"-"+(sCurrentDateTime.getMonth()+1)+"-"+sCurrentDateTime.getDate()+" 10:30:00";
	var sToadyEndTime = sCurrentDateTime.getFullYear()+"-"+(sCurrentDateTime.getMonth()+1)+"-"+sCurrentDateTime.getDate()+" 21:30:00";
	var iCurrentDateTime = (new Date()).valueOf();
	var iStartDateTime = new Date(sToadyStartTime).getTime();
	var iEndDateTime = new Date(sToadyEndTime).getTime();
	if(iCurrentDateTime<iStartDateTime)
	{
		iRestMinute  = iSecondToMinute(iStartDateTime - iCurrentDateTime) - 1;
	}
	else if(iCurrentDateTime>iEndDateTime)
	{
		iRestMinute  = iSecondToMinute(iCurrentDateTime - iEndDateTime) - 1;
	}
	else
	{
		iRestMinute = 30 - (sCurrentDateTime.getMinutes()%30) - 1;
	}

	iRestMinute = (sCurrentDateTime.getSeconds()=="00")?iRestMinute+1:iRestMinute;

	return sFillZero(iRestMinute);
}

function iSecondToMinute(iTimeStamp)
{
	return Math.round(iTimeStamp / 60 / 1000);
}


function iGetCountDownSecond()
{
	var sCurrentDateTime = new Date();
	return sFillZero(60 - sCurrentDateTime.getSeconds());
}

function sGetCommingIssueDate()
{
	var sHourMinute = sCurrentDateTime.getHours() + ":"+ sCurrentDateTime.getMinutes();
	if(sHourMinute>"21:30")
	{
		return  sCurrentDateTime.getFullYear() + "-" +(sCurrentDateTime.getMonth()+1)  + "-" + sCurrentDateTime.getDate()
	}
	else
	{
		return  sCurrentDateTime.getFullYear() + "-" +(sCurrentDateTime.getMonth()+2)  + "-" + sCurrentDateTime.getDate()
	}
}

function sGetCommingIssueTime()
{
	var sHourMinute = sCurrentDateTime.getHours() + ":"+ sCurrentDateTime.getMinutes();
	if(sHourMinute<"10:30" || sHourMinute>"21:30")
	{
		return "10:30";
	}
	else
	{
		if(sCurrentDateTime.getMinutes()>=30)
		{
			return sFillZero(sCurrentDateTime.getHours()+1) + ":00";
		}
		else
		{
			return sFillZero(sCurrentDateTime.getHours()) + ":30";
		}
	}
}

function sFillZero(iNum)
{
	return (parseInt(iNum)<10)?"0"+iNum:iNum;
}

function vPostBet()
{
	vAjax();
}

function vAjax()
{
	$.ajax({
		type: "POST",
		url: sPostUrl,
		data: {
			'gametype' : sGameTypeName,
			'multiple' : $('#multiple').val(),
			'return' : $('#bonus :selected').val(),
			'codes' : aBetCodes,
			'_token' : $('input[name=_token]').val()
		},
		success: function(data){
			if(data.status==true)
			{
				vSetBetAreaRefresh();
			}

			alert(data.message);

		}
	});
}

function vSetBonus()
{
	var iBasicBouns = iGetBonus((iBasicRate + iKeepPoint));
	var iReturnBouns = iGetBonus(iBasicRate);
	$("#bonus option").each(function()
	{
	    if($(this).val()==1)
	    {
	    	$(this).text(sGetBonusOptionText(iReturnBouns,iKeepPoint));
	    }
	    if($(this).val()==0)
	    {
	    	$(this).text(sGetBonusOptionText(iBasicBouns,0));
	    }
	});
}

function sGetBonusOptionText(iBonus,iPoint)
{
	return iBonus+' ( -'+iPoint+'% )'
}

function iGetBonus(iRate)
{
	return (iRate / 100) * 2 * iOdds;
}


function vSetBetOdds()
{
	iOdds = $("input[type='radio']:checked").attr('odds');
}

function iGetFactorial(iNum)
{
	if(iNum==1)
	{
		return 1;
	}

	var iFactorial = null;
	while(iNum > 1)
	{
		if(iFactorial == null )
		{
			iFactorial = iNum;
		}
		else
		{
			iFactorial *= iNum;
		}
		iNum--;
	}
	return iFactorial;
}

function vBetAmountCaculate()
{
	var iBetAmount;

	if(sGameTypeName=='sanmajrshiuan')
	{
		iBetAmount = iSanMaJrShiuanBetAmountCaculate();
	}

	if(sGameTypeName=='sanmatzushiuan')
	{
		iBetAmount = iSanMaTzuShiuanBetAmountCaculate();
	}

	if(sGameTypeName=='yimabudingdan')
	{
		iBetAmount = iYiMaBuDingDanBetAmountCaculate();
	}
	console.log('iBetAmount:'+iBetAmount);
	$('#betamount').html(iBetAmount);
	$('#sumamount').html(iBetAmount * 2 * $('#multiple').val());
}

function iYiMaBuDingDanBetAmountCaculate()
{
	if(aBetCodes[0].length==0)
	{
		return 0;
	}

	return aBetCodes[0].length;
}

function iSanMaTzuShiuanBetAmountCaculate()
{
	var iSelected = aBetCodes[0].length;
	if(iSelected < 3)
	{
		return 0;
	}

	if(iSelected == 3)
	{
		return 1;
	}

	return iFormulaC(iSelected,3);
}

function iFormulaC(iTotal,iChoose)
{
	if(iTotal==iChoose)
	{
		return 1;
	}

	var iM = iTotal;
	var iN1 = iChoose;
	var iN2 = iM - iN1;
	return iGetFactorial(iM) / (iGetFactorial(iN1) * iGetFactorial(iN2));
}

function iSanMaJrShiuanBetAmountCaculate()
{
	var iBetAmount = 0;
	console.log(aBetCodes);

	for (var i = 0; i < aBetCodes.length; i++)
	{
		if(aBetCodes[i].length<1)
		{
			return 0;
		}

		if(iBetAmount == 0 )
		{
			iBetAmount = aBetCodes[i].length;
		}
		else
		{
			iBetAmount *= aBetCodes[i].length;
		}
	}
	return iBetAmount;
}

function vSetBetCodeOnClicked(oElement)
{
	var iBetCodesIndex = iGetBetCodesIndex($(oElement).closest('.field').attr('id'));
	var iCodeIndex = aBetCodes[iBetCodesIndex].indexOf($(oElement).html());
	vHandleClickedCode(iCodeIndex,oElement,iBetCodesIndex);
	vBetAmountCaculate();
}

function vHandleClickedCode(iCodeIndex,oElement,iBetCodesIndex)
{
	if(iCodeIndex==-1)
	{
		aBetCodes[iBetCodesIndex].push($(oElement).html());
		$(oElement).css('background-color','gray');
	}
	else
	{
		if(aBetCodes[iBetCodesIndex].length==1)
		{
			aBetCodes[iBetCodesIndex] = [];
		}
		else
		{
			delete aBetCodes[iBetCodesIndex][iCodeIndex];
			aBetCodes[iBetCodesIndex] = aBetCodes[iBetCodesIndex].filter(function (el) {
				return el !== undefined;
			});
		}

		$(oElement).css('background-color','white');
	}
}

function iGetBetCodesIndex(sDitit)
{
	var index;
	if(jQuery.inArray(sDitit, ['hundreds','dan','tzushiuan']) !== -1)
	{
		iIndex = 0;
	}

	if(jQuery.inArray(sDitit, ['tens','tuo']) !== -1)
	{
		iIndex = 1;
	}

	if(jQuery.inArray(sDitit, ['ones']) !== -1)
	{
		iIndex = 2;
	}
	return iIndex;
}

function vGameTypeOnChange(oElement)
{
	$('div[name=gametypecodearea]').children().hide();
	vSetBetAreaRefresh();
	sGameTypeName = $(oElement).attr('id');
	vSetGameTypeCodeShow(sGameTypeName);
}

function vSetBetAreaRefresh()
{
	vSetBetCodesUnselected();
	$('#betamount').html(0);
	$('#sumamount').html(0);
	$('#multiple').val(1);
}



function vSetBetCodesUnselected()
{
	$('.betcode').css('background-color','white');
	vSetBetCodesInit();
}

function vSetBetCodesInit()
{
	aBetCodes[0] = [];
	aBetCodes[1] = [];
	aBetCodes[2] = [];
}

function vSetGameTypeCodeShow(sGameTypeName)
{
	$('div[name='+sGameTypeName+']').show();
}
</script>
@endsection
<!-- https://www-jqbkj.com/12:25 12:35-->