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
    <link rel="shortcut icon" href="../style/img/favicon.ico">

    <!-- Стили -->
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons"
		  rel="stylesheet">
	<link rel="stylesheet" href="../style/css/navbar.css">

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
<div class="navbar navbar-inverse navbar-fixed-top navbar-da" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a href="#" class="navbar-brand">SuiteCMS</a>
		</div>
		<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
			</ul>
		</div>
	</div>
</div>

<div style="padding-top: 60px" align="center"><img src="{web_logo}"></div>
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
</div>

<!-- Optional JavaScript -->
<!-- Latest compiled and minified JavaScript -->
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

<script>
	$("[rel=tooltip]").tooltip();
	$("[rel=popover]").popover();
</script>
</body>
</html>