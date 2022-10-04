# import pymysql

# # 初始化 mysql 数据库
# def init_mysql():
#     # 打开数据库连接,数据库为 myadmin，密码为 1IEXqpKP#5tKSFJG
#     db = pymysql.connect("localhost", "root", "1IEXqpKP#5tKSFJG", "myadmin")
#     # 使用 cursor() 方法创建一个游标对象 cursor
#     cursor = db.cursor()
#     # 返回
#     return db,cursor

# # 程序入口
# def main():
#     # 读取文件
#     with open('俗语对比.txt', 'r', encoding='utf-8') as f:
#         # 读取文件内容
#         content = f.read()
#         # 按行分割
#         lines = content.splitlines()
#         # 用于存储结果
#         result = []
#         # 遍历每一行
#         for line in lines:
#             # 如果是空行，则跳过
#             if len(line) == 0:
#                 continue
#             # 去除 、之前的内容
#             line = line.split('、')[1]
#             # 去除 - 之后的内容
#             line = line.split('-')[0]
#             # 去除 俗话说：
#             line = line.replace('俗话说：', '')
#             print(line)
#             # 插入数据库
#             db,cursor = init_mysql()
#             # 插入数据库
#             sql = "INSERT INTO books(content,type_id) VALUES (%s,%s)"
#             cursor.execute(sql, (line, 69))
#             db.commit()

# if __name__ == '__main__':
#     main()
