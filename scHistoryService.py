import os
import sys
import time
from time import sleep
import socket
import threading
import MySQLdb
import gc
import urllib.request
import json



host = ''
port = 4800
packetLength = 2048

mysqlHost = "localhost"
mysqlUser = "editor"
mysqlPass = "5ohnn0"

sock = socket.socket()
sock.bind((host, port))
sock.listen(5)

scURL = 'http://gmt.star-conflict.com/pubapi/v1/userinfo.php?nickname='
bigUserArray = [[]]


def Main(self):
	buffer = "null"
	while True:
		data = self.conn.recv(packetLength)
		if len(data) < 1:
			return
		try:
			inputText = data.decode('UTF-8').replace("<EOF>", "")
		except UnicodeDecodeError:
			AddLog("Ошибка. Пакет не является текстовым", 'error')
			return
		AddLog('Получаем пакет: ' + inputText, 'debug')
		outputText, buffer = Reaction(inputText, buffer)
		
		if outputText != "null":
			bytes = outputText.encode('UTF-8')
			self.conn.send(bytes + b"<EOF>")
#end define

def Reaction(inputText, buffer):
	AddLog("Start Reaction", "debug")
	outputText = 'null'
	if ('<nickname>' in inputText):
		outputText = AddNicknameToMySQL(inputText)
	
	elif ('<NumberOfUsersInMySQL>' in inputText):
		outputText, buffer = NumberOfUsersInMySQL(inputText)
	
	elif ('<user>' in inputText):
		outputText = SendNicknameFromMySQL(inputText, buffer)
		
	return outputText, buffer
#end define

def SendNicknameFromMySQL(inputText, data):
	AddLog("Start SendNicknameFromMySQL", "debug")
	userNumberString = Parsing(inputText, '<user>', '</user>')
	userNumber = int(float(userNumberString))
	AddLog('userNumber=' + userNumberString, "debug")
	outputText = data[userNumber]['nickname']
	return outputText
#end define

def NumberOfUsersInMySQL(inputText):
	AddLog("Start NumberOfUsersInMySQL", "debug")
	result = data = "null"
	try:
		conn = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db="sc_history_db")
	except MySQLdb.Error as err:
		AddLog("Connection error: {}".format(err), 'error')
		conn.close()
	
	sql = "SELECT * FROM nickname_uid"
	
	try:
		cur = conn.cursor(MySQLdb.cursors.DictCursor)
		result = cur.execute(sql)
		result = str(result)
		data = cur.fetchall()
	except MySQLdb.Error as err:
		AddLog("Query error: {}".format(err), 'error')
	cur.close()
	conn.close()
	gc.collect()
	return result, data
#end define

def AddNicknameToMySQL(inputText):
	AddLog("Start AddNicknameToMySQL", "debug")
	nickname = Parsing(inputText, '<nickname>', '</nickname>')
	AddLog("nickname: " + nickname)
	
	# Проверяем правильность никнейма
	result = IsNicknameGood(nickname)
	if result < 0:
		return 'nickname is not good'
	
	# Проверяем наличие данного ника в БД
	BDresult, uidFromDB = IsNicknameInMySQL(nickname)
	AddLog("BDresult: " + str(BDresult) + " uidFromDB: " +  str(uidFromDB), 'debug');
	
	# Проверяем правильность ника через сервер SC
	uv_code, uidFromSC = UserVerificationAcrossScApi(nickname)
	AddLog("uv_code: " +  str(uv_code) + " uidFromSC: " +  str(uidFromSC), 'debug');
	if uv_code < 0:
		return 'user not found'
	
	
	# Проверка является никнейм заменой старого никнейма
	if BDresult > 0:
		if uidFromDB != uidFromSC:
			DeleteRowInTable(nickname)
		else:
			return 'user already exists'
	
	# Добавляем новый ник в БД
	AddEntryIntoTable(uidFromSC, nickname)
	
	return 'ok'
#end define

def DeleteRowInTable(nickname):
	"""Удалить строку из таблицы"""
	AddLog("Start DeleteRowInTable", "debug")
	try:
		conn = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db="sc_history_db")
	except MySQLdb.Error as err:
		AddLog("Connection error: {}".format(err), 'error')
		conn.close()
		
	sql = "DELETE FROM nickname_uid WHERE BINARY nickname='" + nickname + "' LIMIT 1"
	
	try:
		cur = conn.cursor(MySQLdb.cursors.DictCursor)
		result = cur.execute(sql)
		data = cur.fetchall()
		conn.commit()
	except MySQLdb.Error as err:
		AddLog("Query error: {}".format(err), 'error')
#end define

def IsNicknameGood(nickname):
	"""Проверяем никнейм на нормальность"""
	goodsign_s = 'q w e r t y u i o p a s d f g h j k l z x c v b n m Q W E R T Y U I O P A S D F G H J K L Z X C V B N M 1 2 3 4 5 6 7 8 9 0 ! @ # $ % ^ & *'.split(" ")
	user_nikname_0 = list(nickname)
	nickname_cycle = 0
	while nickname_cycle < len(user_nikname_0):
		c_gn_1 = user_nikname_0[nickname_cycle]
		if c_gn_1 not in goodsign_s:
			return -1
		nickname_cycle = nickname_cycle + 1
	return 0
#end define

def UserVerificationAcrossScApi(nickname):
	"""Проверка пользователя по базе данных Star Conflict"""
	global scURL
	AddLog('Start UserVerificationAcrossScApi', 'debug')
	uid = 0
	uv_code = -3 # unknown_error
	if 'empty_result' in nickname:
		uv_code = -2 # Не правильный никнейм
	else:
		try:
			sleep(0.3)
			webform = (urllib.request.urlopen(scURL + nickname).read(1000)).decode('utf-8')
			json_str = json.loads(webform)
		except:
			AddLog('Warning! SC API block with me!!!', 'error')
			return -4, 0
		if json_str['code'] == 1:
			uv_code = -1 # Никнейм не найден
		elif json_str['code'] == 0:
			uv_code = 0
			serchText = 'uid'
			data = json_str['data']
			if (serchText in data):
				uid = int(data[serchText])
		else:
			AddLog('Error UserVerificationAcrossScApi unknown_error')
	AddLog('UserVerificationAcrossScApi nickname=' + nickname + ' uv_code=' + str(uv_code), 'debug')
	return uv_code, uid
# end define

def IsNicknameInMySQL(nickname):
	AddLog('Start IsNicknameInMySQL', 'debug')
	result = uid = 0
	try:
		conn = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db="sc_history_db")
	except MySQLdb.Error as err:
		AddLog("Connection error: {}".format(err), 'error')
		conn.close()
	
	sql = "SELECT * FROM nickname_uid WHERE BINARY nickname='" + nickname + "'"
	AddLog("sql: " + sql, 'debug')
	
	try:
		cur = conn.cursor(MySQLdb.cursors.DictCursor)
		result = cur.execute(sql)
		data = cur.fetchall()
		AddLog("data: " + str(data), 'debug')
	except MySQLdb.Error as err:
		AddLog("Query error: {}".format(err), 'error')
	cur.close()
	conn.close()
	gc.collect()
	if result > 0:
		uid = data[0]['uid']
	return result, uid
#end define

def AddEntryIntoTable(uid, nickname):
	AddLog('Start AddEntryIntoTable', 'debug')
	try:
		conn = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db="sc_history_db")
	except MySQLdb.Error as err:
		AddLog("Connection error: {}".format(err), 'error')
		return
	
	sql = "INSERT INTO nickname_uid (uid, nickname) VALUES ('" + str(uid) + "', '" + nickname + "');"
	sql2 = "CREATE TABLE uid_" + str(uid) + " (date DATE, uid  BIGINT, nickname VARCHAR(20), effRating BIGINT, karma BIGINT, prestigeBonus DOUBLE, gamePlayed BIGINT, gameWin BIGINT, totalAssists BIGINT, totalBattleTime BIGINT, totalDeath BIGINT, totalDmgDone BIGINT, totalHealingDone BIGINT, totalKill BIGINT, totalVpDmgDone BIGINT, clanName VARCHAR(20), clanTag VARCHAR(20));"
	
	try:
		cur = conn.cursor(MySQLdb.cursors.DictCursor)
		result = cur.execute(sql)
		data = cur.fetchall()
		conn.commit()
		result = cur.execute(sql2)
		data = cur.fetchall()
		conn.commit()
	except MySQLdb.Error as err:
		AddLog("Query error: {}".format(err), 'error')
	cur.close()
	conn.close()
	gc.collect()
#end define

class Connect(threading.Thread):
	def __init__(self, conn, addr):
		self.conn = conn
		self.addr = addr
		threading.Thread.__init__(self)
	def run (self):
		try:
			Main(self)
		except ConnectionResetError:
			AddLog("Клиент принудительно разорвал соединение: " + str(self.addr), 'error')
#end define

def Parsing(inputText, startScan, endScan):
	text_0 = inputText[inputText.find(startScan) + len(startScan):]
	outputText = text_0[:text_0.find(endScan)]
	return outputText
#end define

def AddLog(inputText, mode="info"):
	"""Запись в логи"""
	global logName
	if mode == 'debug':
		return
	localDate = time.strftime('%d.%m.%Y, %H:%M:%S'.ljust(21, " "))
	modeText = (' [' + mode + '] ').ljust(10, " ")
	text = localDate + modeText + inputText
	file = open(logName + ".log", 'a')
	file.write(text + "\r\n")
	file.close()
	print(text)
#end define

logName = (sys.argv[0])[:(sys.argv[0]).rfind('.')]

if os.path.isfile(logName):
	os.remove(logName)

AddLog('Запуск сервера scHistoryService на порту: ' + str(port))
while True:
	try:
		sleep(1)
		conn, addr = sock.accept()
		print("--- --- ---")
		print("Есть входящее соединение: " + str(addr))
		Connect(conn, addr).start()
	except BaseException as err:
		AddLog("Critical error: " + str(err), 'error')
# end while
