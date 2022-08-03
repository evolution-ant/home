import os
import re
import json

# 入口
if __name__ == '__main__':
    # 读取所有文件
    files = os.listdir('./file')
    number_contents=[]
    # 遍历文件
    for file in files:
        # 如果文件名不是 txt 文件，则跳过
        if not file.endswith('.txt'):
            continue
        # 读取文件内容
        with open(f'./file/{file}', 'r', encoding='utf-8') as f:
            # 按行读取文件内容
            lines = f.readlines()
            # 遍历行
            for line in lines:
                # 去除前后空格
                line = line.strip()
                # 如果包含.则跳过
                if '.' not in line:
                    continue
                # 用 . 分割字符串
                line_list = line.split('.')
                # 如果列表长度不是 2，则跳过
                if len(line_list) < 2:
                    continue
                # 获取第一个元素
                number = line_list[0]
                # 如果 number 不是数字，则跳过
                if not number.isdigit():
                    continue
                content = ''.join(line_list[1:])
                # # 截取连续的英文和数字
                # # content = re.sub(r'[^a-zA-Z0-9]', '', content)
                # en_content = re.sub(r'[^a-zA-Z0-9\s,.?’\'!\/-]', '', content)
                # # 去除前后?.
                # en_content = en_content.strip('?.! ')
                # # en_content = 替换掉非英文字符
                # zh_content = content.replace(en_content, "",-1)
                # zh_content = zh_content.strip()

                # 遍历 content 列表，并获取当前索引
                index = 0
                for content_item in content:
                    # 正则判断是否是中文
                    if re.match(r'[\u4e00-\u9fa5]', content_item):
                        # 如果是中文，则跳出
                        break
                    index = index + 1
                # 根据索引获取中文内容
                zh_content = content[index:]
                # 根据索引获取英文内容
                en_content = content[:index]
                number_contents.append(
                    {
                        'number': int(number),
                        'zh_content': zh_content,
                        'en_content': en_content
                    }
                )
    # 遍历 number_contents 去重 number
    number_contents_new = []
    for number_content in number_contents:
        if number_content['en_content'] not in [i['en_content'] for i in number_contents_new]:
            number_contents_new.append(number_content)
    # number_contents 按 number 排序
    number_contents_new.sort(key=lambda x: x['number'])
    # 写入 json
    with open('number_contents.json', 'w', encoding='utf-8') as f:
        json.dump(number_contents_new, f, ensure_ascii=False, indent=4)
