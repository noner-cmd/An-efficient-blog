<?php
// 读取 blog.txt 文件中的内容
$file = 'blog.txt';
$blogs = [];

// 检查文件是否存在
if (file_exists($file)) {
    // 获取所有博客条目
    $content = file_get_contents($file);
    // 按双换行分割每篇博客
    $entries = explode("\n\n\n\n\n\n\n", trim($content));
    
    foreach ($entries as $entry) {
        // 按标题和内容分割（假设标题以“标题：”开始）
        $parts = explode("\n", $entry);
        if (count($parts) >= 2) {
            $title = str_replace("标题：", "", $parts[0]);  // 获取标题
            $date = trim(str_replace("→", "", $parts[1]));  // 获取日期
            $content = implode("\n", array_slice($parts, 2));  // 获取内容
            $blogs[] = ['title' => $title, 'date' => $date, 'content' => $content];
        }
    }
} else {
    echo "没有找到博客文件。";
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Dan's Blog</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="stylespc.css" media="screen and (min-width: 800px)">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
  <!-- 顶部导航栏 -->
  <header>
      <div id="hamburger" onclick="toggleSidebar()">
          <i class="fas fa-bars"></i>
      </div>
      <h1>
          <a href="https://egg-dan.space/" style="color: #394E6A; text-decoration: none;">
              📄Dan's Blog
          </a>
      </h1>
      <div id="weather">
          <i class="fa-solid fa-earth-americas"></i>
      </div>
  </header>

  <!-- 侧边栏 -->
  <div id="sidebar">
      <a href="https://egg-dan.space/">主页</a>
      <a href="https://egg-dan.space/api/api.html">API工程</a>
      <a href="https://egg-dan.space/scj/scj.html">收藏夹</a>
      <a href="https://egg-dan.space/blog/blog.php">博客</a>
      <a href="https://egg-dan.space/business/business.html">商业与赞助</a>
  </div>

  <!-- 主体布局 -->
  <div class="layout">
    <div class="left">
      <div class="container">
        <div class="title">Dan的</div>
        <div class="title">🗂博客</div>
      </div>
      <div class="container">
        <img src="blog.jpg" alt="关于我" style="width: 100%; border-radius: 10px;">
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
      <!-- 显示每篇博客 -->
      <?php foreach ($blogs as $blog): ?>
        <div class="container">
            <div class="title"><?php echo "📄" . $blog['title']; ?></div>
            <div class="content">
                <p><?php echo nl2br($blog['content']); ?></p>
            </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- JavaScript -->
  <script>
      function toggleSidebar() {
          const sidebar = document.getElementById("sidebar");
          const hamburger = document.getElementById("hamburger");

          sidebar.classList.toggle("open");

          if (sidebar.classList.contains("open")) {
              hamburger.innerHTML = '<i class="fas fa-times"></i>';
          } else {
              hamburger.innerHTML = '<i class="fas fa-bars"></i>';
          }
      }

      function calculateRunningDays() {
          const startDate = new Date('2024-10-16');
          const currentDate = new Date();
          const differenceInTime = currentDate - startDate;
          const differenceInDays = Math.floor(differenceInTime / (1000 * 60 * 60 * 24));
          document.getElementById('running-days').textContent = differenceInDays;
      }

      calculateRunningDays();

</body>
</html>