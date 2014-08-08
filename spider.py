# -*- coding: utf-8 -*-
import urllib
import urllib2
import re
import MySQLdb
import os
import shutil
import traceback

proxy_handler = urllib2.ProxyHandler({'http': '127.0.0.1:8087'})
null_proxy_handler = urllib2.ProxyHandler({})

#抓取一个宠物信息
def getOne(number):
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
                opener = urllib2.build_opener(proxy_handler)
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
def monsterSpider():
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
                temp = getOne(1 + i)
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

        #allDungeons = [allDungeons[0]]
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
                    cur.execute('insert ignore into dungeons(name,level) values(%s,%s)', [level1Name,1])
                    conn.commit()
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


def main():
    #monsterSpider()
    #dungeonsSpider()
