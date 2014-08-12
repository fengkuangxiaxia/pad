# -*- coding: utf-8 -*-
import urllib
import urllib2
import re
import MySQLdb
import os
import shutil
import traceback
'''
import sys
reload(sys)   
sys.setdefaultencoding('utf8')
'''
proxy_handler = urllib2.ProxyHandler({'http': '127.0.0.1:8087'})
null_proxy_handler = urllib2.ProxyHandler({})

#抓取一个宠物信息
def getOne(number, useProxy):
    monsterData = {}
    monsterData['id'] = number
    
    url = 'http://pad.skyozora.com/pets/' + str(number)
    content = urllib2.urlopen(url).read()  

    #名字
    namePattern = re.compile(r'<title>(.*?)</title>')
    name = re.findall(namePattern, content)
    try:
        monsterData['name'] = name[0].split(' - ')[1]
    except:
        monsterData['name'] = ''
    pattern = re.compile(r'<table(.*)</table>')
    result = re.findall(pattern, content)

    if(len(result) == 1):
        pattern = re.compile(r'<table(.*?)</table>')
        result = re.findall(pattern, result[0])
        '''
        for i in range(len(result)):
            print i
            print result[i]
        '''
        if(len(result) >= 8):
            #缩略图
            imgPattern = re.compile(r'<img(.*?)>')
            imgUrl = re.findall(imgPattern, result[0])
            if(len(imgUrl) == 1):
                imgUrlResult = imgUrl[0].split('"')[1]
                monsterData['thumbImg'] = imgUrlResult
                path = r"./website/public/img/monsters/" + str(number) + ".jpg"
                if(useProxy):
                    opener = urllib2.build_opener(proxy_handler)
                else:
                    opener = urllib2.build_opener(null_proxy_handler)
                urllib2.install_opener(opener)
                try:
                    with open(path,'wb') as f:
                        f.write(urllib2.urlopen(imgUrlResult).read())
                except:
                    os.remove(path)
                    shutil.copyfile('./website/public/img/monsters/0.jpg', path)
                    print str(number) + ' img error'
                finally:
                    f.close()
                    opener = urllib2.build_opener(null_proxy_handler)
                    urllib2.install_opener(opener)
            else:
                print str(number) + ' img error'

            #系列
            seriesPattern = re.compile(r'<span(.*?)</span>')
            seriesFlag = True
            for i in result:
                if(i.find('相同系列的寵物') >= 0):
                    series = re.findall(seriesPattern, i)
                    if(len(series) == 1):
                        seriesResult = series[0][series[0].find('- ') + 2:]
                        monsterData['series'] = seriesResult
                    else:
                        print str(number) + ' series error'
                    seriesFlag = False
                    break
            if(seriesFlag):
                print str(number) + ' no series error'
                monsterData['series'] = '无'
            
        else:
            '''
            for i in range(len(result)):
                print i
                print result[i]
            '''
            print len(result)
            print str(number) + ' table number error'
    else:
        print str(number) + ' error'

    return monsterData

#抓取宠物信息
def monsterSpider(useProxy = False):
    maxNumber = input('请输入上限:\n')
    
    conn = MySQLdb.connect(host = 'localhost', user='root', passwd='', port=3306, charset = 'utf8')
    cur = conn.cursor()
    conn.select_db('pad')
    
    results = []
    try:
        for i in range(int(maxNumber)):
            cur.execute('select count(*) from monsters where id = ' + str(1 + i))
            count = cur.fetchone()
            count = count[0]
            
            if(count == 0):
                print i + 1
                temp = getOne(1 + i, useProxy)
                if(temp['name'] != ''):
                    results.append((temp['id'], temp['name'], temp['series'], temp['thumbImg']))
    except:
        print str(results[-1][0] + 1) + " getOne error"
    finally:
        cur.executemany('insert into monsters values(%s,%s,%s,%s)', results)
        conn.commit()
        
        #print results
        cur.close()
        conn.close()

#抓取地下城信息
def dungeonsSpider():
    conn = MySQLdb.connect(host = 'localhost', user='root', passwd='', port=3306, charset = 'utf8')
    cur = conn.cursor()
    conn.select_db('pad')

    results = {}

    try:
        url = 'http://pad.skyozora.com/javascript/stage-clear.js'
        content = urllib2.urlopen(url).read()
        content = content[content.find('function dataHierarchy(){') + len('function dataHierarchy(){') + 1 : content.find('dataTree=dataHierarchy();') - 2]       
        allDungeons = content.split('\n\n')

        #优先插入顶级节点
        for dungeons in allDungeons:
            temp = dungeons.strip('\n').split('\n')
            namePattern = re.compile(r'var (.*?)=')
            name = re.findall(namePattern, temp[0])[0]

            if(name != 'output'):
                continue
            else:
                nodes = temp[2:-1]
                for node in nodes:
                    nodeDataPattern = re.compile(r'\((.*?)\)')
                    nodeData = re.findall(nodeDataPattern, node)
                    tempNode = nodeData[0].split(',')
                    tempNodePattern = re.compile(r'\"(.*?)\"')
                    level1Name = re.findall(tempNodePattern, tempNode[0])[0]
                    cur.execute('insert ignore into dungeons(name,level) values(%s,%s)', [level1Name,1])
                    conn.commit()
        #插入2级3级节点    
        for dungeons in allDungeons:
            temp = dungeons.strip('\n').split('\n')
            namePattern = re.compile(r'var (.*?)=')
            name = re.findall(namePattern, temp[0])[0]

            if(name != 'output'):
                nodes = temp[2:]
                for node in nodes:
                    nodeDataPattern = re.compile(r'\((.*?)\)')
                    nodeData = re.findall(nodeDataPattern, node)
                    tempNode = nodeData[0].split(',')
                    tempNodePattern = re.compile(r'\"(.*?)\"')
                    level2Name = re.findall(tempNodePattern, tempNode[0])[0]
                    cur.execute('insert ignore into dungeons(name,level) values(%s,%s)', [level2Name,2])
                    conn.commit()
                    cur.execute('select `id` from `dungeons` where `name` like \'' + level2Name + '\'')
                    level2id = cur.fetchone()[0]
                    if(not results.has_key(name)):
                        results[name] = []
                    results[name].append(level2id)
                    level3Name = tempNode[1:]
                    for i in range(len(level3Name)):
                        level3Name[i] = re.findall(tempNodePattern, level3Name[i])[0]
                        cur.execute('insert ignore into dungeons(name,level,father_id) values(%s,%s,%s)', [level3Name[i],3,level2id])            
            else:
                nodes = temp[2:-1]
                for node in nodes:
                    nodeDataPattern = re.compile(r'\((.*?)\)')
                    nodeData = re.findall(nodeDataPattern, node)
                    tempNode = nodeData[0].split(',')
                    tempNodePattern = re.compile(r'\"(.*?)\"')
                    level1Name = re.findall(tempNodePattern, tempNode[0])[0]
                    level2Name = tempNode[1].strip(' ')
                    cur.execute('select `id` from `dungeons` where `name` like \'' + level1Name + '\'')
                    level1id = cur.fetchone()[0]
                    for j in results[level2Name]:
                        cur.execute('update dungeons set father_id = ' + str(level1id) + ' where id = ' + str(j))
    except Exception, e:
        exstr = traceback.format_exc()
        print exstr
    finally:
        conn.commit()
        cur.close()
        conn.close()

#获取一个地下城的通关队伍
def getOneTeam(dungeonName):
    url = 'http://pad.skyozora.com/team/' + dungeonName + '/Page1/'
    #url = 'http://localhost/pad/a.html'
    content = urllib2.urlopen(url).read()

    #页数
    pagePattern = re.compile(r'共 <font color=yellow><strong>\d*?</strong></font> 頁')
    page = re.findall(pagePattern, content)
    page = page[0]
    page = page[page.find('<strong>') + len('<strong>'):page.find('</strong>')]
    
    #原始数据,获取table标签间的主体数据
    teamsPattern = re.compile(r'<table(.*?)</table>', re.DOTALL)
    teamsRaw = re.findall(teamsPattern, content)

    for i in teamsRaw:
        if((i.find('關卡') != -1) and (i.find('隊長') != -1) and (i.find('隊員') != -1)):
            teamsRaw = '<table' + i + '</table>'
            break

    #按td标签切割为行
    linesPattern = re.compile(r'<td(.*?)</td>', re.DOTALL)
    lines = re.findall(linesPattern, teamsRaw)

    for i in range(len(lines)):
        lines[i] = '<td' + lines[i] + '</td>'

    #组装行成为一组组,每组代表一支队伍的原始数据
    i = 0
    while((i < len(lines)) and (lines[i].find('<td height=6 colspan=9></td>') == -1)):
        i = i + 1

    teamsRaw = []
    teamRaw = []
    while(i < len(lines)):
        teamRaw.append(lines[i])
        if(lines[i].find('<a href="team/') != -1):
            teamsRaw.append(teamRaw)
            teamRaw = []
        i = i + 1

    teams = []
    #爬取队伍详细数据
    for teamRaw in teamsRaw:
        team = {}
        hpAndStonePattern = re.compile(r'<b>(.*?)</b>')
        hp = -1
        stone = -1
        monsters = []
        monsterPattern = re.compile(r'href="pets/\d*?"')
        description = ''
        descriptionPattern = re.compile(r'<description class="slide">(.*?)</description>', re.DOTALL)
        for i in teamRaw:
            #爬取hp和魔法石用量
            hpAndStone = re.findall(hpAndStonePattern, i)
            if(len(hpAndStone) > 0):
                if(hp == -1):
                    hp = hpAndStone[0]
                elif(stone == -1):
                    stone = hpAndStone[0]
                else:
                    print hpAndStone
            #爬取宠物
            monster = re.findall(monsterPattern, i)
            if(len(monster) > 0):
                if(len(monsters) < 6):
                    temp = monster[0]
                    monsters.append(temp[temp.find('/') + 1:temp.rfind('\"')])
            #爬取说明
            desc = re.findall(descriptionPattern, i)
            if(len(desc) > 0):
                description = description + desc[0]

        team['monsters'] = monsters
        team['hp'] = hp
        team['stone'] = stone
        team['description'] = description
        teams.append(team)

    return page,teams   
    '''
    with open('./c.html','wb') as f:
        for i in teams:
            for j in i.keys():
                if(j == 'monsters'):
                    f.write(j + ':')
                    for k in i[j]:
                        f.write(k + ',')
                    f.write('\n')
                else:
                    f.write(j + ':' + i[j] + '\n')
            f.write('\n')
    f.close()
    '''

def teamsSpider():
    conn = MySQLdb.connect(host = 'localhost', user='root', passwd='', port=3306, charset = 'utf8')
    cur = conn.cursor()
    conn.select_db('pad')

    try:
        cur.execute('select `name` from `dungeons` where `level` = 3')
        dungeonNames = cur.fetchall()
        
        dungeonName = dungeonNames[-3][0]
        dungeonName = urllib.quote(dungeonName.encode('utf8'))
        page,teams = getOneTeam(dungeonName)
        print page
        
    except Exception, e:
        exstr = traceback.format_exc()
        print exstr
    finally:
        conn.commit()
        cur.close()
        conn.close()
        
def main():
    #monsterSpider()
    #dungeonsSpider()
    #teamsSpider()

main()
