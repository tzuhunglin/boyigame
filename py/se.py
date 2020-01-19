# -*- coding: utf-8 -*-

#!/usr/bin/env python
#!/usr/bin/python
from selenium import webdriver
import urllib2
import time
from bs4 import BeautifulSoup
import json
from datetime import date
from datetime import datetime
from pprint import pprint
from dotenv import find_dotenv,load_dotenv
import os
from pyvirtualdisplay import Display
display = Display(visible=0, size=(800, 800))
display.start()
load_dotenv(find_dotenv())
chrome_options = webdriver.ChromeOptions()
chrome_options.add_argument('--headless')
chrome_options.add_argument('--disable-gpu')
chrome_options.add_argument("window-size=1024,768")
chrome_options.add_argument("--no-sandbox")
now = datetime.now()
sFetchCodeUrl = "https://www.1395p.com/shssl/?utp=topbar"
oDriver = webdriver.Chrome(chrome_options=chrome_options)

oDriver.get(sFetchCodeUrl)
oSoup = BeautifulSoup(oDriver.page_source, "html.parser")
oCodes = oSoup.find("div", {"class": "awarding_tips number_redAndBlue"})
aCodeList = []

for oCode in oCodes:
	aCodeList.append(oCode.text)

sCodeList = json.dumps(aCodeList)
sIssue = oSoup.find("i", {"class": "font_gray666"}).text

sCodeList = json.dumps(aCodeList)
sToday = now.strftime("%Y-%m-%d %H:%M")
sDateTime = sToday
sUpdateTime = now.strftime("%Y-%m-%d %H:%M:%S")
sLottery = "jisupailie3"
sSql = "INSERT INTO issueinfo (`datetime`,`issue`,`code`,`lottery`,`updated_at`,`created_at`) VALUES ('"+sDateTime+"','"+sIssue+"','"+sCodeList.replace(" ", "")+"','"+sLottery+"','"+sUpdateTime+"','"+sUpdateTime+"') ;"

host = os.environ.get('DB_HOST')
username = os.environ.get('DB_USERNAME')
password = os.environ.get('DB_PASSWORD')
database = os.environ.get('DB_DATABASE')
port = os.environ.get('DB_PORT')

import mysql.connector
from mysql.connector import Error
from mysql.connector import errorcode
myConnection = mysql.connector.connect( host=host, user=username, passwd=password, db=database, port=port)

cur = myConnection.cursor()
cur.execute(sSql)
print(sSql)
myConnection.commit()
print(cur.rowcount, "Record inserted successfully into issueinfo table")
cur.close()
myConnection.close()

oDriver.quit()

