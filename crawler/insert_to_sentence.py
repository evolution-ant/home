import os
import json

# 初始化 mysql 数据库函数
def init_mysql():
    # 初始化数据库连接
    import pymysql
    # 连接数据库
    conn = pymysql.connect(host='localhost', port=3306, user='root', passwd='1IEXqpKP#5tKSFJG', db='myadmin', charset='utf8')
    # 创建游标
    cursor = conn.cursor()
    return conn, cursor

# 插入数据库
def insert_to_sentence(conn, cursor, zh_content, en_content):
    # 插入数据
    sql = 'insert into sentences(content, translations) values(%s, %s)'
    cursor.execute(sql, (zh_content, en_content))
    # 提交
    conn.commit()
    print('插入成功')

# 查询数据库
def select_from_sentence(conn, cursor):
    # 查询数据
    sql = 'select content,translations from sentences'
    cursor.execute(sql)
    # 获取查询结果
    results = cursor.fetchall()
    return results

# 入口
if __name__ == '__main__':
    number_contents = []
    # 读取 json 文件
    with open('./number_contents.json', 'r', encoding='utf-8') as f:
        number_contents = json.load(f)
    # 初始化数据库
    conn, cursor = init_mysql()
    # 查询数据库
    results = select_from_sentence(conn, cursor)
    # 循环写入文件
    for result in results:
        # 获取 content 和 translations
        content = result[0]
        translations = result[1]
        # 写入文件
        with open('./sentences.txt', 'a', encoding='utf-8') as f:
            f.write(content + '\n')
            f.write(translations + '\n')
    # 遍历 number_contents 列表
    # for number_content in number_contents:
    #     # 获取 number 和 en_content,zh_content
    #     number = number_content['number']
    #     zh_content = number_content['zh_content']
    #     en_content = number_content['en_content']
    #     # 插入数据库
    #     insert_to_sentence(conn, cursor, zh_content, en_content)
