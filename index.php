<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>CiyaZ的笔记系统</title>
    <link href="node_modules/@ciyaz/silicon-ui/dist/silicon-ui.css" type="text/css" rel="stylesheet"/>
    <link href="node_modules/highlightjs/styles/github.css" type="text/css" rel="stylesheet"/>
    <link href="app/app.min.css" type="text/css" rel="stylesheet"/>
    <script src="node_modules/highlightjs/highlight.pack.min.js"></script>
    <script src="app/app.min.js"></script>
</head>
<body class="si-bg-light-green">
<div id="modal" style="visibility: hidden">
    <div class="modal-overlay">
    </div>
    <div class="modal-data" style="background-color: #E9F1DF">
        <p>
            <button class="si-btn si-bg-light-green si-fg-white" onclick="toggle_modal()">close</button>
        </p>
        <ul style="text-align: left; padding: 35px" id="tree-view" class="si-list si-bg-white">
            <?php
                $index = json_decode(file_get_contents("indexed.json"), true)["notebooks"];
                foreach ($index as $notebook) {
                    echo "<li class=\"collapsible\">";
                    echo "<span>".$notebook["name"]."</span>";
                    echo "</li>";
                    echo "<li style=\"display: none\">";
                    echo "<ul class=\"si-list\" style=\"background-color: #E9F1DF\">";
                    foreach ($notebook["categories"] as $category) {
                        echo "<li class=\"collapsible\">";
                        echo "<span>".$category["name"]."</span>";
                        echo "</li>";
                        echo "<li style=\"display: none\">";
                        echo "<ul class=\"si-list si-bg-white\">";
                        foreach ($category["posts"] as $post) {
                            echo "<li>";
                            echo "<span><a href=\"index.php?id=".$post["key"]."\">".$post["name"]."</a></span>";
                            echo "</li>";
                        }
                        echo "</ul>";
                        echo "</li>";
                    }
                    echo "</ul>";
                    echo "</li>";
                }
            ?>
        </ul>
        <p>
            <button class="si-btn si-bg-light-green si-fg-white" onclick="toggle_modal()">close</button>
        </p>
    </div>
</div>
<div class="si-header si-bg-white">
    <span style="font-size: 31px; margin-left: 10px;float: left;" class="si-fg-green">
        CiyaZ的笔记系统
    </span>
</div>
<div class="container">
    <div class="si-panel si-bg-white" style="margin-top: 45px; text-align: right">
        <span>
            <a href="index.php" class="si-btn si-fg-white si-bg-light-green">主页</a>
        </span>
        <span>
            <button class="si-btn si-fg-white si-bg-light-green" onclick="toggle_modal()"
                    style="margin-right: 4px;">目录</button>
        </span>
    </div>
    <div class="si-panel si-bg-white" style="margin-top: 5px">
        <?php

        require "vendor/autoload.php";
        if (isset($_GET["id"]) && !empty($_GET["id"])) {
            echo render_post($_GET["id"]);
        } else {
            echo render_index();
        }

        function render_index()
        {
            $md = file_get_contents("doc/README.md");
            $parser = new Parsedown();
            return $parser->text($md);
        }

        function render_post($hash_key)
        {
            $kv_map = json_decode(file_get_contents("indexed.json"), true)["kvs"];
            if (array_key_exists($hash_key, $kv_map)) {
                $note_path = $kv_map[$hash_key];
                $note_md_path = get_md_path($note_path);
                $md = file_get_contents($note_md_path);
                $md = preg_replace("/\!\[\]\((.*)\)/", '![](' . $note_path . '/${1})', $md);
                $parser = new Parsedown();
                return $parser->text($md);
            } else {
                return null;
            }
        }

        function get_md_path($note_path)
        {
            $note_path_arr = explode("/", $note_path);
            return $note_path . "/" . end($note_path_arr) . ".md";
        }

        ?>
    </div>
    <div class="si-panel si-bg-white" style="margin-top: 5px; text-align: center;">
        <p class="si-fg-gray" style="font-size: 12px">
            Copyright © 2017-2019 CiyaZ All Rights Reserved.
        </p>
    </div>
</div>
<script>
    // Highlight.js初始化
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('pre code').forEach((block) => {
            hljs.highlightBlock(block);
        });
    });
</script>
</body>
</html>
