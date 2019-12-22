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
now = datetime.now()

sFetchCodeUrl = "https://www.1395p.com/shssl/?utp=topbar"

# sExcutablePath = "/home/austin/Desktop/python/geckodriver"
# oDriver = webdriver.Firefox(executable_path=sExcutablePath)


oDriver = webdriver.Firefox()

oDriver.get(sFetchCodeUrl)
# time.sleep(10)
oSoup = BeautifulSoup(oDriver.page_source, "html.parser")
oCodes = oSoup.find("div", {"class": "awarding_tips number_redAndBlue"})
aCodeList = []

for oCode in oCodes:
	aCodeList.append(oCode.text)

sCodeList = json.dumps(aCodeList)
sIssue = oSoup.find("i", {"class": "font_gray666"}).text

# aCodeList = [1,5,5]
# sIssue = "20191214-2"



sCodeList = json.dumps(aCodeList)
sToday = now.strftime("%Y-%m-%d %H:%M")
sDateTime = sToday
sUpdateTime = now.strftime("%Y-%m-%d %H:%M:%S")
sLottery = "jisupailie3"
sSql = "INSERT INTO issueinfo (`datetime`,`issue`,`code`,`lottery`,`updatetime`) VALUES ('"+sDateTime+"','"+sIssue+"','"+sCodeList.replace(" ", "")+"','"+sLottery+"','"+sUpdateTime+"') ;"








hostname = '127.0.0.1'
username = 'root'
password = 'root'
database = "notification"
port = "8889"









# print "Using MySQLdb…"
# import MySQLdb
# myConnection = MySQLdb.connect( host=hostname, user=username, passwd=password, db=database )
# doQuery( myConnection )
# myConnection.close()

# print "Using pymysql…"
# import pymysql
# myConnection = pymysql.connect( host=hostname, user=username, passwd=password, db=database )
# doQuery( myConnection )
# myConnection.close()

# print "Using mysql.connector…"
import mysql.connector
from mysql.connector import Error
from mysql.connector import errorcode
myConnection = mysql.connector.connect( host=hostname, user=username, passwd=password, db=database, port=port)

cur = myConnection.cursor()
cur.execute(sSql)
print(sSql)
myConnection.commit()
print(cur.rowcount, "Record inserted successfully into Laptop table")
cur.close()
myConnection.close()

oDriver.quit()


# font_gray666 issue
# font_gray999 time
# awardResult code


