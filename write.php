<?php
// 设置正确的发布密码
$correctPassword = 'root';  // 请替换为您希望设置的密码

// 初始化错误消息
$errorMessage = '';

// 读取现有的博客内容
$file = 'blog.txt';
$blogEntries = [];

if (file_exists($file)) {
    $currentContent = file_get_contents($file);
    // 使用双换行符（5个 \n）作为分隔符，分割博客
    $rawEntries = explode("\n\n\n\n\n\n", $currentContent);
    
    // 遍历每个原始博客条目，检查其是否符合有效博客格式
    foreach ($rawEntries as $entry) {
        // 检查该条目是否包含有效的 "标题"、"时间" 和 "内容"
        if (preg_match('/标题：(.+?)\n/', $entry) && preg_match('/→(.+?)\n/', $entry) && preg_match('/内容：\n.+\n(.+?)(?=\n|$)/s', $entry)) {
            $blogEntries[] = $entry;
        }
    }
}

// 检查是否有 POST 请求
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 发布新博客
    if (isset($_POST['title']) && isset($_POST['content']) && isset($_POST['password'])) {
        // 获取标题、内容和密码
        $title = $_POST['title'];
        $content = $_POST['content'];
        $password = $_POST['password'];
        $date = date('Y.m.d');  // 获取当前日期

        // 检查标题、内容和密码是否为空
        if (!empty($title) && !empty($content)) {
            // 检查密码是否正确
            if ($password === $correctPassword) {
                // 格式化博客内容
                $blogEntry = "标题：$title\n内容：\n→$date\n$content\n";

                // 追加新的博客
                $currentContent = implode("\n\n\n\n\n\n", $blogEntries); // 拼接已有博客
                file_put_contents($file, $currentContent . "\n\n\n\n\n\n" . $blogEntry);  // 添加新博客并保存
                $errorMessage = '发布成功！';
                header('Location: ' . $_SERVER['PHP_SELF'] . '?error=' . urlencode($errorMessage));
                exit;
            } else {
                $errorMessage = '密码错误！';
                header('Location: ' . $_SERVER['PHP_SELF'] . '?error=' . urlencode($errorMessage));
                exit;
            }
        } else {
            $errorMessage = '标题或内容不能为空！';
            header('Location: ' . $_SERVER['PHP_SELF'] . '?error=' . urlencode($errorMessage));
            exit;
        }
    }

    // 删除博客
    if (isset($_POST['delete_index'])) {
        $deleteIndex = $_POST['delete_index'];
        $deletePassword = $_POST['delete_password'];

        // 检查密码
        if ($deletePassword === $correctPassword) {
            if (isset($blogEntries[$deleteIndex])) {
                unset($blogEntries[$deleteIndex]);  // 删除指定博客
                // 将修改后的博客列表重新保存到文件
                $currentContent = implode("\n\n\n\n\n\n", $blogEntries);  // 拼接所有博客
                file_put_contents($file, $currentContent);  // 保存到文件
                $errorMessage = '博客删除成功！';
                header('Location: ' . $_SERVER['PHP_SELF'] . '?error=' . urlencode($errorMessage));
                exit;
            }
        } else {
            $errorMessage = '删除密码错误！';
            header('Location: ' . $_SERVER['PHP_SELF'] . '?error=' . urlencode($errorMessage));
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>发布博客</title>
  <style>
/* 基础重置 */
html, body {
  margin: 0;
  padding: 10px;
  font-family: Arial, sans-serif;
}

/* 表单和博客列表容器样式 */
.form-container, .blog-list, .delete-password-container {
  width: 100%;
  max-width: 800px; /* 可以根据实际需要调整这个宽度 */
  margin: 0 auto; /* 居中显示 */
}

/* 表单样式 */
.form-container {
  margin-top: 20px;
}

.form-container h2 {
  text-align: center;
}

.form-container form {
  display: flex;
  flex-direction: column;
}

.form-container label {
  margin-top: 10px;
}

.form-container input[type="text"],
.form-container textarea,
.form-container input[type="password"] {
  padding: 10px;
  margin-top: 5px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.form-container input[type="submit"] {
  padding: 10px 20px;
  margin-top: 20px;
  background-color: #5cb85c;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.form-container input[type="submit"]:hover {
  background-color: #4cae4c;
}

/* 博客列表样式 */
.blog-list {
  margin-top: 20px;
}

.blog-list h2 {
  text-align: center;
}

/* 表格样式 */
table {
  width: 100%;
  border-collapse: collapse;
}

table, th, td {
  border: 1px solid black;
}

th, td {
  padding: 10px;
  text-align: left;
}

/* 删除密码容器样式 */
.delete-password-container {
  display: none; /* 默认不显示，通过JavaScript控制显示 */
  flex-direction: column;
  margin-top: 20px;
}

.delete-password-container h2 {
  text-align: center;
}

.delete-password-container form {
  display: flex;
  flex-direction: column;
}

.delete-password-container label {
  margin-top: 10px;
}

.delete-password-container input[type="password"] {
  padding: 10px;
  margin-top: 5px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.delete-password-container input[type="submit"] {
  padding: 10px 20px;
  margin-top: 20px;
  background-color: #d9534f;
  color: white;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.delete-password-container input[type="submit"]:hover {
  background-color: #c9302c;
}
  </style>
</head>
<body>

  <div class="form-container">
    <h2>发布新博客</h2>

    <form action="" method="POST">
      <label for="title">标题：</label>
      <input type="text" id="title" name="title" required><br>

      <label for="content">内容：</label>
      <textarea id="content" name="content" rows="10" cols="50" required></textarea><br>

      <label for="password">发布密码：</label>
      <input type="password" id="password" name="password" required><br>

      <input type="submit" value="发布博客">
    </form>
  </div>

  <div class="blog-list">
    <h2>管理博客</h2>
    <table>
      <thead>
        <tr>
          <th>标题</th>
          <th>日期</th>
          <th>操作</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($blogEntries as $index => $entry): ?>
          <?php
          // 解析博客内容
          preg_match('/标题：(.+?)\n/', $entry, $titleMatch);
          preg_match('/→(.+?)\n/', $entry, $dateMatch);
          $title = isset($titleMatch[1]) ? $titleMatch[1] : '';
          $date = isset($dateMatch[1]) ? $dateMatch[1] : '';
          ?>
          <tr>
            <td><?php echo htmlspecialchars($title); ?></td>
            <td><?php echo htmlspecialchars($date); ?></td>
            <td>
              <form action="" method="POST" style="display:inline;">
                <input type="hidden" name="delete_index" value="<?php echo $index; ?>">
                <button type="button" onclick="showDeletePasswordForm(<?php echo $index; ?>)">删除</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="delete-password-container" id="delete-password-container" style="display: none;">
    <h2>输入删除密码：</h2>
    <form action="" method="POST">
      <input type="hidden" id="delete_index" name="delete_index" value="">
      <label for="delete_password">删除密码：</label>
      <input type="password" id="delete_password" name="delete_password" required><br>
      <input type="submit" value="删除博客">
    </form>
  </div>

  <script>
    // 弹窗提示
    <?php if (isset($_GET['error'])): ?>
      alert('<?php echo htmlspecialchars($_GET['error']); ?>');
    <?php endif; ?>

    // 显示删除密码输入框
    function showDeletePasswordForm(index) {
      document.getElementById('delete-password-container').style.display = 'block';
      document.getElementById('delete_index').value = index;
    }
  </script>

</body>
</html>