var app = require('express');
var http = require('http').Server(app);
var io = require('socket.io')(http);
io.origins('*:*');
var Redis = require('ioredis');
var redis = new Redis();





redis.subscribe('issueInfoJisupailie3', function(err, count) {
  console.log('connect!');
});

redis.on('message', function(channel, notification) {
  if(channel=='issueInfoJisupailie3')
  {
    console.log(notification);
    notification = JSON.parse(notification);

    // 將訊息推播給使用者
    io.emit('issueInfoJisupailie3', notification.data.oIssueInfoPushData);
  }

});



// redis.subscribe('notification', function(err, count) {
//   console.log('connect!');
// });

// redis.on('message', function(channel, notification) {
//   console.log(notification);
//   notification = JSON.parse(notification);

//   // 將訊息推播給使用者
//   io.emit('notification', notification.data.message);
// });

const redisCache = require('redis');
const client = redisCache.createClient(); // this creates a new client

client.on('connect', () => {
  console.log('Redis client connected');
});
// client.set('foo', 'bar', redis.print);
// client.get('foo', (error, result) => {
//   if (error) {
//     console.log(error);
//     throw error;
//   }
//   console.log('GET result ->' + result);
// });




redis.subscribe('gameDataBlackjack', function(err, count) {
  console.log('blackjack connect!');
});

io.on('connection', function(socket) {
  // 當使用者觸發 set-token 時將他加入屬於他的 room
  socket.on('set-token', function(token) {
    console.log(token);
    socket.join('token:' + token);


      client.get(token, (error, result) => {
        if (error) {
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

    // var oGameData = oGetGameData(token)

  });

  socket.on('gameDataBlackjack', function(oData) {
    // console.log(oData);

    // var oGameData = oGetGameData(token)
    vStatusController(oData);
  });
});

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

function bInsuranceCompleteCheck(aUserList)
{
  var bInsuranceComplete = true;
  for (var i = 0; i < aUserList.length; i++) {
    if(aUserList[i].iInsurance==1 || aUserList[i].iInsurance==0)
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
    if (error) {
      console.log(error);
      throw error;
    }
      var oGameData = JSON.parse(result);
      var iUserPos = oGameData.aUserIds.indexOf(oData.iUserId);

      oGameData.aUserList[iUserPos].iInsurance = oData.iInsurance;
      var bInsuranceComplete = bInsuranceCompleteCheck(oGameData.aUserList);

      if(bInsuranceComplete==true)
      {
        oGameData.iStatus = 3;
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

function oGetDoubleBetGameData(oGameData)
{
  oGameData = oGetNewCardGameData(oGameData);
  var iUserPos = oGameData.aUserIds.indexOf(oGameData.iTurn);
  oGameData.aUserList[iUserPos].iDouble = 3;
  oGameData = oGetNextTurnGameData(oGameData);
  return oGameData;
}

function vSetStatusPlaying(oData)
{
    client.get(oData.sHashKey, (error, result) => {
      if (error) {
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

      if(oGameData.iStatus==4)
      {
        oGameData.iTurn = 0;
        for (var i = 0; i < oGameData.aUserIds.length; i++) {
          client.del("blackjack_"+oGameData.aUserIds[i], (error, result) => {
            if (error) {
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
    oGameData.aUserList[iUserPos].aCards[0].push(iNumber);
    oGameData.aAllCards.push(iNumber);
    oGameData = oGetPointCaculatedGameData(oGameData);
    oGameData.aUserList[iUserPos].iDouble = 2;
    return oGameData;
}

function bCheckIfUserGameOver(oGameData)
{
  var iUserPos = oGameData.aUserIds.indexOf(oGameData.iTurn);
  if(oGameData.iTurn == -1 || oGameData.aUserList[iUserPos] == undefined)
  {
    return true;
  }
  var bGameOver = true;
  var aPoints = oGameData.aUserList[iUserPos].aPoints[0];
  var aCards = oGameData.aUserList[iUserPos].aCards[0];


  if(aCards.length==5)
  {
    return true;
  }

  for (var i = 0; i < aPoints.length; i++) {
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
        for (var i = iTurnPosition+1; i < oGameData.aUserList.length; i++) 
        {
      pointloop:
          for (var j = 0; j < oGameData.aUserList[i].aPoints[0].length; j++) {
            if(oGameData.aUserList[i].aPoints[0][j]<21)
            {
              break userloop;
            }
          }
        }

        if(i==oGameData.aUserList.length)
        {
          oGameData.iTurn = oGetFinishedGameData(oGameData);
        }
        else
        {
          oGameData.iTurn = oGameData.aUserIds[i];
        }

  return oGameData;
}


function oGetFinishedGameData(oGameData)
{
  var iWinnerPoint = iGetWinnerPoint(oGameData)
  var bCompleteCheck = false;
  while(true)
  {
    bCompleteCheck = bBankerCardsCompleteCheck(oGameData,iWinnerPoint);
    if(bCompleteCheck==true)
    {
      break;
    }

    oGameData = oGetBankerCardsGameData(oGameData);
  }

  oGameData = oGetWinLoseSettleGameData(oGameData);


  return oGameData;
}

function oGetWinLoseSettleGameData(oGameData)
{
  var iBankerPriorPoint = iGetPriorPoint(oGameData.aBankerPoints);
  for (var i = 0; i < oGameData.aUserList.length; i++)
  {
    var iUserPriorPoint = iGetPriorPoint(oGameData.aUserList[i].aPoints[0]);
    oGameData.aUserList[i].iWinLose = iBankerUserCompare(iBankerPriorPoint,iUserPriorPoint);
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

function bBankerCardsCompleteCheck(oGameData, iWinnerPoint)
{
  if(oGameData.aBankerCards.length==5)
  {
    return true
  }

  if(oGameData.aBankerPoints[1] == undefined)
  {
    if(oGameData.aBankerPoints[0]==21)
    {
      return true;
    }

    if(oGameData.aBankerPoints[0]>iWinnerPoint && oGameData.aBankerPoints[0]>17)
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
    for (var i = 0; i < oGameData.aBankerPoints.length; i++) 
    {
      if(oGameData.aBankerPoints[i]==21)
      {
        return true;
      }

      if(oGameData.aBankerPoints[i]>17 && oGameData.aBankerPoints[i] > iWinnerPoint && oGameData.aBankerPoints[i]<22)
      {
        return true;
      }
    }

    if(oGameData.aBankerPoints[0]>21 && oGameData.aBankerPoints[1] >21)
    {
      return true;
    }

    return false;
  }
}

function oGetBankerCardsGameData(oGameData)
{
  if(oGameData.aBankerCards.length==5)
  {
    return oGameData;
  }

        var iNumber = iGetUniqueNumber(oGameData.aAllCards);
        oGameData.aBankerCards.push(iNumber);
        oGameData.aBankerPoints = aGetCardPoints(oGameData.aBankerCards);
        oGameData.aAllCards.push(iNumber);
        return oGameData
}

function iGetWinnerPoint(oGameData)
{
  var aUserPoints = [];
  for (var i = 0; i < oGameData.aUserIds.length; i++)
  {
    for (var j = 0; j < 2; j++) {
      if(oGameData.aUserList[i].aPoints[0][j]!=undefined)
      {
        aUserPoints.push(oGameData.aUserList[i].aPoints[0][j]);
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



function vSetStatusDealing(oData)
{
  client.get(oData.sHashKey, (error, result) => {
    if (error) {
      console.log(error);
      throw error;
    }
      var oGameData = JSON.parse(result);
      var iUserPos = oGameData.aUserIds.indexOf(oData.iUserId);
      oGameData.aUserList[iUserPos].iBetAmount = oData.iBetAmount;

      var bBetComplete = bBetCompleteCheck(oGameData);
      var bDealComplete = bDealCompleteCheck(oGameData.aUserList);

      if(bBetComplete==true && bDealComplete==false)
      {
        oGameData = oGetCardDeltGameData(oGameData);
      }

      if(oGameData.iTurn==undefined)
      {
        userloop:
        for (var i = 0; i < oGameData.aUserList.length; i++)
        {
          pointloop:
          if(oGameData.aUserList[i].aPoints!=null)
          {
            for (var j = 0; j < oGameData.aUserList[i].aPoints[0].length; j++) {
              if(oGameData.aUserList[i].aPoints[0][j]==21)
              {
                continue userloop;
              }
              if(oGameData.aUserList[i].aPoints[0][j]!=21)
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
        if(i==oGameData.aUserList.length)
        {
          console.log("oGameData.aUserList.length:"+oGameData.aUserList.length);
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


function bBetCompleteCheck(oGameData)
{
  var bBetComplete = true;
  for (var i = 0; i < oGameData.aUserList.length; i++) {
    if(oGameData.aUserList[i].iBetAmount==0)
    {
      bBetComplete = false;
    }
  }

  return bBetComplete;
}

function bDealCompleteCheck(aUserList)
{
  var bDealComplete = true;
  for (var i = 0; i < aUserList.length; i++) {
    if(aUserList[i].aPoints==undefined||aUserList[i].aPoints[0]==null)
    {
      bDealComplete= false;
    }
  }
  return bDealComplete;
}

function oGetPointCaculatedGameData(oGameData)
{
  for (var i = 0; i < oGameData.aUserList.length; i++) {
    if(oGameData.aUserList[i].aPoints==undefined)
    {
      oGameData.aUserList[i].aPoints = [];
      oGameData.aUserList[i].aPoints[0] = [];
      oGameData.aUserList[i].aPoints[1] = [];
    }
    oGameData.aUserList[i].aPoints[0] = aGetCardPoints(oGameData.aUserList[i].aCards[0]);

  }
  oGameData.aBankerPoints = aGetCardPoints(oGameData.aBankerCards);

  return oGameData;
}

function aGetCardPoints(aCards)
{
  var bAceExist = false;
  var iTotalPoints = 0;
  var aCardPoints = [];
  for (var i = 0; i < aCards.length; i++) {
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
  for (var i = 0; i < oGameData.aUserList.length; i++)
  {
    if(oGameData.aUserList[i].aCards == undefined )
    {
      oGameData.aUserList[i].aCards = [];
      oGameData.aUserList[i].aCards[0] = [];
      for (var j = 0; j < 2; j++)
      {
        var iNumber = iGetUniqueNumber(oGameData.aAllCards);
        oGameData.aUserList[i].aCards[0].push(iNumber);
        oGameData.aAllCards.push(iNumber);
      }
      var bDoubleBetChanceCheck = bGetDoubleBetChanceCheck(oGameData.aUserList[i].aCards[0]);
        oGameData.aUserList[i].iDouble = (bDoubleBetChanceCheck)?1:0;
    }
  }
  if(oGameData.aBankerCards == undefined )
  {
    for (var k = 0; k < 1; k++)
    {
      oGameData.aBankerCards = [];
      var iNumber = iGetUniqueNumber(oGameData.aAllCards);
      oGameData.aBankerCards.push(iNumber);
      oGameData.aAllCards.push(iNumber);
    }
  }

  oGameData = oGetPointCaculatedGameData(oGameData);
  var aAceCodes = [1,14,27,40];
  var bBankerAce = (aAceCodes.indexOf(parseInt(oGameData.aBankerCards[0]))!=-1);

  oGameData.iStatus = 2;


  for (var i = 0; i < oGameData.aUserList.length; i++)
  {
    oGameData.aUserList[i].iInsurance = (bBankerAce == true)?1:0;
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
    if (error) {
      console.log(error);
      throw error;
    }
      var aGameList = JSON.parse(result);

      var iIndex = aGameList.indexOf(oData.sHashKey);
      delete aGameList[iIndex];
      aGameList = aGameList.filter(function (el) {
        return el != null;
      });
      var sGameList = JSON.stringify(aGameList);
      client.set("blackjack_waitinggamelist", sGameList, redis.print);
  });

  client.get(oData.sHashKey, (error, result) => {
    if (error) {
      console.log(error);
      throw error;
    }
      var oGameData = JSON.parse(result);
      oGameData.iStatus = oData.iStatus;
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


// 監聽 3000 port
http.listen(3000, function() {
  console.log('Listening on Port 3000');
});