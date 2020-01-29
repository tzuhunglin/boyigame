<?php
//init curl
$ch = curl_init();
//curl_setopt可以設定curl參數
//設定url
curl_setopt($ch , CURLOPT_URL , "www.yahoo.com.tw");
//設定AGENT
curl_setopt($ch, CURLOPT_USERAGENT, "Google Bot");
//執行，並將結果存回
$result = curl_exec($ch);
echo "<pre>"; print_r(curl_getinfo($ch));
//關閉連線
curl_close($ch);
?>
