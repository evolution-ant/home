from itertools import count
import json
import os
from sys import flags

# 读取 json 文件
def read_json(file_path):
    # 读取文件
    with open(file_path, 'r', encoding='utf-8') as f:
        # 读取 json 数据
        data = json.load(f)
        # 返回数据
        return data

# 解析 json 的内容
def parse_json(file_path):
    data = read_json(file_path)
    return data

# 程序入口
if __name__ == '__main__':
    # 读取 words 文件夹下的所有文件夹
    dirs = os.listdir('words')
    handled_results = {}
    # 遍历文件夹
    for dir in dirs:
        # .DS_Store 文件夹跳过
        if dir == '.DS_Store':
            continue
        # 读取文件夹下的所有文件
        files = os.listdir('words/' + dir)
        # 遍历文件
        for file in files:
            file_path = 'words/' + dir + '/' + file
            print(file_path)
            # 读取文件
            results = parse_json(file_path)
            index = 0
            # 遍历 results 对象
            for result in results:
                word = result
                item = results[result]
                flag = item['flag']
                count = item['count']
                print("index:",index)
                print(word)
                print(count)
                if word =='有' and count == 97:
                    print(handled_results[word])
                # 如果已经存在了
                if word in handled_results:
                    # 旧的值 + 新的值
                    handled_results[word]['count']  = handled_results[word]['count'] + count
                    print('🔴已存在')
                    print(count)
                # 如果不存在
                else:
                    print(count)
                    handled_results[word] = {
                        'flag': flag,
                        'count': count
                    }
                    print('🟢不存在')
    # 保存结果
    with open('words.json', 'w', encoding='utf-8') as f:
        json.dump(handled_results, f, ensure_ascii=False)
    print(len(results))

