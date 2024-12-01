<?php
// 读取 blog.txt 文件内容
$file = 'blog.txt';

// 检查文件是否存在
if (file_exists($file)) {
    // 获取文件内容
    $blogs = file_get_contents($file);

    // 返回内容给前端
    echo nl2br($blogs);  // nl2br 用于在换行符处插入 <br> 标签
} else {
    echo "没有找到博客内容";
}
?>