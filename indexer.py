#!/usr/bin/env python3

import os
import json
import hashlib


def md5hex(s):
    s = s.encode("utf-8")
    md5 = hashlib.md5()
    md5.update(s)
    return md5.hexdigest()


if __name__ == "__main__":
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
