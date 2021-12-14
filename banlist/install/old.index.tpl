<!DOCTYPE html>
<html lang="ru">
<head>
    <!-- Теги -->
    <meta charset="utf-8">
    <title>{title_name} | {global_name}</title>
    <meta name="description" content="{title_desc}">
    <meta name="keywords" content="{title_keys}">

    <meta name="revisit-after" content="3 day">
    <meta name="rating" content="General">

    <meta name="generator" content="Superban CMS v 1.5">
    <meta name="author" content="Oleksandr Kornienko">

    <!-- Иконка -->
    <link rel="shortcut icon" href="../style/img/favicon.ico">

    <!-- Стили -->
    <link rel="stylesheet" href="../style/css/style.css">

    <style>
        body {
            padding-top: 60px;
            background:url("{web_fon}");
            background-attachment: fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            background-size: cover;
        }
    </style>

    <!--[if lt IE 9]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>

<body>
<div class="navbar  navbar-fixed-top">
    <div class="navbar-inner">
        <div class="container">
            <a class="brand" href="/">Главная</a>
            <ul class="nav">
                <li><a href="{web_site}">Сайт</a></li>
                <li><a href="/index.php?do=admins">Список Администрации</a></li>
                <li><a href="/index.php?do=servers">Сервера</a></li>
                <li><a href="/index.php?do=search">Поиск</a></li>
            </ul>
        </div>
    </div>
</div>

<div align="center"><img src="{web_logo}"></div>
<br />
<div class="container">
    <div class="alert alert-info">
        <span style="color:black;font-weight:bold;"><h4>{steps}</h4></span>
        <br />
        <div class="progress">
            <div class="bar" style="width: {num_steps}"></div>
        </div>
    </div>
    <div class="well well-small">
        {page_content}
    </div>
    <div class="navbar">
        <div class="navbar-inner">
            <a class="brand" href="/">Главная</a>
            <ul class="nav pull-right">
                <li><a href="{web_site}">Сайт</a></li>
                <li><a href="/index.php?do=admins">Список Администрации</a></li>
                <li><a href="/index.php?do=servers">Сервера</a></li>
                <li><a href="/index.php?do=search">Поиск</a></li>
            </ul>
        </div>
    </div>
</div>


<!-- Javascripts -->
<!-- Размещенные в конце документа, чтобы ускорить загрузку страниц -->
<script src="/style/js/jquery.js"></script>
<script src="/style/js/bootstrap.js"></script>
<script>
    $("[rel=tooltip]").tooltip();
    $("[rel=popover]").popover();
</script>
</body>
</html>