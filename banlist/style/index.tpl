<!DOCTYPE html>
<html lang="ru">
<head>
    <!-- Теги -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{title_name} | {global_name}</title>
    <meta name="description" content="{title_desc}">
    <meta name="keywords" content="{title_keys}">

    <meta name="generator" content="SuiteCMS 1.0">
    <meta name="author" content="RevCrew">

    <!-- Иконка -->
    <link rel="shortcut icon" href="{url}style/img/favicon.ico">

    <!-- Стили -->
    <link rel="stylesheet" href="{url}style/css/bootstrap3.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
          rel="stylesheet">
    <link rel="stylesheet" href="{url}style/css/navbar.css">

    <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css" rel="stylesheet">
    <style>
        body {
            background: url("{web_fon}");
            background-attachment: fixed;
            -webkit-background-size: cover;
            -moz-background-size: cover;
            background-size: cover;
        }
    </style>
</head>

<body>
<div class="navbar navbar-inverse navbar-da" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="{url}" class="navbar-brand">SuiteCMS</a>
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li><a href="{web_site}">Сайт</a></li>
                <li><a href="{url}index.php?do=admins">Список администрации</a></li>
                <li><a href="{url}index.php?do=servers">Список Серверов</a></li>
            </ul>
            {adminpanel}
            <form class="navbar-form navbar-right" role="search" action="index.php">
                <div class="form-group">
                    <input type="text" class="form-control" name="search" placeholder="Поиск по банам Steam\IP...">
                </div>
                <button type="submit" class="btn btn-default btn-small">Поиск</button>
            </form>
        </div>
    </div>
</div>
<div align="center"><img src="{web_logo}"></div>
<div class="container" style="width:80%">
    {index_content}
</div>

<!-- Optional JavaScript -->
<!-- Latest compiled and minified JavaScript -->
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"
        integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa"
        crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>

<script>
    $("[rel=tooltip]").tooltip();
    $("[rel=popover]").popover();
</script>
</body>
</html>