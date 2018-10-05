<?php
    include('../config.php');
    session_start();
    if(empty($_SESSION['session_state']))
    {
        header('Location:/admin/login.php');
    }    
    $connection=mysqli_connect(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
    $query=mysqli_query($connection,"SELECT * FROM `users` WHERE email='{$_SESSION['session_state']}'");
    $row=mysqli_fetch_assoc($query);
    if($_SERVER['REQUEST_METHOD']==='GET'){
        if(empty($_GET['p'])){
            $page=1;
        }else{
            $page=(int)$_GET['p'];
        }
        $limit_first_p=($page-1)*6;
        
        $count=mysqli_query($connection,"SELECT * FROM `post` ")->num_rows;
        $query=mysqli_query($connection," select   (@i:=@i+1)   as   i,`post`.*  from  `post`,(select   @i:=0)  as it limit $limit_first_p,6");       
    }
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>主页</title>
    <link rel="stylesheet" href="/static/assets/venders/bootstrap-4.0.0/dist/css/bootstrap.css">
    <script src='/static/assets/venders/jquery3.0/jquery-3.0.0.min.js'></script>
</head>

<body>
    <div class="nav ">
        <div class="avatar">
            <a class="nav-link active" href="#"><img src="<?php echo $row['avatar']?>" alt="" width="50px" height="50px"
                    class="img-circle"></a>
        </div>
        <ul class="nav flex-justify-content-center my-3">
            <li class="nav-item mx-0 ">
                <a class="nav-link active" href="#">
                    <?php echo $row['name']?></a>
                <img src="" alt="">
            </li>
            <li class="nav-item mx-0 ">
                <a class="nav-link active" href="./myblog.php">我的文章</a>
                <img src="" alt="">
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/admin/writeblog.php">写博客</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/admin/setting.php">编辑资料</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/admin/login.php">注销</a>
            </li>
        </ul>
    </div>
    <h3 class='mx-5'>热门文章</h3>
    <div class="container">
        <div class="btn-toolbar justify-content-center" role="toolbar" aria-label="Toolbar with button groups">
            <div class="btn-group mr-2" role="group" aria-label="First group">
                <button type="button" class="btn btn-outline-secondary mx-1 first">上一页</button>
                <button type="button" class="btn btn-outline-secondary mx-1 page">1</button>
                <button type="button" class="btn btn-outline-secondary mx-1 page">2</button>
                <button type="button" class="btn btn-outline-secondary mx-1 page">3</button>
                <button type="button" class="btn btn-outline-secondary mx-1 page">4</button>
                <button type="button" class="btn btn-outline-secondary mx-1 page">5</button>
                <button type="button" class="btn btn-outline-secondary mx-1 end">下一页</button>
            </div>

        </div>
        <table class='table table-hover my-3 text-center' id="table">
            <thead class="table ">
                <tr>
                    <th scope="col">文章标题</th>
                    <th scope="col">作者</th>
                    <th scope="col">创作时间</th>
                    <th scope="col">分属类别</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row=mysqli_fetch_assoc($query)):?>
                <tr>
                    <th scope="row"><a class='toblog' href='#'><?php echo $row['theme'] ?></a></th>
                    <td class='p-3'>
                        <?php echo $row['user'] ?>
                    </td>
                    <td>
                        <?php echo $row['time'] ?>
                    </td>
                    <td>
                        <?php echo $row['class'] ?>
                    </td>
                </tr>
                <?php endwhile ?>
            </tbody>
        </table>
        
        <div id="blog" class='col-10'>
            
                <button class='return btn btn-default '>返回</button>
            
            <div class="blog-theme rounded-5 border border-light my-3 "></div>
            <div class="blog-content rounded-5 border border-light my-3 "></div>
        </div>
        
    </div>
    <script>
        $(function () {
            $("#blog").hide()
            $(".return").hide()
            $('.toblog').click(function () {
                var blogname = $(this).text();
                $.get('/admin/api/showblog.php?name='+blogname,function (rel) {
                    $('.btn-toolbar').fadeOut(700);
                    $('#table').fadeOut(700, function () {
                    $("#blog").fadeIn(700);
                    $('.blog-theme').show().html("<h3>" + blogname + "</h3>");
                    $('.blog-content').show().html(rel);
                    $(".return").show()
                    });

                })
            })
            $('.return').click(function () {
                $("#blog").fadeOut(700, function () {
                    $('#table').fadeIn(700);
                    $('.btn-toolbar').fadeIn(700);
                });
            })
            //y 为所有文章的数量,根据这个数量来确定有多少页
            var y=<?php echo $count?>;
            var current_page = <?php echo $page?>;
            //y/6想上去整数,每个页面最多放6个文章的列表 x为一共有多少页; 
            x = Math.ceil(y / 6);
            //这里写10是因为没有过多文章的数据来测试分页功能,正式使用请取消
            x=10;
            //点击按钮触发事件使得页面重新加载
            $('.page').click(function () {
                var page = $(this).text();
                window.location.href = "/admin/index.php?p=" + page;
            })
            //点击上一页下一页跳转到功能
            $('.end').click(function () {
                var page = $(this).text();
                window.location.href = "/admin/index.php?p=" + (++current_page);
            })
            $('.first').click(function () {
                var page = $(this).text();
                window.location.href = "/admin/index.php?p=" + (--current_page);
            })

            //对于页码滚动显示功能的实现
            if (x<2) {
                $(".btn-group").hide();
            } else if (x<6) {
                $(".page:eq(" + (x-1) + ")").nextAll().hide()
                $(".end").show();
            } 
            //全在左边的情况
            if(current_page<3){
                for(var i=0;i<6;i++){
                    $('.page').eq(i).text((i+1));
                }
            }
            //全在右边的情况
            else if(current_page>x-2){
                for(var i=0;i<6;i++){
                    $('.page').eq(i).text((x-4+i));
                }
            }
            //在中间的情况使得当前页面为中
            else {
                for(var i=0;i<6;i++){
                    $('.page').eq(i).text((current_page-2+i));
                }
            }
            if (current_page == 1) {
                $('.first').css("visibility","hidden");
            }
            if (current_page == x) {
                $('.end').css("visibility","hidden");
            }
            //为当前页面的按钮改变样式更醒目
            for(var i=0;i<6;i++){
                if($('.page').eq(i).text()==current_page){
                    $('.page').eq(i).addClass("btn-outline-info")
                }
            }

            //用来删除文章的函数
            
        })
    </script>
</body>

</html>