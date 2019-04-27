<?php
$index = file_get_contents("indexed.json");
$notebooks = json_decode($index, true)["notebooks"];
$kv_map = json_decode($index, true)["kvs"];

$title = "CiyaZ的笔记系统";
$bread = "主页";
if (isset($_GET["id"]) && !empty($_GET["id"])) {
	if (array_key_exists($_GET["id"], $kv_map)) {
		$note_path = $kv_map[$_GET["id"]];
		$note_path_arr = explode("/", $note_path);
		$title = end($note_path_arr);
		array_shift($note_path_arr);
		$bread = implode(" > ", $note_path_arr);
	} else {
		http_response_code(404);
		$title = "404";
		$bread = "找不到该页面";
	}
}
echo <<<HTML
<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>{$title}</title>
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
        <ul style="text-align: left; padding: 35px" id="tree-view" class="si-list si-bg-white-lighten-2">
HTML;
foreach ($notebooks as $notebook) {
	echo "<li class=\"collapsible\">";
	echo "<span>" . $notebook["name"] . "</span>";
	echo "</li>";
	echo "<li style=\"display: none\">";
	echo "<ul class=\"si-list\" style=\"background-color: #E9F1DF\">";
	foreach ($notebook["categories"] as $category) {
		echo "<li class=\"collapsible\">";
		echo "<span>" . $category["name"] . "</span>";
		echo "</li>";
		echo "<li style=\"display: none\">";
		echo "<ul class=\"si-list si-bg-white\">";
		foreach ($category["posts"] as $post) {
			echo "<li>";
			echo "<span><a href=\"index.php?id=" . $post["key"] . "\">" . $post["name"] . "</a></span>";
			echo "</li>";
		}
		echo "</ul>";
		echo "</li>";
	}
	echo "</ul>";
	echo "</li>";
}
echo <<<HTML
        </ul>
        <p>
            <button class="si-btn si-bg-light-green si-fg-white" onclick="toggle_modal()">close</button>
        </p>
    </div>
</div>
<div class="si-header si-bg-white-lighten-2">
    <span style="font-size: 31px; margin-left: 10px;float: left;" class="si-fg-green">
        CiyaZ的笔记系统
    </span>
</div>
<div class="si-container">
    <a href="index.php" class="si-btn si-fg-green" style="position: fixed; top: 70px;right: 0;z-index: 1;background-color: #E9F1DF;">主页</a>
    <button class="si-btn si-fg-green" onclick="toggle_modal()" style="position: fixed; top: 120px;right: 0;z-index: 1;background-color: #E9F1DF;">目录</button>
    <div class="si-panel si-bg-white-lighten-2" style="margin-top: 45px">
        {$bread}
    </div>
    <div class="si-panel si-bg-white-lighten-2" style="margin-top: 5px">
HTML;

require "vendor/autoload.php";
if (isset($_GET["id"]) && !empty($_GET["id"])) {
	$post_rendered = render_post($kv_map, $_GET["id"]);
	if ($post_rendered != null) {
		echo $post_rendered;
	} else {
		echo "<div style=\"text-align: center\"><h1>404</h1></div>";
	}
} else {
	echo render_index();
}

function render_index()
{
	$md = file_get_contents("doc/README.md");
	$parser = new Parsedown();
	return $parser->text($md);
}

function render_post($kv_map, $hash_key)
{
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

echo <<<HTML
    </div>
    <div class="si-panel si-bg-white-lighten-2" style="margin-top: 5px; text-align: center;">
        <p class="si-fg-gray" style="font-size: 12px">
            Copyright © 2017-2019 CiyaZ All Rights Reserved.
        </p>
        <p class="si-fg-gray" style="font-size: 12px">
            Powered By <a href="https://github.com/CiyaZ/note2blog-ssr" style="text-decoration: none">note2blog-ssr</a>
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
<style>
code {
	font-family: "source code variable","consolas","menlo","monaco","courier new",monospace;
}
body {
	font-family: "pingfang sc", "helvetica neue", "hiragino sans gb", "segoe ui", "microsoft yahei ui", 微软雅黑, sans-serif;
}
</style>
</body>
</html>
HTML;
