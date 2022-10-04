import jieba
import jieba.posseg as pseg
import requests
from bs4 import BeautifulSoup

def get_page(url):
    # 请求头
    headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36'
    }
    # 发送请求
    response = requests.get(url, headers=headers)
    # 判断状态码
    if response.status_code == 200:
        # 返回页面内容
        return response.text
    else:
        # 返回 None
        return None

# 获取网页
def get_html(url):
    # 抓取页面
    page = get_page(url)
    print(page)
    if page==None:
        return []

    return

# 处理分词
def handle_word_jieba(sentence):
    jieba.enable_paddle() #启动paddle模式。 0.40版之后开始支持，早期版本不支持
    words = pseg.cut(sentence) #paddle模式
    for word, flag in words:
        print('%s %s' % (word, flag))
# 程序入口
if __name__ == '__main__':
    url = 'https://www.zhihu.com/topic/19551683/top-answers'
    sentence = get_html(url)
