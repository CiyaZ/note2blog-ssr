# Note2Blog SSR Loader

Load markdown notes as static web pages.

![https://gitee.com/ciyaz/imgbed/raw/master/images/1.png](https://gitee.com/ciyaz/imgbed/raw/master/images/1.png)

这个工具的用途是把markdown笔记加载成博客，功能类似`hexo`，需要部署在你自己的云服务器中。

本仓库是`note2blog`的后端渲染版本，这里使用的是PHP进行加载，同时移除了原版本中庞大的字体、JQuery和CSS样式库，针对低速网络进行了针对性优化。

## 和Note2Blog Loader的区别

|                  |   note2blog    | note2blog-ssr |
| :--------------: | :------------: | :-----------: |
| 可部署到GitPages |       √        |       ×       |
|   搜索引擎友好   |       ×        |       √       |
|    移动端支持    |       √        |       √       |
|   网络传输压力   |      较大      |     较小      |
|  服务端性能压力  |      很小      | IO、CPU都较大 |
|  客户端性能压力  | 渲染性能压力大 |     一般      |

## 目录结构

```
|_app 页面样式和脚本
|_doc 笔记文档目录结构，默认其中是一个例子，删掉即可
|_DOMAIN 这里写上您网站的域名，该配置仅用于用于站点地图URL的生成
|_indexer-config.json 要索引的笔记本配置
|_indexed.json 生成的索引
|_indexer.py 索引和站点地图生成器脚本
|_index.php 服务端脚本
|_sitemap.xml 自动生成的站点地图
```

## 文档结构

笔记需要按照指定的文档结构编写，我使用的是Atom进行编辑，因为它能很方便的结合Git进行版本控制，具有终端插件，Minimap插件，支持文件导航，有文件图标插件，支持Markdown语法，支持Markdown预览。

```
doc
|_笔记本1
	|_目录1
		|_笔记1
			|_res
			|_笔记1.md
	|_目录2
|_笔记本2
```

## 定制化

样式依赖于[silicon-ui](https://github.com/CiyaZ/silicon-ui)，它还很不成熟，但是体积很小，加载速度很快。

页面样式主要定制以下三个文件：

```
index.php 主页，包括底部Copyright等信息
app/app.css 一些样式，颜色，字体等
```

## 使用指南

### 添加笔记本

创建「笔记本」文件夹后，需要在`indexer-config.json`中配置其信息，指示索引器读取其内容。

### Markdown写作

所有笔记文档要按照目录结构约定编写，图片链接写作`![](res/x.png)`的形式，这样在编辑器中也能预览，发布后后端加载时会自动转换。

### 多终端同步

笔记最好使用Git进行同步，部署一个远程仓库和数个镜像库，实现在多个终端协同工作，同时确保这些珍贵的数据足够安全。

### 发布

每次发布前，执行`python3 indexer.py`，刷新索引文件和站点地图。索引文件能指示服务端脚本正确响应用户请求，站点地图用于指示搜索引擎抓取页面。

## 部署指南

1. 在服务器装好Nginx（或Apache）和PHP7并配置
2. 下载本仓库代码，分别执行`npm`和`composer`的安装命令下载依赖库
3. 按照你的需求修改源码
4. 配置`httpd`的目录指向为根目录
5. 在`doc`文件夹中用Git拉取你的笔记结构

> 注：线上版本修改了一些样式，效果图仅供参考：[https://gitee.com/ciyaz/note2blog-ssr-fork](https://gitee.com/ciyaz/note2blog-ssr-fork)