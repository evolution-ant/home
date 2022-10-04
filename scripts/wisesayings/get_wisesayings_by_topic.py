import json
# 爬取 html
import requests
# 导入 bs4 库
from bs4 import BeautifulSoup
import requests_cache


# 定义 base_url
base_url = 'https://www.wisesayings.com'

# 抓取页面
def get_page(url):
    session = requests_cache.CachedSession("wisesayings_cache")
    response = session.get(url)
    # 发送请求
    # response = requests.get(url)
    # 获取页面内容
    page = response.text
    # 判断 response 的状态码
    if response.status_code == 200:
        # 返回页面内容
        return page
    else:
        # 返回 None
        return None

# 获取 quote 链接
def get_quote_page(url):
    # 抓取页面
    page = get_page(url)
    if page==None:
        return []
    # 创建 bs4 对象
    soup = BeautifulSoup(page, 'html.parser')
    # 获取 class 为 letter-list 的 div 标签
    div = soup.find('div', class_='letter-list')
    # 获取 div 标签中的所有 a 标签中的 href
    hrefs = div.find_all('a', href=True)
    links = []
    # 遍历 hrefs
    for href in hrefs:
        # 获取 href 中的内容
        href = href['href']
        # 拼接 url
        link = base_url + href
        # 添加到 links 中
        links.append(link)
    # 返回 links
    return links

# 获取 quote 内容
def get_quote_content(url):
    # 去除 https://www.wisesayings.com/ 和 / 的内容
    quotes = []
    # 抓取页面
    page = get_page(url)
    if page == None:
        return []
    # 创建 bs4 对象
    soup = BeautifulSoup(page, 'html.parser')
    # 获取 class 为 quote-content 的 div 标签
    div = soup.find('div', class_='content')
    # 获取 content 中的所有 blockquote 标签
    blockquotes = div.find_all('blockquote')
    # 遍历 blockquotes
    for blockquote in blockquotes:
        # 获取 blockquote 中的 q 标签
        q = blockquote.find('q')
        # 获取 blockquote 中的 cite 标签
        cite = blockquote.find('cite')
        # 获取 p 标签中的内容
        quote = {
            'quote': q.text,
            'author': cite.text
        }
        # 添加到 quotes 中
        quotes.append(quote)
    return quotes

# 程序入口
if __name__ == '__main__':
    # 定义数组 a-b
    letters = [chr(i) for i in range(ord('a'), ord('z'))]
    # 遍历数组
    for letter in letters:
        # https://www.wisesayings.com/quote-topics/a/
        # 拼接 url
        url = base_url+'/quote-topics/' + letter + '/'
        # 获取 quote 页面
        links = get_quote_page(url)
        quotes_objs = []
        # 遍历 links
        for link in links:
            quotes_obj = {}
            # 获取 quote 内容
            topic = link.replace('https://www.wisesayings.com/', '').replace('/', '').replace('-quotes', '')
            if topic in ['cliché','forever','mother-in-law','mother’s-day']:
                continue
            print(topic)
            quotes_obj['topic'] = topic
            quotes = get_quote_content(link)
            quotes_obj['quotes'] = quotes
            quotes_objs.append(quotes_obj)
        # 写入 json 文件
        with open('quotes/' + letter + '.json', 'w') as f:
            json_str = json.dumps(quotes_objs, indent=4, ensure_ascii=False)
            f.write(json_str)
