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
    # 返回页面内容
    return page

# 获取 quote 链接
def get_quote_page(url):
    # 抓取页面
    page = get_page(url)
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
    # 创建 bs4 对象
    soup = BeautifulSoup(page, 'html.parser')
    # 获取 class 为 quote-content 的 div 标签
    div = soup.find('div', class_='content')
    # 获取 div 标签中的所有 p 标签
    ps = div.find_all('q')
    # 遍历 ps
    for p in ps:
        # 获取 p 标签中的所有文本
        text = p.text
        # 添加到 quote 中
        quotes.append(text)
    # 返回 quotes
    return quotes

# 获取 page 链接
def get_page_links(url):
    # 抓取页面
    page = get_page(url)
    # 创建 bs4 对象
    soup = BeautifulSoup(page, 'html.parser')
    # 获取 class 为 letter-list 的 div 标签
    ul = soup.find('ul', class_='pagination-list')
    # 如果 ul 为空，则返回空列表
    if ul is None:
        return [url]
    # 获取 div 标签中的所有 a 标签中的 href
    hrefs = ul.find_all('a', href=True)
    links = []
    # 遍历 hrefs
    for href in hrefs:
        # 获取 href 中的内容
        href = href['href']
        # 拼接 url
        link = base_url + href
        # 添加到 links 中
        links.append(link)
    # 去重
    links = list(set(links))
    # 返回 links
    return links

# 程序入口
if __name__ == '__main__':
    # 定义数组 e-z
    letters = [chr(i) for i in range(ord('e'), ord('z'))]
    # 遍历数组
    for letter in letters:
        # https://www.wisesayings.com/quote-authors/a/
        # 拼接 url
        url = base_url+'/quote-authors/' + letter + '/'
        # 获取 quote 页面
        links = get_quote_page(url)
        quotes_objs = []
        # 遍历 links
        for link in links:
            quotes_obj = {}
            # 获取 quote 内容
            author = link.replace('https://www.wisesayings.com/', '').replace('/', '').replace('-quotes', '').replace('authors', '')
            print('link:',link)
            print('author:',author)
            quotes_obj['author'] = author
            page_urls = get_page_links(link)
            print('pages:',len(page_urls))
            # 遍历 page_urls
            quotes = []
            for page_url in page_urls:
                # 获取 quote 内容
                quote = get_quote_content(page_url)
                # 添加到 quotes 中
                quotes.extend(quote)
            # 去重
            quotes = list(set(quotes))
            print("quotes:",len(quotes))
            quotes_obj['quotes'] = quotes
            quotes_objs.append(quotes_obj)
        # 写入 json 文件
        with open('author/' + letter + '.json', 'w') as f:
            json_str = json.dumps(quotes_objs, indent=4, ensure_ascii=False)
            f.write(json_str)
