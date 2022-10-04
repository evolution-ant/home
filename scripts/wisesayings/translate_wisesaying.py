import json
import requests
from zhconv import convert
import pymysql


# 初始化数据库方法
def init_db():
    db = pymysql.connect(host='localhost', port=3306, user='root', passwd='1IEXqpKP#5tKSFJG', db='myadmin', charset='utf8')
    return db
# 查询数据方法
def select_db(db,sql):
    cursor = db.cursor()
    cursor.execute(sql)
    data = cursor.fetchall()
    return data
# 更新数据方法
def update_db(db,sql):
    cursor = db.cursor()
    cursor.execute(sql)
    db.commit()
    return True

# 翻译方法
def translate(text):
    url = 'http://23.82.16.134:55550/trans_content'
    payload={'content': text,
    'lang': 'en',
    'desc_lang': 'zh'}
    files=[]
    headers = {}
    response = requests.request("POST", url, headers=headers, data=payload, files=files)
    json_data = json.loads(response.text)
    text = json_data['data']['zh-TW']
    ch_translated_text = convert(text, 'zh-cn')
    return ch_translated_text

# 程序入口
if __name__ == '__main__':
    # 查询
    # sql = 'SELECT en_content,id FROM wisesayings WHERE content IS NULL LIMIT 10000 OFFSET 0'
    # sql = 'SELECT en_content,id FROM wisesayings WHERE content IS NULL LIMIT 10000 OFFSET 10000'
    sql = 'SELECT en_content,id FROM wisesayings WHERE content IS NULL LIMIT 10000 OFFSET 20000'
    # sql = 'SELECT en_content,id FROM wisesayings WHERE content IS NULL '
    db = init_db()
    data = select_db(db,sql)
    print('data:', len(data))
    # 翻译
    for item in data:
        en_content = item[0]
        # # 遍历 en_content 去除字符串中数字，英语，符号以外的字符
        # for i in en_content:
        #     if not (i.isdigit() and i.isalpha() and i in ['.',',','?','!',';','\'','\"']):
        #         en_content = en_content.replace(i,'')
        id = item[1]
        zh_content = translate(en_content)
        # sql 处理引号
        zh_content = pymysql.escape_string(zh_content)
        # 更新
        sql = 'UPDATE wisesayings SET content = "%s" WHERE id = %s' % (zh_content,id)
        print(en_content)
        print(zh_content)
        update_db(db,sql)
