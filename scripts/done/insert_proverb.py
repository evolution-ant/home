import os
import time
import pymysql

# 初始化 mysql 数据库
def init_mysql():
    # 打开数据库连接,数据库为 myadmin，密码为 1IEXqpKP#5tKSFJG
    db = pymysql.connect("localhost", "root", "1IEXqpKP#5tKSFJG", "myadmin")
    # 使用 cursor() 方法创建一个游标对象 cursor
    cursor = db.cursor()
    # 返回
    return db,cursor

# 程序入口
if __name__ == '__main__':
    # 读取 proverb.txt
    with open(os.path.join(os.path.dirname(__file__), 'proverb.txt'), 'r', encoding='utf-8') as f:
        lines = f.readlines()
        # 循环 lines
        for line in lines:
            # 去除前后空格
            line = line.strip()
            # 如果 line 为空，则跳过
            if len(line) == 0:
                continue
            # 判断开头第一个字符是否为数字
            if line[0].isdigit():
                # 用 、 分割 line
                line_list = line.split('、')
                # 去除 。
                line_list = [line.replace('。', '') for line in line_list]
                print(line_list[1])
                # 插入数据库
                db,cursor = init_mysql()
                # 插入数据库
                sql = "INSERT INTO books(content,type_id) VALUES (%s,%s)"
                cursor.execute(sql, (line_list[1], 67))
                db.commit()

