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

scURL = 'http://gmt.star-conflict.com/pubapi/v1/userinfo.php?nickname='
bigUserArray = [[]]

def SaveInformation():
	"""РЎРѕС…СЂР°РЅСЏРµРј РёСЃС‚РѕСЂРёСЋ SC"""
	AddLog("Start SaveInformation", "debug")
	
	# Р—Р°РїРёСЃР°С‚СЊ РІСЂРµРјСЏ РЅР°С‡Р°Р»Рѕ РІ Р‘Р”
	RecordStartTime()
	
	# Р”РѕСЃС‚Р°РµРј СЃРїРёСЃРѕРє РїРѕР»СЊР·РѕРІР°С‚РµР»РµР№ РёР· Р‘Р”
	FindAllUserInMySQL()
	
	# РџСЂРѕР±РёРІР°РµРј РєР°Р¶РґРѕРіРѕ РїРѕР»СЊР·РѕРІР°С‚РµР»СЏ РїРѕ Р‘Р” Star Conflict
	DiscoverAllUserFromSC()
	
	# Р—Р°РїРёСЃР°С‚СЊ РІСЂРµРјСЏ РєРѕРЅС†Р° РІ Р‘Р”
	RecordEndTime()
	
#end define

def RecordStartTime():
	AddLog("Start RecordStartTime", "debug")
	
	try:
		conn = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db="other_db")
	except MySQLdb.Error as err:
		AddLog("Connection error: {}".format(err), 'error')
		return
	
	sql = "UPDATE timestamps SET value=" + str(time.time()) + " WHERE nomination='RecordStartTime'"
	
	try:
		cur = conn.cursor(MySQLdb.cursors.DictCursor)
		result = cur.execute(sql)
		row = cur.fetchone()
		conn.commit()
	except MySQLdb.Error as err:
		AddLog("Query error: {}".format(err), 'error')
	cur.close()
	conn.close()
	gc.collect()
	
#end define

def RecordEndTime():
	AddLog("Start RecordEndTime", "debug")
	
	try:
		conn = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db="other_db")
	except MySQLdb.Error as err:
		AddLog("Connection error: {}".format(err), 'error')
		return
	
	sql = "UPDATE timestamps SET value='" + str(time.time()) + "' WHERE nomination='RecordEndTime'"
	
	try:
		cur = conn.cursor(MySQLdb.cursors.DictCursor)
		result = cur.execute(sql)
		row = cur.fetchone()
		conn.commit()
	except MySQLdb.Error as err:
		AddLog("Query error: {}".format(err), 'error')
	cur.close()
	conn.close()
	gc.collect()
	
#end define

def FindAllUserInMySQL():
	AddLog("Start FindAllUserInMySQL", "debug")
	global bigUserArray
	bigUserArray = [[]]
	try:
		conn = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db="sc_history_db")
	except MySQLdb.Error as err:
		AddLog("Connection error: {}".format(err), 'error')
		return
	
	sql = "SELECT * FROM nickname_uid;"
	
	try:
		cur = conn.cursor(MySQLdb.cursors.DictCursor)
		result = cur.execute(sql)
		row = cur.fetchone()
		
		
		while row is not None:
			nickname = row['nickname']
			uid = row['uid']
			
			bigUserArray = bigUserArray + [[nickname, uid]]
			
			row = cur.fetchone()
	except MySQLdb.Error as err:
		AddLog("Query error: {}".format(err), 'error')
	cur.close()
	conn.close()
	gc.collect()
#end define

def DiscoverAllUserFromSC():
	AddLog("Start DiscoverAllUserFromSC", "debug")
	global bigUserArray, scURL
	
	data_now = time.strftime('%Y-%m-%d')
	
	# РџРѕРєР»СЋС‡Р°РµРјСЃСЏ Рє MySQL
	try:
		conn = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db="sc_history_db")
		conn2 = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db="sc_clan_db")
	except MySQLdb.Error as err:
		AddLog("Connection error: {}".format(err), 'error')
		return
	try:
		cur = conn.cursor(MySQLdb.cursors.DictCursor)
		cur2 = conn2.cursor(MySQLdb.cursors.DictCursor)
	except MySQLdb.Error as err:
		AddLog("Query error: {}".format(err), 'error')
		return
	
	# РџРµСЂРµР±РёСЂР°РµРј РїРѕР»СЊР·РѕРІР°С‚РµР»РµР№
	for user in bigUserArray:
		# РџСЂРѕР±РёРІР°РµРј РёРЅС„Сѓ РїРѕ Р‘Р” SC
		if len(user) > 0:
			uid = user[1]
			nickname = user[0]
			try:
				sleep(0.3)
				while True:
					try:
						webform = (urllib.request.urlopen(scURL + nickname).read(1000)).decode('utf-8')
						json_str = json.loads(webform)
						break
					except:
						AddLog('Warning! SC API block with me!!!', 'error')
						sleep(10)
				if json_str['code'] == 1:
					uv_code = -1 # РќРёРєРЅРµР№Рј РЅРµ РЅР°Р№РґРµРЅ
					DeleteInTheTop100(uid);
				elif json_str['code'] == 0:
					# Р—Р°РїРёСЃС‹РІР°РµРј РёРЅС„РѕСЂРјР°С†РёСЋ РІ Р‘Р”
					WriteInToMySQL(data_now, conn, conn2, cur, cur2, uid, nickname, json_str['data'])
				else:
					AddLog('Error UserVerificationAcrossScApi unknown_error')
					return
			except BaseException as err:
				AddLog(str(err), 'error')
				return
	#end for
	cur.close()
	conn.close()
	gc.collect()
#end define

def WriteInToMySQL(data_now, conn, conn2, cur, cur2, uid, nickname, data):
	AddLog("Start WriteInToMySQL " + str(uid), "debug")
	
	
	#РџСЂРµСЂРІР°С‚СЊ, РµСЃР»Рё uid РЅРµ СЃРѕРІРїР°РґР°СЋС‚
	if uid != data['uid']:
		return
	
	effRating = karma = prestigeBonus = gamePlayed = gameWin = totalAssists = totalBattleTime = totalDeath = totalDmgDone = totalHealingDone = totalKill = totalVpDmgDone = 0
	clanName = clanTag = ''
	
	serchText = 'effRating'
	if (serchText in data):
		effRating = int(data[serchText])
	serchText = 'karma'	
	if (serchText in data):
		karma = int(data[serchText])
	serchText = 'prestigeBonus'
	if (serchText in data):
		prestigeBonus = float(data[serchText]) # Р”СЂРѕР±РЅРѕРµ С‡РёСЃР»Рѕ
	#end if
	
	serchText = 'pvp'
	if (serchText in data):
		pvp = data[serchText]
		serchText = 'gamePlayed'
		if (serchText in pvp):
			gamePlayed = int(pvp[serchText])
		serchText = 'gameWin'
		if (serchText in pvp):
			gameWin = int(pvp[serchText])
		serchText = 'totalAssists'
		if (serchText in pvp):
			totalAssists = int(pvp[serchText])
		serchText = 'totalBattleTime'
		if (serchText in pvp):
			totalBattleTime = int(pvp[serchText])
		serchText = 'totalDeath'
		if (serchText in pvp):
			totalDeath = int(pvp[serchText])
		serchText = 'totalDmgDone'
		if (serchText in pvp):
			totalDmgDone = int(pvp[serchText])
		serchText = 'totalHealingDone'
		if (serchText in pvp):
			totalHealingDone = int(pvp[serchText])
		serchText = 'totalKill'
		if (serchText in pvp):
			totalKill = int(pvp[serchText])
		serchText = 'totalVpDmgDone'
		if (serchText in pvp):
			totalVpDmgDone = int(pvp[serchText])
	#end if
	
	serchText = 'clan'
	if (serchText in data):
		clan = data[serchText]
		serchText = 'name'
		if (serchText in clan):
			clanName = clan[serchText]
		serchText = 'tag'
		if (serchText in clan):
			clanTag = clan[serchText]
	#end if
	
	WriteInTheTop100(conn, cur, uid, nickname, effRating, karma, prestigeBonus, gamePlayed, gameWin, totalAssists, totalBattleTime, totalDeath, totalDmgDone, totalHealingDone, totalKill, totalVpDmgDone, clanName, clanTag)
	
	WriteInTheCorporationsHistory(data_now, conn2, cur2, effRating, karma, prestigeBonus, gamePlayed, gameWin, totalAssists, totalBattleTime, totalDeath, totalDmgDone, totalHealingDone, totalKill, totalVpDmgDone, clanName, clanTag)
	
	sql = "INSERT INTO uid_" + str(uid) + " (date, uid, nickname, effRating, karma, prestigeBonus, gamePlayed, gameWin, totalAssists, totalBattleTime, totalDeath, totalDmgDone, totalHealingDone, totalKill, totalVpDmgDone, clanName, clanTag) VALUES ('" + data_now + "', '" + str(uid) + "', '" + str(nickname) + "', '" + str(effRating) + "', '" + str(karma) + "', '" + str(prestigeBonus) + "', '" + str(gamePlayed) + "', '" + str(gameWin) + "', '" + str(totalAssists) + "', '" + str(totalBattleTime) + "', '" + str(totalDeath) + "', '" + str(totalDmgDone) + "', '" + str(totalHealingDone) + "', '" + str(totalKill) + "', '" + str(totalVpDmgDone) + "', '" + clanName + "', '" + clanTag + "');"
	
	try:
		result = cur.execute(sql)
		data = cur.fetchall()
		conn.commit()
	except MySQLdb.Error as err:
		AddLog("Query error: {}".format(err), 'error')
#end define

def WriteInTheCorporationsHistory(data_now, conn, cur, effRating, karma, prestigeBonus, gamePlayed, gameWin, totalAssists, totalBattleTime, totalDeath, totalDmgDone, totalHealingDone, totalKill, totalVpDmgDone, clanName, clanTag):
	"""Р”РѕР±Р°РІРёС‚СЊ Р·Р°РїРёСЃСЊ РІ corporations_history"""
	AddLog("Start WriteInTheCorporationsHistory", "debug")
	
	# РџСЂРµСЂРІР°С‚СЊ, РµСЃР»Рё Р±РµР· РєРѕСЂРїРѕСЂР°С†РёРё
	if (len(clanName) == 0):
		return
	
	# РќР°Р№С‚Рё СЃРІРµР¶СѓСЋ Р·Р°РїРёСЃСЊ
	sql = "SELECT * FROM corporations_history WHERE date='" + data_now + "' and BINARY clanName='" + clanName + "' and BINARY clanTag='" + clanTag + "'"
	
	try:
		result = cur.execute(sql)
		data = cur.fetchall()
	except MySQLdb.Error as err:
		AddLog("Query error: {}".format(err), 'error')
	#end try
	
	AddLog("result: " + str(result));
	
	# РЎРѕР·РґР°С‚СЊ СЃРІРµР¶СѓСЋ Р·Р°РїРёСЃСЊ РєРѕСЂРїРѕСЂР°С†РёРё, РµСЃР»Рё РѕРЅР° РЅРµ СЃРѕР·РґР°РЅР°
	if (result == 0):
		sql = "INSERT INTO corporations_history (date, clanName, clanTag, effRating, karma, prestigeBonus, gamePlayed, gameWin, totalAssists, totalBattleTime, totalDeath, totalDmgDone, totalHealingDone, totalKill, totalVpDmgDone, number) VALUES ('" + data_now + "', '" + clanName + "', '" + clanTag + "', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);"
		try:
			result = cur.execute(sql)
			data = cur.fetchall()
			conn.commit()
		except MySQLdb.Error as err:
			AddLog("Query error: {}".format(err), 'error')
	
	# РџСЂРёР±Р°РІРёС‚СЊ Р·РЅР°С‡РµРЅРёСЏ РёРіСЂРѕРєР° РІ РѕР±С‰СѓСЋ СЃСѓРјРјСѓ С‚Р°Р±Р»РёС†С‹
	sql = "UPDATE corporations_history SET number = number + 1, effRating = effRating + " + str(effRating) + ", karma = karma + " + str(karma) + ", prestigeBonus = prestigeBonus + " + str(prestigeBonus) + ", gamePlayed = gamePlayed + " + str(gamePlayed) + ", gameWin = gameWin + " + str(gameWin) + ", totalAssists = totalAssists + " + str(totalAssists) + ", totalBattleTime = totalBattleTime + " + str(totalBattleTime) + ", totalDeath = totalDeath + " + str(totalDeath) + ", totalDmgDone = totalDmgDone + " + str(totalDmgDone) + ", totalHealingDone = totalHealingDone + " + str(totalHealingDone) + ", totalKill = totalKill + " + str(totalKill) + ", totalVpDmgDone = totalVpDmgDone + " + str(totalVpDmgDone) + " WHERE date = '" + data_now + "' and BINARY clanName = '" + clanName + "' and BINARY clanTag = '" + clanTag+ "'"
	
	try:
		result = cur.execute(sql)
		data = cur.fetchall()
		conn.commit()
	except MySQLdb.Error as err:
		AddLog("Query error: {}".format(err), 'error')
#end define

def WriteInTheTop100(conn, cur, uid, nickname, effRating, karma, prestigeBonus, gamePlayed, gameWin, totalAssists, totalBattleTime, totalDeath, totalDmgDone, totalHealingDone, totalKill, totalVpDmgDone, clanName, clanTag):
	"""Р”РѕР±Р°РІРёС‚СЊ Р·Р°РїРёСЃСЊ РІ TOP100"""
	sql = "SELECT * FROM top100 WHERE BINARY uid='" + str(uid) + "'"
	
	try:
		result = cur.execute(sql)
		data = cur.fetchall()
	except MySQLdb.Error as err:
		AddLog("Query error: {}".format(err), 'error')
	#end try
	
	effRating_old = karma_old = prestigeBonus_old = gamePlayed_old = gameWin_old = totalAssists_old = totalBattleTime_old = totalDeath_old = totalDmgDone_old = totalHealingDone_old = totalKill_old = totalVpDmgDone_old = 0
	
	if len(data) > 0:
		
		effRating_old = int(float(data[0]['effRating']))
		karma_old = int(float(data[0]['karma']))
		prestigeBonus_old = float(data[0]['prestigeBonus']) # РґСЂРѕР±РЅРѕРµ
		gamePlayed_old = int(float(data[0]['gamePlayed']))
		gameWin_old = int(float(data[0]['gameWin']))
		totalAssists_old = int(float(data[0]['totalAssists']))
		totalBattleTime_old = int(float(data[0]['totalBattleTime']))
		totalDeath_old = int(float(data[0]['totalDeath']))
		totalDmgDone_old = int(float(data[0]['totalDmgDone']))
		totalHealingDone_old = int(float(data[0]['totalHealingDone']))
		totalKill_old = int(float(data[0]['totalKill']))
		totalVpDmgDone_old = int(float(data[0]['totalVpDmgDone']))
	#end if
	
	if totalDeath != 0:
		kd = totalKill / totalDeath
		kda = (totalKill + totalAssists) / totalDeath
		kd = float("%.2f" % kd) # РґСЂРѕР±РЅРѕРµ
		kda = float("%.2f" % kda) # РґСЂРѕР±РЅРѕРµ
	else:
		kd = kda = 0
	if gamePlayed != 0:
		wr = gameWin / gamePlayed
		wr = float("%.2f" % wr) # РґСЂРѕР±РЅРѕРµ
	else:
		wr = 0
	if (gamePlayed - gameWin) != 0:
		wl = gameWin / (gamePlayed - gameWin)
		wl = float("%.2f" % wl) # РґСЂРѕР±РЅРѕРµ
	else:
		wl = gameWin / 1
	#end if
	
	effRating2 = effRating - effRating_old
	karma2 = karma - karma_old
	prestigeBonus2 = prestigeBonus - prestigeBonus_old
	gamePlayed2 = gamePlayed - gamePlayed_old
	gameWin2 = gameWin - gameWin_old
	totalAssists2 = totalAssists - totalAssists_old
	totalBattleTime2 = totalBattleTime - totalBattleTime_old
	totalDeath2 = totalDeath - totalDeath_old
	totalDmgDone2 = totalDmgDone - totalDmgDone_old
	totalHealingDone2 = totalHealingDone - totalHealingDone_old
	totalKill2 = totalKill - totalKill_old
	totalVpDmgDone2 = totalVpDmgDone - totalVpDmgDone_old
	
	if totalDeath2 != 0:
		kd2 = totalKill2 / totalDeath2
		kda2 = (totalKill2 + totalAssists2) / totalDeath2
		kd2 = float("%.2f" % kd2) # РґСЂРѕР±РЅРѕРµ
		kda2 = float("%.2f" % kda2) # РґСЂРѕР±РЅРѕРµ
	else:
		kd2 = kda2 = 0
	if gamePlayed2 != 0:
		wr2 = gameWin2 / gamePlayed2
		wr2 = float("%.2f" % wr2) # РґСЂРѕР±РЅРѕРµ
	else:
		wr2 = 0
	if (gamePlayed2 - gameWin2) != 0:
		wl2 = gameWin2 / (gamePlayed2 - gameWin2)
		wl2 = float("%.2f" % wl2) # РґСЂРѕР±РЅРѕРµ
	else:
		wl2 = 0
	
	if result > 0:
		sql = "DELETE FROM top100 WHERE uid='" + str(uid) + "'"
		try:
			result = cur.execute(sql)
			data = cur.fetchall()
			conn.commit()
		except MySQLdb.Error as err:
			AddLog("Query error: {}".format(err), 'error')
	#end if
	
	sql = "INSERT INTO top100 (uid, nickname, kd, kd2, kda, kda2, wr, wr2, wl, wl2, effRating, effRating2, karma, karma2, prestigeBonus, prestigeBonus2, gamePlayed, gamePlayed2, gameWin, gameWin2, totalAssists, totalAssists2, totalBattleTime, totalBattleTime2, totalDeath, totalDeath2, totalDmgDone, totalDmgDone2, totalHealingDone, totalHealingDone2, totalKill, totalKill2, totalVpDmgDone, totalVpDmgDone2, clanName, clanTag) VALUES ('" + str(uid) + "', '" + str(nickname) + "', '" + str(kd) + "', '" + str(kd2) + "', '" + str(kda) + "', '" + str(kda2) + "', '" + str(wr) + "', '" + str(wr2) + "', '" + str(wl) + "', '" + str(wl2) + "', '" + str(effRating) + "', '" + str(effRating2) + "', '" + str(karma) + "', '" + str(karma2) + "', '" + str(prestigeBonus) + "', '" + str(prestigeBonus2) + "', '" + str(gamePlayed) + "', '" + str(gamePlayed2) + "', '" + str(gameWin) + "', '" + str(gameWin2) + "', '" + str(totalAssists) + "', '" + str(totalAssists2) + "', '" + str(totalBattleTime) + "', '" + str(totalBattleTime2) + "', '" + str(totalDeath) + "', '" + str(totalDeath2) + "', '" + str(totalDmgDone) + "', '" + str(totalDmgDone2) + "', '" + str(totalHealingDone) + "', '" + str(totalHealingDone2) + "', '" + str(totalKill) + "', '" + str(totalKill2) + "', '" + str(totalVpDmgDone) + "', '" + str(totalVpDmgDone2) + "', '" + str(clanName) + "', '" + str(clanTag) + "');"
	
	try:
		result = cur.execute(sql)
		data = cur.fetchall()
		conn.commit()
	except MySQLdb.Error as err:
		AddLog("Query error: {}".format(err), 'error')
#end define

def DeleteInTheTop100(uid):
	AddLog("Start DeleteInTheTop100", "debug")
	try:
		conn = MySQLdb.connect(host=mysqlHost, user=mysqlUser, passwd=mysqlPass, db="sc_history_db")
	except MySQLdb.Error as err:
		AddLog("Connection error: {}".format(err), 'error')
		conn.close()
		
	sql = "DELETE FROM top100 WHERE uid='" + str(uid) + "'"
	
	try:
		cur = conn.cursor(MySQLdb.cursors.DictCursor)
		result = cur.execute(sql)
		data = cur.fetchall()
		conn.commit()
	except MySQLdb.Error as err:
		AddLog("Query error: {}".format(err), 'error')
	cur.close()
	conn.close()
	gc.collect()
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


# Start database update
logName = (sys.argv[0])[:(sys.argv[0]).rfind('.')]

if os.path.isfile(logName):
	os.remove(logName)

SaveInformation()


