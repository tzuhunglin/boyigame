var app = require('express');
var http = require('http').Server(app);
var io = require('socket.io')(http);
io.origins('*:*');
var Redis = require('ioredis');
var redis = new Redis();
var aAllGameList = [];
var iTimeLimit = 10000;
var mysql = require('mysql');
var conn = mysql.createConnection({
host : '127.0.0.1',
user : 'root',
password : 'root',
database : 'boyigame'
});
conn.connect(function(err){
if(err) throw err;
console.log('connect success!');
});

// const oUserData;
// console.log(oGetUserData(1));



redis.subscribe('issueInfoJisupailie3', function(err, count) {
  console.log('connect!');
});

redis.on('message', function(channel, notification) {
  if(channel=='issueInfoJisupailie3')
  {
    console.log(notification);
    notification = JSON.parse(notification);

    io.emit('issueInfoJisupailie3', notification.data.oIssueInfoPushData);
  }

});


const redisCache = require('redis');
const client = redisCache.createClient(); // this creates a new client

client.on('connect', () => {
  console.log('Redis client connected');
});


redis.subscribe('gameDataBlackjack', function(err, count) {
  console.log('blackjack connect!');
});

io.on('connection', function(socket) {
  socket.on('set-token', function(token) {
    console.log(token);
    socket.join('token:' + token);

    client.get(token, (error, result) => {
      if (error)
      {
        console.log(error);
        throw error;
      }
      var oGameData = JSON.parse(result);

      if(oGameData!=null)
      {
        io.to('token:' + token).emit(
          'gameDataBlackjack',
          oGameData
        );
      }
    });
  });

  socket.on('gameDataBlackjack', function(oData) {
    vStatusController(oData);
  });
});


setInterval(
function ()
{
  console.log(aAllGameList);
  for (var i = 0; i < aAllGameList.length; i++) {
    var sHashKey = aAllGameList[i];
    client.get(sHashKey, (error, result) => {
      if (error)
      {
        console.log(error);
        throw error;
      }
      var oGameData = JSON.parse(result);
      console.log(oGameData)

      oGameData = oMonitorController(oGameData);
      if(oGameData==null)
      {
        return;
      }

      var sGameData = JSON.stringify(oGameData);
      client.set(sHashKey, sGameData, redis.print);

      if(oGameData!=null)
      {
        io.to('token:' + sHashKey).emit(
          'gameDataBlackjack',
          oGameData
        );
      }
    });
  }
}
,1000);

function oMonitorController(oGameData)
{
  console.log(oGameData);
  switch(oGameData.iStatus)
  {
    case 1:
      oBettingMonitor(oGameData);
      break;
    case 2:
    case 3:
      oPlayingMonitor(oGameData);
      break;
  }

}

function oPlayingMonitor(oGameData)
{
  if(oGameData.iPlayUpdateTime+iTimeLimit > iGetCurrentTimeStamp())
  {
    return ;
  }

  if(oGameData.iStatus == 2 && oGameData.iInsuranceStartTime!=undefined && oGameData.iInsuranceStartTime+iTimeLimit < iGetCurrentTimeStamp())
  {
    console.log("insurance");
    for (var i = 0; i < oGameData.aUserInfoList.length; i++)
    {
      if(oGameData.aUserInfoList[i].iInsurance==1)
      {
        var oData = {"sHashKey":oGameData.sHashKey, "iInsurance":2,"iUserId":oGameData.aUserIds[i],"iStatus":99};
        vStatusController(oData);
      }
    }
  }
  else
  {
    console.log("paly");

    var iUserPos = oGameData.aUserIds.indexOf(oGameData.iTurn);
    var bGetCard = bGetUserCardCheck(oGameData.aUserInfoList[iUserPos].aPoints);

    var oData = {"sHashKey":oGameData.sHashKey, "iStatus":3,"iUserId":oGameData.iTurn,"iValue":bGetCard}
    vSetStatusPlaying(oData);
  }
}

function bGetUserCardCheck(aPoints)
{
  if(aPoints[0][1]==undefined)
  {
    if(aPoints[0][0]>17)
    {
      return 0;
    }
    else
    {
      return 1;
    }
  }
  else
  {
    if(aPoints[0][0]>17 || aPoints[0][1]>17)
    {
      return 0;
    }
    else
    {
      return 1;
    }
  }


}

function oBettingMonitor(oGameData)
{
  if(oGameData.iBetStartTime+iTimeLimit > iGetCurrentTimeStamp())
  {
    return ;
  }
  for (var i = 0; i < oGameData.aUserInfoList.length; i++)
  {
    if(oGameData.aUserInfoList[i].iBetAmount == 0)
    {
      var oData = {"sHashKey":oGameData.sHashKey, "iBetAmount":2,"iUserId":oGameData.aUserIds[i],"iStatus":2};
      vStatusController(oData);
    }
  }
}


function vStatusController(oData)
{
  switch(oData.iStatus)
  {
    case 1:
      vSetStatusBetting(oData);
      break;
    case 2:
      vSetStatusDealing(oData);
      break;
    case 3:
      vSetStatusPlaying(oData);
      break;
    case 99:
      vSetStatusInsurance(oData);
      break;
  }
}

function bInsuranceCompleteCheck(aUserInfoList)
{
  var bInsuranceComplete = true;
  for (var i = 0; i < aUserInfoList.length; i++)
  {
    if(aUserInfoList[i].iInsurance==1 || aUserInfoList[i].iInsurance==0)
    {
      bInsuranceComplete = false;
      break;
    }
  }
  return bInsuranceComplete;
}

function vSetStatusInsurance(oData)
{
  client.get(oData.sHashKey, (error, result) => {
    if (error)
    {
      console.log(error);
      throw error;
    }
    var oGameData = JSON.parse(result);
    var iUserPos = oGameData.aUserIds.indexOf(oData.iUserId);

    oGameData.aUserInfoList[iUserPos].iInsurance = oData.iInsurance;
    var bInsuranceComplete = bInsuranceCompleteCheck(oGameData.aUserInfoList);

    if(bInsuranceComplete==true)
    {
      oGameData.iStatus = 3;
      oGameData.iPlayUpdateTime = iGetCurrentTimeStamp();
    }

    var sGameData = JSON.stringify(oGameData);
    client.set(oData.sHashKey, sGameData, redis.print);

    if(oGameData!=null)
    {
      io.to('token:' + oData.sHashKey).emit(
        'gameDataBlackjack',
        oGameData
      );
    }
  });
}

function iGetCurrentTimeStamp()
{
  var oDate = new Date();
  return oDate.getTime();
}

function oGetDoubleBetGameData(oGameData)
{
  oGameData = oGetNewCardGameData(oGameData);
  var iUserPos = oGameData.aUserIds.indexOf(oGameData.iTurn);
  oGameData.aUserInfoList[iUserPos].iDouble = 3;
  oGameData = oGetNextTurnGameData(oGameData);
  return oGameData;
}

function vSetStatusPlaying(oData)
{
  console.log(oData);
    client.get(oData.sHashKey, (error, result) => {
      if (error)
      {
        console.log(error);
        throw error;
      }
      var oGameData = JSON.parse(result);
      if(oGameData.iTurn != oData.iUserId)
      {
        return ;
      }

      if(oData.iValue==0)
      {
        oGameData = oGetNextTurnGameData(oGameData);
      }
      else if(oData.iValue==1)
      {
        oGameData = oGetNewCardGameData(oGameData);
      }
      else if(oData.iValue==2)
      {
        oGameData = oGetDoubleBetGameData(oGameData);
      }

      if(bCheckIfUserGameOver(oGameData)==true)
      {

        oGameData = oGetNextTurnGameData(oGameData);
      }

      oGameData.iPlayUpdateTime = iGetCurrentTimeStamp();
      if(oGameData.iStatus==4)
      {
        oGameData.iTurn = 0;
        for (var i = 0; i < oGameData.aUserIds.length; i++)
        {
          client.del("blackjack_"+oGameData.aUserIds[i], (error, result) => {
            if (error)
            {
              console.log(error);
              throw error;
            }
          });
        }
      }

      var sGameData = JSON.stringify(oGameData);
      client.set(oData.sHashKey, sGameData, redis.print);

      if(oGameData!=null)
      {
        io.to('token:' + oData.sHashKey).emit(
          'gameDataBlackjack',
          oGameData
        );
      }
  });

}

function oGetNewCardGameData(oGameData)
{
    var iUserPos = oGameData.aUserIds.indexOf(oGameData.iTurn);
    var iNumber = iGetUniqueNumber(oGameData.aAllCards);
    oGameData.aUserInfoList[iUserPos].aCards[0].push(iNumber);
    oGameData.aAllCards.push(iNumber);
    oGameData = oGetPointCaculatedGameData(oGameData);
    oGameData.aUserInfoList[iUserPos].iDouble = 2;
    return oGameData;
}

function bCheckIfUserGameOver(oGameData)
{
  var iUserPos = oGameData.aUserIds.indexOf(oGameData.iTurn);
  if(oGameData.iTurn == -1 || oGameData.aUserInfoList[iUserPos] == undefined)
  {
    return true;
  }
  var bGameOver = true;
  var aPoints = oGameData.aUserInfoList[iUserPos].aPoints[0];
  var aCards = oGameData.aUserInfoList[iUserPos].aCards[0];


  if(aCards.length==5)
  {
    return true;
  }

  for (var i = 0; i < aPoints.length; i++) 
  {
    if(parseInt(aPoints[i])==21)
    {
      return true;
    }
    if(parseInt(aPoints[i])<21)
    {
      bGameOver = false;
    }
  }

  return bGameOver;
}

function oGetNextTurnGameData(oGameData)
{
  var iTurnPosition = oGameData.aUserIds.indexOf(oGameData.iTurn);
  userloop:
  for (var i = iTurnPosition+1; i < oGameData.aUserInfoList.length; i++)
  {
    pointloop:
    for (var j = 0; j < oGameData.aUserInfoList[i].aPoints[0].length; j++)
    {
      if(oGameData.aUserInfoList[i].aPoints[0][j]<21)
      {
        break userloop;
      }
    }
  }

  if(i==oGameData.aUserInfoList.length)
  {
    oGameData.iTurn = oGetFinishedGameData(oGameData);
  }
  else
  {
    oGameData.iTurn = oGameData.aUserIds[i];
  }

  return oGameData;
}

function vSetStatusDealing(oData)
{
  client.get(oData.sHashKey, (error, result) => {
    if (error)
    {
      console.log(error);
      throw error;
    }
    var oGameData = JSON.parse(result);
    var iUserPos = oGameData.aUserIds.indexOf(oData.iUserId);
    oGameData.aUserInfoList[iUserPos].iBetAmount = oData.iBetAmount;

    var bBetComplete = bBetCompleteCheck(oGameData);
    var bDealComplete = bDealCompleteCheck(oGameData.aUserInfoList);

    if(bBetComplete==true && bDealComplete==false)
    {
      oGameData = oGetCardDeltGameData(oGameData);
    }

    if(oGameData.iTurn==undefined)
    {
      userloop:
      for (var i = 0; i < oGameData.aUserInfoList.length; i++)
      {
        pointloop:
        if(oGameData.aUserInfoList[i].aPoints!=null)
        {
          for (var j = 0; j < oGameData.aUserInfoList[i].aPoints[0].length; j++) {
            if(oGameData.aUserInfoList[i].aPoints[0][j]==21)
            {
              continue userloop;
            }
            if(oGameData.aUserInfoList[i].aPoints[0][j]!=21)
            {
              break userloop;
            }
          }
        }
        else
        {
          break;
        }
      }
      console.log(i);
      if(i==oGameData.aUserInfoList.length)
      {
        console.log("oGameData.aUserInfoList.length:"+oGameData.aUserInfoList.length);
        oGameData = oGetFinishedGameData(oGameData);
      }
      else
      {
        oGameData.iTurn = oGameData.aUserIds[i];
      }
    }

    var sGameData = JSON.stringify(oGameData);
    client.set(oData.sHashKey, sGameData, redis.print);

    if(oGameData!=null)
    {
      io.to('token:' + oData.sHashKey).emit(
        'gameDataBlackjack',
        oGameData
      );
    }
  });
}


function oGetFinishedGameData(oGameData)
{
  var iWinnerPoint = iGetWinnerPoint(oGameData.aUserInfoList)
  var bCompleteCheck = false;
  while(true)
  {
    bCompleteCheck = bBankerCardsCompleteCheck(oGameData.aBankerInfo.aCards,oGameData.aBankerInfo.aPoints,iWinnerPoint);
    if(bCompleteCheck==true)
    {
      break;
    }
    oGameData = oGetBankerCardsGameData(oGameData);
  }

  oGameData = oGetWinLoseSettleGameData(oGameData);
  var iHashKeyIndex = aAllGameList.indexOf(oGameData.sHashKey);
  delete aAllGameList[iHashKeyIndex];
  aAllGameList = aGetFiltered(aAllGameList);

  return oGameData;
}

function oGetWinLoseSettleGameData(oGameData)
{
  var iBankerPriorPoint = iGetPriorPoint(oGameData.aBankerInfo.aPoints);
  for (var i = 0; i < oGameData.aUserInfoList.length; i++)
  {
    var iUserPriorPoint = iGetPriorPoint(oGameData.aUserInfoList[i].aPoints[0]);
    oGameData.aUserInfoList[i].iWinLose = iBankerUserCompare(iBankerPriorPoint,iUserPriorPoint);
  }

  oGameData.iStatus = 4;
  oGameData.iTurn = null;
  return oGameData;
}

function iBankerUserCompare(iBankerPoint,iUserPoint)
{
  var iWinLose;
  if(iBankerPoint>21)
  {
    if(iUserPoint>21)
    {
      iWinLose = 1;
    }
    else
    {
      iWinLose = 2;
    }
  }
  else
  {
    if(iUserPoint>21)
    {
      iWinLose = 0;
    }
    else
    {
      if(iBankerPoint > iUserPoint)
      {
        iWinLose = 0;
      }
      else if(iBankerPoint < iUserPoint)
      {
        iWinLose = 2;
      }
      else
      {
        iWinLose = 1;
      }
    }
  }

  return iWinLose;
}

function iGetPriorPoint(aPoints)
{
  if(aPoints[1]==undefined)
  {
    return aPoints[0];
  }
  else
  {
    if(aPoints[1]<22)
    {
      return aPoints[1];
    }
    else
    {
      return aPoints[0];
    }
  }
}

function bBankerCardsCompleteCheck(aCards, aPoints, iWinnerPoint)
{
  if(aCards.length==5)
  {
    return true
  }

  if(aPoints[1] == undefined)
  {
    if(aPoints[0]==21)
    {
      return true;
    }

    if(aPoints[0]>iWinnerPoint && aPoints[0]>17)
    {
      return true;
    }
    else
    {
      return false;
    }

  }
  else
  {
    for (var i = 0; i < aPoints.length; i++)
    {
      if(aPoints[i]==21)
      {
        return true;
      }

      if(aPoints[i]>17 && aPoints[i] > iWinnerPoint && aPoints[i]<22)
      {
        return true;
      }
    }

    if(aPoints[0]>21 && aPoints[1] >21)
    {
      return true;
    }

    return false;
  }
}

function oGetBankerCardsGameData(oGameData)
{
  if(oGameData.aBankerInfo.aCards.length==5)
  {
    return oGameData;
  }

  var iNumber = iGetUniqueNumber(oGameData.aAllCards);
  oGameData.aBankerInfo.aCards.push(iNumber);
  oGameData.aBankerInfo.aPoints = aGetCardPoints(oGameData.aBankerInfo.aCards);
  oGameData.aAllCards.push(iNumber);
  return oGameData
}

function iGetWinnerPoint(aUserInfoList)
{
  var aUserPoints = [];
  for (var i = 0; i < aUserInfoList.length; i++)
  {
    for (var j = 0; j < 2; j++)
    {
      if(aUserInfoList[i].aPoints[0][j]!=undefined)
      {
        aUserPoints.push(aUserInfoList[i].aPoints[0][j]);
      }
    }
  }

  aUserPoints.sort();
  var iWinnerPoint = 0;
  for (var i = aUserPoints.length - 1; i >= 0; i--)
  {
    if(aUserPoints[i]<22)
    {
      iWinnerPoint = aUserPoints[i];
      break;
    }
  }
  return iWinnerPoint;
}

function bBetCompleteCheck(oGameData)
{
  var bBetComplete = true;
  for (var i = 0; i < oGameData.aUserInfoList.length; i++)
  {
    if(oGameData.aUserInfoList[i].iBetAmount==0)
    {
      bBetComplete = false;
    }
  }

  return bBetComplete;
}

function bDealCompleteCheck(aUserInfoList)
{
  var bDealComplete = true;
  for (var i = 0; i < aUserInfoList.length; i++)
  {
    if(aUserInfoList[i].aPoints==undefined||aUserInfoList[i].aPoints[0]==null)
    {
      bDealComplete= false;
    }
  }
  return bDealComplete;
}

function oGetPointCaculatedGameData(oGameData)
{
  for (var i = 0; i < oGameData.aUserInfoList.length; i++)
  {
    if(oGameData.aUserInfoList[i].aPoints==undefined)
    {
      oGameData.aUserInfoList[i].aPoints = [];
      oGameData.aUserInfoList[i].aPoints[0] = [];
      oGameData.aUserInfoList[i].aPoints[1] = [];
    }
    oGameData.aUserInfoList[i].aPoints[0] = aGetCardPoints(oGameData.aUserInfoList[i].aCards[0]);
  }
  oGameData.aBankerInfo.aPoints = aGetCardPoints(oGameData.aBankerInfo.aCards);

  return oGameData;
}

function aGetCardPoints(aCards)
{
  var bAceExist = false;
  var iTotalPoints = 0;
  var aCardPoints = [];
  for (var i = 0; i < aCards.length; i++)
  {
    var iCardNumber = aCards[i]%13;
    if(iCardNumber==1)
    {
      bAceExist = true;
    }
    var iPoint = (iCardNumber>10||iCardNumber==0)?10:iCardNumber;
    iTotalPoints+= iPoint;
  }

  if(bAceExist!=false)
  {
    aCardPoints = [iTotalPoints,iTotalPoints+10];
  }
  else
  {
    aCardPoints = [iTotalPoints];
  }

  return aCardPoints;
}



function oGetCardDeltGameData(oGameData)
{
  oGameData.aAllCards = [];
  for (var i = 0; i < oGameData.aUserInfoList.length; i++)
  {
    if(oGameData.aUserInfoList[i].aCards == undefined )
    {
      oGameData.aUserInfoList[i].aCards = [];
      oGameData.aUserInfoList[i].aCards[0] = [];
      for (var j = 0; j < 2; j++)
      {
        var iNumber = iGetUniqueNumber(oGameData.aAllCards);
        oGameData.aUserInfoList[i].aCards[0].push(iNumber);
        oGameData.aAllCards.push(iNumber);
      }
      var bDoubleBetChanceCheck = bGetDoubleBetChanceCheck(oGameData.aUserInfoList[i].aCards[0]);
      oGameData.aUserInfoList[i].iDouble = (bDoubleBetChanceCheck)?1:0;
    }
  }

  oGameData.aBankerInfo = {};
  oGameData.aBankerInfo.aCards = [];
  for (var k = 0; k < 1; k++)
  {
    var iNumber = iGetUniqueNumber(oGameData.aAllCards);
    oGameData.aBankerInfo.aCards.push(iNumber);
    oGameData.aAllCards.push(iNumber);
  }

  oGameData = oGetPointCaculatedGameData(oGameData);
  var aAceCodes = [1,14,27,40];
  var bBankerAce = (aAceCodes.indexOf(parseInt(oGameData.aBankerInfo.aCards[0]))!=-1);

  oGameData.iStatus = 2;
  oGameData.iPlayUpdateTime = iGetCurrentTimeStamp();

  for (var i = 0; i < oGameData.aUserInfoList.length; i++)
  {
    oGameData.aUserInfoList[i].iInsurance = (bBankerAce == true)?1:0;
  }

  if(bBankerAce == true)
  {
    oGameData.iInsuranceStartTime = iGetCurrentTimeStamp();
  }

  return oGameData;
}

function bGetDoubleBetChanceCheck(aCards)
{
  aCardPoints = aGetCardPoints(aCards);
  return (aCardPoints.indexOf(11)!=-1);

}

function iGetUniqueNumber(aAllCards)
{
  while(true)
  {
    var iNumber = iGetRandom();
    if(aAllCards.indexOf(iNumber)==-1)
    {
      break;
    }
  }
  return iNumber;
}

function iGetRandom(){
    return Math.floor(Math.random()*52)+1;
};

function vSetStatusBetting(oData)
{
  client.get("blackjack_waitinggamelist", (error, result) => {
    if (error)
    {
      console.log(error);
      throw error;
    }
    var aGameList = JSON.parse(result);
    var iIndex = aGameList.indexOf(oData.sHashKey);
    delete aGameList[iIndex];
    aGameList = aGetFiltered(aGameList);
    var sGameList = JSON.stringify(aGameList);
    client.set("blackjack_waitinggamelist", sGameList, redis.print);
    aAllGameList.push(oData.sHashKey);
  });

  client.get(oData.sHashKey, (error, result) => {
    if (error)
    {
      console.log(error);
      throw error;
    }
    var oGameData = JSON.parse(result);
    oGameData.iStatus = oData.iStatus;
    oGameData.iBetStartTime = iGetCurrentTimeStamp();
    var sGameData = JSON.stringify(oGameData);

    client.set(oData.sHashKey, sGameData, redis.print);

    if(oGameData!=null)
    {
      io.to('token:' + oData.sHashKey).emit(
        'gameDataBlackjack',
        oGameData
      );
    }
  });

}

function aGetFiltered(aData)
{
  aData = aData.filter(function (el) {
    return el != null;
  });
  return aData;
}

// 監聽 3000 port
http.listen(3000, function() {
  console.log('Listening on Port 3000');
});
