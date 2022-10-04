# # 作者: qydq
# # 时间: 2019-05-01
# import sys
# import os
# import pymysql


# # 初始化 mysql 数据库
# def init_mysql():
#     # 打开数据库连接,数据库为 myadmin，密码为 1IEXqpKP#5tKSFJG
#     db = pymysql.connect("localhost", "root", "1IEXqpKP#5tKSFJG", "myadmin")
#     # 使用 cursor() 方法创建一个游标对象 cursor
#     cursor = db.cursor()
#     # 返回
#     return db,cursor

# def main():
#     # 读取文件
#     with open(os.path.join(sys.path[0], '俗语.txt'), 'r', encoding='utf-8') as f:
#         # 读取文件内容
#         content = f.read()
#         # 按行分割
#         lines = content.splitlines()
#         # 用于存储结果
#         result = []
#         # 遍历每一行
#         for line in lines:
#             # 去除空格
#             line = line.strip()
#             # 如果是空行，则跳过
#             if len(line) == 0:
#                 continue
#             # 如果包含 俗语或谚语，跳过
#             if '俗语' in line or '谚语' in line or '目录' in line:
#                 continue
#             # 如果包含 、
#             if '、' in line:
#                 # 去除 、之前的内容
#                 line = line.split('、')[1]
#             # 如果包含 ：
#             if '：' in line:
#                 # 去除 ：之后的内容
#                 line = line.split('：')[0]
#             # 如果包含 ——
#             if '——' in line:
#                 # 去除 —— 之后的内容
#                 line = line.split('——')[0]
#             # # 如果包含 .
#             # if '.' in line:
#             #     # 去除 . 之前的内容
#             #     line = line.split('.')[1]
#             # 去除前后空格
#             line = line.strip()
#             print(line)
#             # # 插入数据库

#             db,cursor = init_mysql()
#             # 插入数据库
#             sql = "INSERT INTO books(content,type_id) VALUES (%s,%s)"
#             cursor.execute(sql, (line, 67))
#             db.commit()

# # 程序入口
# if __name__ == '__main__':
#     main()
