# -*- coding: utf-8 -*-
import urllib
import urllib2
import re
import MySQLdb
import os
import shutil

proxy_handler = urllib2.ProxyHandler({'http': '127.0.0.1:8087'})
null_proxy_handler = urllib2.ProxyHandler({})

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

def main():
    conn = MySQLdb.connect(host = 'localhost', user='root', passwd='', port=3306, charset = 'utf8')
    cur = conn.cursor()
    conn.select_db('pad')
    
    results = []
    for i in range(310):
        print i
        cur.execute('select count(*) from monsters where id = ' + str(1 + i))
        count = cur.fetchone()
        count = count[0]
        
        if(count == 0):
            temp = getOne(1 + i)
            if(temp['name'] != ''):
                results.append((temp['id'], temp['name'], temp['series'], temp['thumbImg']))
    
    cur.executemany('insert into monsters values(%s,%s,%s,%s)', results)
    conn.commit()
    
    #print results
    cur.close()
    conn.close()

main()
