import requests
# 导入 bs4 库
from bs4 import BeautifulSoup

# 抓取页面
def get_page(url):
    # 发送请求
    response = requests.get(url)
    # 获取页面内容
    page = response.text
    # 返回页面内容
    return page

# 解析页面
def main():
    # 抓取页面
    page = get_page('https://www.imooc.com/article/283507')
    # 解析页面
    parse_page(page)

def parse_page(page):
    # 创建 bs4 对象
    soup = BeautifulSoup(page, 'html.parser')
    # 获取 class 为 m-detail--body 的 div 标签
    div = soup.find('div', class_='m-detail--body')
    # 获取 div 标签中的所有 ul 标签
    uls = div.find_all('ul')
    # 遍历 uls
    for ul in uls:
        # 获取 ul 标签中的所有 li 标签
        lis = ul.find_all('li')
        # 遍历 lis
        for li in lis:
            # 获取 li 标签中的所有文本
            text = li.text
            # 打印文本
            print(text)
# 程序入口
if __name__ == '__main__':
    # 抓取页面
    page = get_page('https://parade.com/940979/kelseypelzer/best-dad-jokes/')
    # 解析页面
    parse_page(page)
