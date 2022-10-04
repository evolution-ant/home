<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Document</title>
    <script src="https://cdn.jsdelivr.net/npm/mind-elixir/dist/MindElixir.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/mind-elixir/dist/painter.js"></script>

    <style>
        #map {
            margin-top: 0px;
            height: 950px;
            width: 100%;
        }
    </style>
</head>

<body>
    <div align="middle"><a href="/admin/mindmaps"><b>Back</b></a></div>
    <div id="map"></div>
</body>

<script>
    let url = window.location.href
    let id = 0
    // 如果 url 中包含 create
    if (url.includes('?')) {
        id = url.split('?')[1].split('=')[1]
    }
    console.log(id)
    // 页面加载之前调用接口获取数据
    window.onload = function() {
        console.log(id)
        // 请求接口数据
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'http://localhost:8001/api/mindmap/query?id=' + id);
        xhr.send();
        xhr.onreadystatechange = function() {
            if (xhr.status == 200) {
                if (xhr.responseText != '') {
                    // 获取数据
                    var res = JSON.parse(xhr.responseText);
                    var data = res['data']
                    var json_data = JSON.parse(data)
                    init_data = json_data
                    let mind = new MindElixir({
                        el: '#map',
                        direction: MindElixir.SIDE,
                        draggable: true, // default true
                        contextMenu: true, // default true
                        toolBar: true, // default true
                        nodeMenu: true, // default true
                        keypress: true, // default true
                    })
                    mind.init(init_data)
                    mind.bus.addListener('operation', operation => {
                        if (operation.name === 'finishEdit') {
                            save(mind, id)
                        }
                    })
                }
            }
        }
    }
    // 定义保存数据函数
    function save(mind, id) {
        var js_content = mind.getAllData();
        var md_content = mind.getAllDataMd();
        url = 'http://localhost:8001/api/mindmap/create'
        data = {
            js_content: JSON.stringify(js_content),
            md_content: md_content,
        }
        if (id != 0) {
            url = 'http://localhost:8001/api/mindmap/update'
            data['id'] = id
        }
        // 发送 post 请求
        var xhr = new XMLHttpRequest();
        xhr.open('POST', url);
        xhr.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
        xhr.send(JSON.stringify(data));
        xhr.onreadystatechange = function() {
            console.log(xhr.status)
            if (xhr.status == 200) {
                if (xhr.responseText != "") {
                    // 获取数据
                    var res = JSON.parse(xhr.responseText);
                    var data = res['data']
                    console.log(data)
                }
            } else {
                alert('保存失败')
            }
        }
    }
</script>

</html>