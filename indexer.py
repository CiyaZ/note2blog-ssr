#!/usr/bin/env python3

import os
import time
import json
import hashlib
import xml.dom.minidom as minidom


def md5hex(s):
    s = s.encode("utf-8")
    md5 = hashlib.md5()
    md5.update(s)
    return md5.hexdigest()


if __name__ == "__main__":
    # 索引文件生成
    fp = open("indexer-config.json", "r", encoding="UTF-8")
    indexer_config = json.load(fp)
    fp.close()

    indexer_root = {"notebooks": [], "kvs": {}}
    for notebook_name in indexer_config["indexed_categories"]:
        notebook = {"name": notebook_name, "categories": []}
        categories = os.listdir("doc/" + notebook_name)
        categories.sort()
        for category_name in categories:
            category = {"name": category_name, "posts": []}
            posts = os.listdir("doc/" + notebook_name + "/" + category_name)
            posts.sort()
            for post_name in posts:
                post_dir = "doc/" + notebook_name + "/" + category_name + "/" + post_name
                post_key = md5hex(post_dir)
                post = {"name": post_name, "key": post_key}
                indexer_root["kvs"][post_key] = post_dir
                category["posts"].append(post)
            notebook["categories"].append(category)
        indexer_root["notebooks"].append(notebook)

    indexer_out = json.dumps(indexer_root, ensure_ascii=False)
    fp = open("indexed.json", "w", encoding="UTF-8")
    fp.write(indexer_out)
    fp.close()

    # sitemap生成
    fp = open("DOMAIN", "r", encoding="UTF-8")
    domain = fp.readline()
    fp.close()
    sitemap_doc = minidom.Document()
    root = sitemap_doc.createElement("urlset")
    root.setAttribute("xmlns", "http://www.sitemaps.org/schemas/sitemap/0.9")
    for key in indexer_root["kvs"]:
        node_url = sitemap_doc.createElement("url")
        # 网址
        node_loc = sitemap_doc.createElement("loc")
        node_loc.appendChild(sitemap_doc.createTextNode("https://" + domain + "/index.php?id=" + key))
        # 最后修改时间
        file_path = ""
        file_path_folder = indexer_root["kvs"][key]
        file_path_folder_files = os.listdir(file_path_folder)
        for file_path_folder_file in file_path_folder_files:
            if file_path_folder_file.find(".md") != -1:
                file_path = file_path_folder + "/" + file_path_folder_file
                break
        file_lastmod_time = time.localtime(os.stat(file_path).st_mtime)
        file_lastmod_time_str = time.strftime("%Y-%m-%d", file_lastmod_time)
        node_lastmod = sitemap_doc.createElement("lastmod")
        node_lastmod.appendChild(sitemap_doc.createTextNode(file_lastmod_time_str))
        node_url.appendChild(node_loc)
        node_url.appendChild(node_lastmod)
        root.appendChild(node_url)
    sitemap_doc.appendChild(root)
    fp = open("sitemap.xml", "w", encoding="UTF-8")
    sitemap_doc.writexml(fp, encoding="utf-8")
    fp.close()
