import json
import os
import pymysql

# 初始化 mysql 数据库
def init_mysql():
    # 打开数据库连接,数据库为 myadmin，密码为 1IEXqpKP#5tKSFJG
    db = pymysql.connect("localhost", "root", "1IEXqpKP#5tKSFJG", "myadmin")
    # 使用 cursor() 方法创建一个游标对象 cursor
    cursor = db.cursor()
    # 返回
    return db,cursor

# 插入 author 数据
def insert_author():
    # 读取author目录下的所有文件
    files = os.listdir('author')
    # 遍历文件
    authors = []
    for file in files:
        # 拼接文件路径
        file_path = 'author/'+file
        print(file_path)
        # 读取 json 文件
        with open(file_path, 'r') as f:
            # 加载 json 文件
            data = json.load(f)
            # 遍历 data
            for item in data:
                # 获取 author
                author = item['author']
                authors.append(author)
                # 获取 quotes
                quotes = item['quotes']
                # 遍历 quotes
                for quote in quotes:
                    # 插入数据库
                    db,cursor = init_mysql()
                    # 插入数据库
                    sql = "INSERT INTO wisesayings (en_content,author) VALUES (%s,%s)"
                    print(sql)
                    cursor.execute(sql, (quote, author))
                    db.commit()



# 插入 author 数据
def insert_topic():
    # 读取author目录下的所有文件
    files = os.listdir('topic')
    files = ['s.json']
    # files 排序
    files.sort()
    # 遍历文件
    authors = []
    for file in files:
        # 如果是 a.json 文件则跳过
        # 拼接文件路径
        file_path = 'topic/'+file
        # print(file_path)
        # 读取 json 文件
        with open(file_path, 'r') as f:
            # 加载 json 文件
            data = json.load(f)
            # 遍历 data
            for item in data:
                topic = item['topic']
                quotes = item['quotes']
                # 遍历 quotes
                for quote in quotes:
                    print(quote)
                    author = quote['author']
                    authors.append(author)
                    # 获取 quotes
                    quote = quote['quote']
                    # 查询 wisesayings 表是否已有 en_content 为 quote 的数据
                    db,cursor = init_mysql()
                    sql = "SELECT * FROM wisesayings WHERE en_content = %s"
                    cursor.execute(sql, (quote))
                    result = cursor.fetchone()
                    if result:
                        print("🔴已存在")
                        # 更新 topic 字段
                        sql = "UPDATE wisesayings SET topic = %s WHERE en_content = %s"
                        # print(sql)
                        cursor.execute(sql, (topic, quote))
                        db.commit()
                    else:
                        print("🟢不存在")
                        # 如果没有，插入 wisesayings 表
                        sql = "INSERT INTO wisesayings (en_content,author,topic) VALUES (%s,%s,%s)"
                        # print(sql)
                        cursor.execute(sql, (quote, author,topic))
                        db.commit()

# 程序入口
if __name__ == '__main__':
    # insert_author()
    insert_topic()

