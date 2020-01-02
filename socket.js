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
    console.log(oData);

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
  }
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

      if(bCheckIfUserGameOver(oGameData)==true && oGameData.bFinish==undefined)
      {
        oGameData = oGetNextTurnGameData(oGameData);
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
    // console.log(oGameData);
    return oGameData;
}

function bCheckIfUserGameOver(oGameData)
{
  var bGameOver = true;
  var iUserPos = oGameData.aUserIds.indexOf(oGameData.iTurn);
  var aPoints = oGameData.aUserList[iUserPos].aPoints[0];

  if(aPoints.length==5)
  {
    return false;
  }

  for (var i = 0; i < aPoints.length; i++) {
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
            if(oGameData.aUserList[i].aPoints[0][j]<22)
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
  console.log("game over");
}

function vSetStatusDealing(oData)
{
  console.log(oData);
  client.get(oData.sHashKey, (error, result) => {
    if (error) {
      console.log(error);
      throw error;
    }
      var oGameData = JSON.parse(result);
      // oGameData.iStatus = oData.iStatus;
      var iUserPos = oGameData.aUserIds.indexOf(oData.iUserId);
      oGameData.aUserList[iUserPos].iBetAmount = oData.iBetAmount;

      var bBetComplete = bBetCompleteCheck(oGameData);
      var bDealComplete = bDealCompleteCheck(oGameData.aUserList);

      if(bBetComplete==true && bDealComplete==false)
      {
        // console.log("VVVVVV");
        oGameData = oGetCardDeltGameData(oGameData);
        oGameData.iStatus = oData.iStatus;
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

        if(i==oGameData.aUserList.length)
        {
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
    console.log(oGameData.aUserList[i].iBetAmount);
    if(oGameData.aUserList[i].iBetAmount==0)
    {
      bBetComplete = false;
    }
  }
    console.log(bBetComplete);

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
  // console.log("fdsfd");
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
  // console.log(aCards);
  var bAceExist = false;
  var iTotalPoints = 0;
  var aCardPoints = [];
  for (var i = 0; i < aCards.length; i++) {
    var iCardNumber = aCards[i]%13;
    bAceExist = (iCardNumber==1)?true:false
    var iPoint = (iCardNumber>10||iCardNumber==0)?10:iCardNumber;
    iTotalPoints+= iPoint;
  }

  if(bAceExist==true)
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
    }
    // console.log("VVVVVVV");

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
  return oGameData;
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
      console.log("iIndex:"+iIndex);
      delete aGameList[iIndex];
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





// function oGetGameData(sHashKey)
// {
//     var oGameData = null;
//     var aKeyList = [
//                     "blackjack_waitinggamelist",
//                     // "blackjack_goinggamelist"
//                     ];
//     for (var i = 0; i < aKeyList.length; i++) {    
//       oGameData =client.get(aKeyList[i], (error, result) => {
//         if (error) {
//           console.log(error);
//           throw error;
//         }
//         // console.log('GET result ->' + typeof result);
//         if(result==null)
//         {
//           return;
//         }

//         var oGameList = JSON.parse(result);

//         if(oGameList[sHashKey]!=undefined)
//         {

//           oGameData = oGameList[sHashKey];
//           return oGameData;


//         }
//       });
//       //     console.log(oGameData);

//       // if(oGameData!=null)
//       // {
//       //   console.log(oGameData);
//       //   break;
//       // }
//     }

//     return oGameData;

// }




// redis.on('gameDataBlackjack', function(notification) {
  // if(channel=='gameDataBlackjack')
  // {
    // console.log(channel);
    // console.log(notification);


    // notification = JSON.parse(notification);
    // console.log(notification.data);

    // // 使用 to() 指定傳送的 room，也就是傳遞給指定的使用者
    // io.to('token:' + notification.data.token).emit(
    //   'gameDataBlackjack',
    //   notification.data.oGameData
    // );
  // }

// });


// 監聽 3000 port
http.listen(3000, function() {
  console.log('Listening on Port 3000');
});