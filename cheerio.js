var request = require("request");
var cheerio = require("cheerio");
  request({
    url: "https://www.1395p.com/shssl/?utp=topbar",
    method: "GET"
  }, function(e,r,b) { /* Callback 函式 */
    /* e: 錯誤代碼 */
    /* b: 傳回的資料內容 */
if(!e) console.log(b);
  });
