import time
import requests
# 导入 bs
from bs4 import BeautifulSoup

# 获取页面内容
def get_html(url):
    r = requests.get(url)
    return r.text

# 解析页面内容
def parse_content(html):
    soup = BeautifulSoup(html, 'html.parser')
    current_title = ''
    # 获取 class = coucont-div3 的 divs
    divs = soup.find('div', class_='coucont-div3')
    # 获取 divs 里的 p
    ps = divs.find_all('p')
    # 定义一个空列表
    content = []
    # 遍历 ps
    for p in ps:
        # 判断 p 是否包含 '<'
        if '<' in p.text:
            # 去除 p 里的 html 标签
            p.text = p.text.replace('<', '')
            p.text = p.text.replace('>', '')
        # 将 p 添加到列表
        content.append(p.text)
    # 返回  next_url, next_title, content
    return {
        'current_title': current_title,
        'content': content
    }

# 解析页面内容
def parse_url(html):
    soup = BeautifulSoup(html, 'html.parser')
    # 获取多个 class = coucont-p3 的 p
    p_list = soup.find_all('p', class_='coucont-p3')
    next_url = ''
    # 遍历 divs
    for p_item in p_list:
        # 判断内容是否包含包含 '下一篇:'
        if '下一篇:' in p_item.text:
            # 获取下一篇的链接
            next_url = p_item.find('a')['href']
            # 获取下一篇的标题
            next_title = p_item.text.replace('下一篇:', '')
    # 返回  next_url, next_title, content
    return {
        'next_url': next_url,
        'next_title': next_title,
    }

# 获取所有链接
def get_all_urls(html):
    # 获取 class = coucont-div3 的 div
    div = BeautifulSoup(html, 'html.parser').find('div', class_='coucont-div3')
    # 获取 div 里的 a
    a_list = div.find_all('a')
    # 定义一个空列表
    url_titles = []
    # 遍历 a_list
    for a in a_list:
        # 将 a['href'] 添加到列表
        url= a['href']
        # 获取 a 标签里的文本
        title = a.text
        # 如果 title 不包含 '（'，则跳过
        if '最全常用日常英语口语900句学习资料，附加词汇分析' in title:
            continue
        # 将 url, title 添加到列表
        url_titles.append({
            'url': url,
            'title': title
        })
    # 返回 urls
    return url_titles

if __name__ == '__main__':
    domain = 'https://www.e2say.com'
    urls = []
    current_url = domain+'/articles/1288/'
    current_title = ''
    # 获取页面内容
    html = get_html(current_url)
    # 获取所有链接
    url_titles = get_all_urls(html)
    # # 遍历 urls
    for url_title in url_titles:
        url = url_title['url']
        title = url_title['title']
        # 获取页面内容
        html = get_html(f'{url}')
        # 解析页面内容
        data_list = parse_content(html)
        content = data_list['content']
        # 写入文件
        with open(f'{title}.txt', 'w', encoding='utf-8') as f:
            for item in content:
                f.write(item)
                f.write('\n')
        time.sleep(1)
    # print('done')
