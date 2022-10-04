from bs4 import BeautifulSoup
import json
import os
import re
import hanlp

# 获取 answer 的内容
def get_answer_content(path):
    # jieba 分词
    def jieba_cut(content):
        # 去除前后空格
        content = content.strip()
        if content == '':
            return []
        # 分割句子
        def split_sentence(content):
            # 根据标点符号分割成句子数组
            sentences = re.split(r'[。！？，]', content)
            # 定义段落
            paragraphs = []
            paragraph = ''
            # 遍历句子数组
            for sentence in sentences:
                paragraph += sentence+','
                if len(paragraph) > 100:
                    paragraphs.append(paragraph)
                    paragraph = ''
            if len(paragraph) > 0:
                paragraphs.append(paragraph)
            return paragraphs
        paragraphs = split_sentence(content)
        word_flags = []
        for paragraph in paragraphs:
            print(paragraph)
            HanLP = hanlp.load(hanlp.pretrained.mtl.CLOSE_TOK_POS_NER_SRL_DEP_SDP_CON_ELECTRA_SMALL_ZH) # 世界最大中文语料库
            result = HanLP([paragraph])
            words = result['tok/fine'][0]
            flags = result['pos/ctb'][0]
            if len(words) == len(flags):
                for i in range(len(words)):
                    word = words[i]
                    flag = flags[i]
                    if re.search(r'[a-zA-Z]', word):
                        continue
                    # 如果包含数字
                    if re.search(r'\d', word):
                        continue
                    if flag == 'PU':
                        continue
                    word_flags.append((word , flag))
        final_words = {}
        for item in word_flags:
            word = item[0]
            flag = item[1]
            # 如果 word 为空 则跳过
            if word == '':
                continue
            # 如果 word 已经存在 final_words 中
            if word in final_words:
                # count + 1
                final_words[word]['count'] += 1
            else:
                final_words[word] = {
                    'flag': flag,
                    'count': 1
                }
        return final_words

    all_words = {}
    answers = []
    # 读取 htmls/文化/文化.html 文件
    with open(path, 'r', encoding='utf-8') as f:
        html = f.read()
    # 解析 html
    soup = BeautifulSoup(html, 'html.parser')
    # 获取所有的 class 为 List-item TopicFeedItem 的 div
    divs = soup.find_all('div', class_='List-item TopicFeedItem')
    # 遍历
    for div in divs:
        answer = {
            'title': '',
            'url': '',
            'author': '',
            'upvoteCount': '',
            'commentCount': '',
            'content': '',
        }
        # 获取 class 为 ContentItem AnswerItem 的 div 中的 data-zop 的值
        answerItem = div.find('div', class_='ContentItem AnswerItem')
        # 如果 answerItem 为空，跳过
        if answerItem is None:
            continue
        data_zop = answerItem.get('data-zop')
        # data_zop 转换为字典
        data_zop = eval(data_zop)
        title = data_zop['title']
        author = data_zop['authorName']
        # 获取所有子节点
        children = div.findChildren()
        url = ''
        # 遍历子节点
        for child in children:
            # 如果标签为 meta 并且 itemprop 为 url
            if child.name == 'meta':
                if child.get('itemprop') == 'url':
                    # 如果 child.get('content') 包含 answer
                    if 'answer' in child.get('content'):
                        url = child.get('content')
                if child.get('itemprop') == 'upvoteCount':
                    upvoteCount = child.get('content')
                if child.get('itemprop') == 'commentCount':
                    commentCount = child.get('content')
        # 获取 class 为 RichContent-inner 的 div
        content_div = div.find('div', class_='RichContent-inner')
        # 如果 content_div 为空，跳过
        if content_div is None:
            continue
        # 获取所有的 p 标签
        ps = content_div.find_all('p')
        content = ''
        # 遍历 p 标签
        for p in ps:
            # 获取 p 标签的文本
            text = p.get_text()
            # 去除空格
            text = text.strip()
            # 去除换行符
            text = text.replace(' ', '')
            # 去除换行符
            text = text.replace('\n', '')
            content += text
        # 去除前后换行符
        content = content.strip()
        words = jieba_cut(content)
        # 遍历 words
        for word in words:
            if word in all_words:
                all_words[word]['count'] += words[word]['count']
            else:
                all_words[word] = words[word]
        # 将数据添加到 answer 中
        answer['title'] = title
        answer['url'] = url
        answer['author'] = author
        answer['upvoteCount'] = upvoteCount
        answer['commentCount'] = commentCount
        answer['content'] = content
        answers.append(answer)
    return answers, all_words

# 程序入口
if __name__ == '__main__':
    # 读取 htmls 文件夹下的所有文件夹
    dirs = os.listdir('htmls')
    # 遍历文件夹
    for dir in dirs:
        # .DS_Store 文件夹跳过
        if dir == '.DS_Store':
            continue
        print('正在读取 %s 文件夹下的文件' % dir)
        # 读取文件夹下的所有文件
        files = os.listdir('htmls/' + dir)
        # 遍历文件
        for file in files:
            # 获取文件名
            file_name = file.split('.')[0]
            # 如果 words/dir目录下 有该文件名的数据，跳过
            if os.path.exists('words/%s/%s.json' % (dir, file_name)):
                continue
            print('正在读取 %s 文件' % file)
            # 获取文件后缀
            file_suffix = file.split('.')[1]
            # 获取文件路径
            file_path = 'htmls/' + dir + '/' + file
            # 如果文件后缀不是 .html 则跳过
            if file_suffix != 'html':
                continue
            print('正在读取文件：' + file_path)
            # 判断 answers/dir 文件夹是否存在
            if not os.path.exists('answers/' + dir):
                # 不存在则创建
                os.makedirs('answers/' + dir)
            # 写入文件
            with open('answers/' + dir + '/' + file_name + '.json', 'w', encoding='utf-8') as f:
                # 获取 answer 的内容
                answers,all_words = get_answer_content(file_path)
                # 将数据转换为 json 格式
                json_data = json.dumps(answers, ensure_ascii=False, indent=4)
                # 写入文件
                f.write(json_data)
                print('写入文件：' + 'htmls/' + dir + '/' + file_name + '.json')
                # 判断 words/dir 文件夹是否存在
                if not os.path.exists('words/' + dir):
                    # 不存在则创建
                    os.makedirs('words/' + dir)
                # all_words 写入文件
                with open('words/' + dir + '/' + file_name + '.json', 'w', encoding='utf-8') as f:
                    # 将数据转换为 json 格式
                    json_data = json.dumps(all_words, ensure_ascii=False, indent=4)
                    # 写入文件
                    f.write(json_data)
                    print('写入文件：' + 'htmls/' + dir + '/' + file_name + '.json')
            # answers = get_answer_content()


