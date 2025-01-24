<?php
// 设置正确的发布密码
$correctPassword = 'root';
// 使用哈希密码验证
// $correctPasswordHash = password_hash('root', PASSWORD_DEFAULT); // 推荐用密码哈希存储

// 初始化错误消息
$errorMessage = '';

// 读取现有的博客内容
$file = 'blog.txt';
$blogEntries = [];

if (file_exists($file)) {
    $currentContent = file_get_contents($file);
    // 使用新的分隔符解析博客
    $rawEntries = explode('@', $currentContent);

    foreach ($rawEntries as $entry) {
        $entry = trim($entry); // 去掉首尾空白
        if (!empty($entry) && str_contains($entry, '￥￥￥')) {
            $blogEntries[] = "@". $entry; // 保留分隔符
        }
    }
}

// 检查是否有 POST 请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 发布新博客
    if (isset($_POST['title']) && isset($_POST['content']) && isset($_POST['password'])) {
        $title = $_POST['title'];
        $content = $_POST['content']; // 使用内容内容
        $password = $_POST['password'];
        $date = date('Y.m.d H:i:s'); // 获取当前日期

        if (empty($title)) {
            $title = '速记';
        }

        if (!empty($content)) {
            if ($password === $correctPassword) { // 如果用哈希密码，使用 password_verify($password, $correctPasswordHash)
                // 格式化博客内容
                $blogEntry = "@\n标题：$title\n时间：$date\n内容：\n$content\n￥￥￥";

                // 确保现有博客内容末尾有换行符，用于拼接新博客
                $currentContent = implode("\n", $blogEntries);
                if (!empty($currentContent) &&!str_ends_with($currentContent, "\n")) {
                    $currentContent.= "\n";
                }

                // 添加新博客
                file_put_contents($file, $currentContent. $blogEntry. "\n");
                $errorMessage = '发布成功！';
                header('Location: '. $_SERVER['PHP_SELF']. '?error='. urlencode($errorMessage));
                exit;
            } else {
                $errorMessage = '密码错误！';
                header('Location: '. $_SERVER['PHP_SELF']. '?error='. urlencode($errorMessage));
                exit;
            }
        } else {
            $errorMessage = '内容不能为空！';
            header('Location: '. $_SERVER['PHP_SELF']. '?error='. urlencode($errorMessage));
            exit;
        }
    }

    // 删除博客
    if (isset($_POST['delete_index'])) {
        $deleteIndex = $_POST['delete_index'];
        $deletePassword = $_POST['delete_password'];

        if ($deletePassword === $correctPassword) { // 使用哈希密码验证： password_verify($deletePassword, $correctPasswordHash)
            if (isset($blogEntries[$deleteIndex])) {
                unset($blogEntries[$deleteIndex]); // 删除指定博客
                $currentContent = implode("\n", $blogEntries); // 重新组合博客内容
                file_put_contents($file, $currentContent); // 保存到文件
                $errorMessage = '博客删除成功！';
                header('Location: '. $_SERVER['PHP_SELF']. '?error='. urlencode($errorMessage));
                exit;
            }
        } else {
            $errorMessage = '删除密码错误！';
            header('Location: '. $_SERVER['PHP_SELF']. '?error='. urlencode($errorMessage));
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
    
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title>
            发布博客
        </title>
        <link rel="stylesheet" href="styles.css">
        <link rel="stylesheet" href="stylespc.css" media="screen and (min-width: 800px)">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        </header>
<style>
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
input[type="text"], textarea, input[type="password"] {
    padding: 8px 12px;
    border: 2px solid #ccc;
    border-radius: 4px;
    width: 90%;
    outline: none;
    transition: border-color 0.3s ease;
    font-family: inherit; /* 继承父元素字体 */
    font-size: inherit; /* 继承父元素字号 */
    color: inherit; /* 继承父元素文字颜色 */
    font-weight: bold;
    margin-bottom: 13px; /* 保留外边距 */
}
table {
    width: 100%;
    table - layout: fixed;
    border - collapse: collapse;
}

th,
td {
    width: 50%;
    border: 1px solid #ccc;
    padding: 8px;
    text - align: center;
}
</style>
        <!-- 顶部导航栏 -->
        <header>
            <h1>
                <a href="https://egg-dan.space/" style="color: #394E6A;text-decoration: none;">
                    📅Dan's Blog
                </a>
            </h1>
        </header>
        <!-- 主体布局 -->
        <div class="layout">
            <!-- 左侧容器 -->
            <div class="left">
                <!-- 第一个容器 -->
                <div class="container">
                    
                    <div class="title">
                        📨发布博客
                    </div>
                </div>
                <!-- 第二个容器 -->
                <div class="container">

                    <div class="title">
                        🌐博客列表
                    </div>
                    <div class="content">
        <table>
            <thead>
                <tr>
                    <th>标题</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($blogEntries as $index => $entry):?>
                    <?php
                    // 解析博客内容
                    preg_match('/标题：(.+?)\n/', $entry, $titleMatch);
                    $title = isset($titleMatch[1])? $titleMatch[1] : '';
                  ?>
                    <tr>
                        <td><?php echo htmlspecialchars($title);?></td>
                        <td>
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="delete_index" value="<?php echo $index;?>">
                                <button type="button" onclick="showDeletePasswordForm(<?php echo $index;?>)">删除</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach;?>
            </tbody>
        </table>
                    </div>
                </div>
    <div class="delete - password - container" id="delete - password - container" style="display: none;">
        <h2>输入密码：</h2>
        <form action="" method="POST">
            <input type="hidden" id="delete_index" name="delete_index" value="">
            <label for="delete_password">删除密码：</label>
            <input type="password" id="delete_password" name="delete_password" required><br>
            <input type="submit" value="删除博客">
        </form>
    </div>
            </div>
            <!-- 右侧容器 -->
            <div class="right">
                <!-- 第三个容器 -->
                <div class="container">

        <form action="" method="POST">
            <label for="title">标题：</label>
            <input type="text" id="title" name="title"><br>

            <label for="content">内容：</label>
            <textarea id="content" name="content" rows="10" cols="50" required></textarea><br>
            <!-- 添加Markdown语法按钮 -->
            <div>
                <button type="button" onclick="insertMarkdownSyntax('引用', '> ')">引用</button>
                <button type="button" onclick="insertMarkdownSyntax('行内代码', '`', '`')">行内代码</button>
                <button type="button" onclick="insertMarkdownSyntax('大标题', '# ')">大标题</button>
                <button type="button" onclick="insertMarkdownSyntax('代码块', '```', '```')">代码块</button>
                <button type="button" onclick="insertMarkdownSyntax('图片引用', '![替代文本](图片链接)')">图片引用</button>
                <button type="button" onclick="insertMarkdownSyntax('链接引用', '[链接文本](链接地址)')">链接引用</button>
                <button type="button" onclick="insertMarkdownSyntax('加粗', '**', '**')">加粗</button>
                <button type="button" onclick="insertMarkdownSyntax('斜体', '*', '*')">斜体</button>
                <button type="button" onclick="insertMarkdownSyntax('分割线', '---')">分割线</button>
                <button type="button" onclick="insertMarkdownSyntax('折叠', '<details><summary>摘要</summary>\n\n内容\n\n</details>')">折叠</button>
                <button type="button" onclick="insertMarkdownSyntax('无序列表', '- ')">无序列表</button>
                <button type="button" onclick="insertMarkdownSyntax('有序列表', '1. ')">有序列表</button>
                <button type="button" onclick="insertMarkdownSyntax('表格', '| 列1 | 列2 |\n| ---- | ---- |\n| 内容1 | 内容2 |')">表格</button>
            </div>

            <label for="password">密码：</label>
            <input type="password" id="password" name="password" required><br>

            <input type="submit" value="发布博客">
        </form>
    </div>


            </div>
        </div>
        </body>
        <!-- JavaScript -->
    <script>
        // 弹窗提示
        <?php if (isset($_GET['error'])):?>
            alert('<?php echo htmlspecialchars($_GET['error']);?>');
        <?php endif;?>

        // 显示删除密码输入框
        function showDeletePasswordForm(index) {
            document.getElementById('delete - password - container').style.display = 'block';
            document.getElementById('delete_index').value = index;
        }

        // 插入Markdown语法到文本域
        function insertMarkdownSyntax(name, start, end = '') {
            const textarea = document.getElementById('content');
            const startIndex = textarea.selectionStart;
            const endIndex = textarea.selectionEnd;
            const before = textarea.value.substring(0, startIndex);
            const selected = textarea.value.substring(startIndex, endIndex);
            const after = textarea.value.substring(endIndex);
            if (name === '行内代码' && selected) {
                start = '`' + selected + '`';
                end = '';
            }
            textarea.value = before + start + end + after;
            textarea.selectionStart = before.length + start.length;
            textarea.selectionEnd = before.length + start.length;
        }
    </script>
        <!-- 百度统计代码-->
        <script>
            var _hmt = _hmt || []; (function() {
                var hm = document.createElement("script");
                hm.src = "https://hm.baidu.com/hm.js?98c311339dd7326a9534b96bd3db1764";
                var s = document.getElementsByTagName("script")[0];
                s.parentNode.insertBefore(hm, s);
            })();
        </script>

</html>