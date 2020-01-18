<?php
date_default_timezone_set("Asia/Taipei");
error_reporting(E_ALL);
ini_set('display_errors', 1);
$bLoop = true;
do
{
	if(date("H:i")>="10:30" && date("H:i")<="21:35")
	{
		if(date("i")=="34"||date("i")=="04")
		{
			echo "<pre>"; print_r(date("H:i"));
	        exec('python /var/www/html/boyigame/py/se.py');

			sleep(60);
		}
		sleep(30);

	}
	else
	{
		sleep(60);
	}

}
while($bLoop);
?>
