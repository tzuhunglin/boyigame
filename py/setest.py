from selenium import webdriver  
driver = webdriver.Firefox('/var/www/html/boyigame/py/')  
driver.get('http://www.baidu.com')  
print driver.title  
driver.quit()  
