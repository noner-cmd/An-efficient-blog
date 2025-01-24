<?php
// 引入 Parsedown
require_once 'Parsedown.php';
$Parsedown = new Parsedown();

// 读取blog.txt文件中的内容
$file = 'blog.txt';
$blogs = [];

// 检查文件是否存在
if (file_exists($file)) {
    // 获取所有博客条目
    $content = file_get_contents($file);
    // 按新分隔符分割每篇博客
    $entries = explode('@', trim($content));

    foreach ($entries as $entry) {
        $entry = trim($entry); // 去除多余空白
        if (!empty($entry) && str_contains($entry, '￥￥￥')) {
            // 去除结束符
            $entry = str_replace('￥￥￥', '', $entry);

            // 解析博客内容
            preg_match('/标题：(.*?)\n/', $entry, $titleMatch);
            preg_match('/时间：(.*?)\n/', $entry, $dateMatch);
            preg_match('/内容：\n([\s\S]*)/', $entry, $contentMatch);

            if (!empty($titleMatch) && !empty($dateMatch) && !empty($contentMatch)) {
                $blogs[] = [
                    'title' => $titleMatch[1],
                    'date' => $dateMatch[1], // 保存时间
                    'content' => $Parsedown->text($contentMatch[1]), // 转换Markdown为HTML
                ];
            }
        }
    }
    // 根据时间字段对$blogs数组进行倒序排序（适配blog.txt里的时间格式2025.01.01 16:57:38）
    usort($blogs, function ($a, $b) {
        // 使用 DateTime 对象来处理日期时间（精确到秒）
        $dateA = DateTime::createFromFormat('Y.m.d H:i:s', $a['date']);
        $dateB = DateTime::createFromFormat('Y.m.d H:i:s', $b['date']);
        return $dateB <=> $dateA; // 倒序排序
    });
} else {
    echo "没有找到博客文件。";
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dan's Blog</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://egg-dan.space/stylespc.css" media="screen and (min-width: 800px)">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
/* 全局样式重置 */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}


a {
  color: #66a7ff
  
}
/* Markdown相关样式 */
pre, code {
    font-family: 'Courier New', monospace;
    background-color: #f7f7f7;
    border-radius: 3px;
    padding: 3px;
   
    overflow-x: auto;
  /*  white-space: nowrap; 禁止文本换行，超长时通过滚动查看 */
}

/* 针对行内代码块 */
code {
    white-space: pre-wrap;
    word-wrap: break-word;
    word-break: normal;
}

blockquote {
    background-color: #f9f9f9;
    border-left: 4px solid #394E6A;
    padding-left: 15px;

    margin: 20px 0;
    width: 100%;
    overflow-x: auto; /* 让引用内容超长可滚动 */
    white-space: nowrap;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    overflow-x: auto; /* 表格内容超长可滚动 */
    display: block;
    white-space: nowrap;
}

th, td {
    border: 1px solid #ddd;
    padding: 10px;
    text-align: left;
    white-space: nowrap;
}

th {
    background-color: #f0f0f0;
}

hr {
    border: 0;
    border-top: 2px solid #394E6A;
    margin: 20px 0;
}

/* 搜索框容器样式 */
.search-container {
    display: flex;
    align-items: center;
    margin-bottom: 13px;
}

/* 搜索输入框样式 */
.search-input {
    padding: 8px 12px;
    border: 2px solid #ccc;
    border-radius: 4px;
    width: 100%;
    outline: none;
    transition: border-color 0.3s ease;
    font-family: inherit; /* 继承父元素字体 */
    font-size: inherit; /* 继承父元素字号 */
    color: inherit; /* 继承父元素文字颜色 */
    font-weight: bold;
}

.search-input:focus {
    border-color: #394E6A;
    font-weight: bold;
}

/* 放大镜图标样式 */
.search-container i {
    margin-left: 10px;
    font-size: 20px;
    color: #888;
}

/* 博客标题、日期等通用样式 */
.blog-container.title,
.blog-container.date {
    width: 100%;
    overflow-x: auto;
    white-space: nowrap;
}

/* 博客内容区域样式 */
.blog-container.content {
    width: 100%;
    overflow-x: auto;
    white-space: nowrap;
}
ol li {
    margin-left: 15px; /* 设置列表项距离左边15px */
}
ul li {
    margin-left: 15px; /* 设置列表项距离左边10px */
}
    </style>
</head>

<body>
<!-- 顶部导航栏 -->
<header>
    <h1>
        <a href="https://egg-dan.space/" style="color: #394E6A; text-decoration: none;">📅Dan's Blog
        </a>
    </h1>
</header>

<!-- 主体布局 -->
<div class="layout">
    <div class="left">
        <div class="container">
            <div class="title">Dan的</div>
            <div class="title">🗂博客</div>
        </div>
        <div class="container">
            <img src="blog.webp" alt="关于我" style="width: 100%; border-radius: 10px;">
            <div class="title">🌐About</div>
            <div class="content">
                <p>记录了我的各种开发经历和所思所想✍️</p>
                <p>希望我的探索和经验也能对你的开发产生一些启发🤠</p>
            </div>
        </div>
        <div class="container">
            <div class="content footer-content">
                <p>🎬网站已安全运行了 <span id="running-days"></span> 天</p>
            </div>
        </div>
    </div>

    <!-- 右侧容器 -->
    <div class="right">
        <!-- 添加搜索框容器 -->
        <div class="container search-container">
            <input type="text" id="search-input" placeholder="输入关键词搜索博客" class="search-input">
            <i class="fas fa-search"></i>
        </div>
        <?php foreach ($blogs as $blog):?>
            <div class="container blog-container">
                <div class="title"><?php echo "📄". htmlspecialchars($blog['title']);?></div>
                <div class="date"><?php echo "📅 ：". htmlspecialchars($blog['date']);?></div>
                <div class="content">
                    <?php echo $blog['content']; // 输出转换后的Markdown内容?>
                </div>
            </div>
        <?php endforeach;?>
    </div>
</div>

<!-- JavaScript -->
<script>
    // 计算网站已运行天数的函数
    function calculateRunningDays() {
        const startDate = new Date('2024-10-16');
        const currentDate = new Date();
        const differenceInTime = currentDate - startDate;
        const differenceInDays = Math.floor(differenceInTime / (1000 * 60 * 60 * 24));
        document.getElementById('running-days').textContent = differenceInDays;
    }

    // 搜索功能相关函数
    function searchBlogs() {
        const searchInput = document.getElementById('search-input').value.toLowerCase();
        const blogContainers = document.querySelectorAll('.blog-container');

        blogContainers.forEach((container) => {
            const title = container.querySelector('.title').textContent.toLowerCase();
            const content = container.querySelector('.content').textContent.toLowerCase();
            if (title.includes(searchInput) || content.includes(searchInput)) {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        });
    }

    // 在页面加载完成后显示所有博客容器
    window.onload = function () {
        const blogContainers = document.querySelectorAll('.blog-container');
        blogContainers.forEach((container) => {
            container.style.display = 'block';
        });
        calculateRunningDays();
    };

    document.getElementById('search-input').addEventListener('input', searchBlogs);
</script>
</body>
</html>