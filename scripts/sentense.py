'''

'''
import requests_cache
import time
import re
import pymysql

# mysql 数据库
def init_mysql():
    # 打开数据库连接,数据库为 myadmin，密码为 1IEXqpKP#5tKSFJG
    db = pymysql.connect("localhost", "root", "1IEXqpKP#5tKSFJG", "myadmin")
    # 使用 cursor() 方法创建一个游标对象 cursor
    cursor = db.cursor()
    # 返回
    return db,cursor


# 根据 word 查询 en_sentence
def get_sentence(word):
    # 初始化缓存
    session = requests_cache.CachedSession(cache_name='cache', backend='sqlite', expire_after=3600)
    # 请求
    response = session.get(f'https://skell.sketchengine.eu/api/run.cgi/concordance?query={word}&lang=English&format=json')
    # 获取 json
    json = response.json()
    # 获取 Lines
    lines = json['Lines']
    line = lines[0]
    # 如果 line['Left'] 长度为 0，则 line_left 为 ''，否则为 line['Left'][0]['Str']
    line_left = '' if len(line['Left']) == 0 else line['Left'][0]['Str']
    # 如果 line['Right'] 长度为 0，则 line_right 为 ''，否则为 line['Right'][0]['Str']
    line_right = '' if len(line['Right']) == 0 else line['Right'][0]['Str']
    # 如果 line['Kwic'] 长度为 0，则 line_kwic 为 ''，否则为 line['Kwic'][0]['Str']
    line_kwic = '' if len(line['Kwic']) == 0 else line['Kwic'][0]['Str']
    # 拼接
    en_sentence = line_left + line_kwic + line_right
    deepl_url = "https://api.deepl.com/v2/translate";
    deepl_key = "0b64fbbe-75f8-1954-30a3-f2fd7f45a839";
    deepl_text = en_sentence;
    deepl_source_lang = "en";
    deepl_target_lang = "zh";
    # post
    response = session.post(deepl_url, data={'text': deepl_text, 'source_lang': deepl_source_lang, 'target_lang': deepl_target_lang, 'auth_key': deepl_key})
    json = response.json()
    zh_sentence = json['translations'][0]['text']
    return en_sentence,zh_sentence

# 程序入口
if __name__ == '__main__':
    # 查询 en_sentence 为空的数据
    db,cursor = init_mysql()
    sql = "select content from words where en_sentence is null"
    cursor.execute(sql)
    results = cursor.fetchall()
    for result in results:
        word = result[0]
        # 如果包含中文，则跳过
        if re.search(u"[\u4e00-\u9fa5]", word):
            continue
        print("word:", word)
        # 查询 en_sentence
        en_sentence,zh_sentence = get_sentence(word)
        print("en_sentence:", en_sentence)
        print("zh_sentence:", zh_sentence)
        # 更新 en_sentence,zh_sentence
        sql = "update words set en_sentence = '%s',zh_sentence = '%s' where content = '%s'" % (en_sentence,zh_sentence,word)
        cursor.execute(sql)
        db.commit()
    print("done")
