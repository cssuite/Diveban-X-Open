<form>
<ul class="dropdown-menu">
    <li>
        <div class="navbar-login">
            <div class="row">
            </div>
    </li>
</ul>
</form>

<ul class="dropdown-menu">
    <li>
        <div class="navbar-login">
            <div class="row">
                <div class="col-lg-4">
                    <p class="text-center">
                        <span class="glyphicon glyphicon-user icon-size"></span>
                    </p>
                </div>
                <div class="col-lg-8">
                    <p class="text-left"><strong>this</strong></p>
                    <p class="text-left small"><label class="badge badge-info">Администратор</label></p>
                    <p class="text-left">
                        <a href="#" class="btn btn-primary btn-block btn-sm">Панель администратора</a>
                    </p>
                </div>
            </div>
        </div>
    </li>
    <li class="divider"></li>
    <li>
        <div class="navbar-login navbar-login-session">
            <div class="row">
                <div class="col-lg-12">
                    <p>
                        <a href="#" class="btn btn-danger btn-block">Выйти</a>
                    </p>
                </div>
            </div>
        </div>
    </li>
</ul>

<ul class="nav navbar-nav navbar-right">
    <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <span class="glyphicon glyphicon-user"></span> 
            <strong>Аккаунт</strong>
            <span class="glyphicon glyphicon-chevron-down"></span>
        </a>
        {adminpanel}

    </li>
</ul>

<form class="navbar-form navbar-right" role="search">
    <div class="form-group">
        <input type="text" class="form-control" placeholder="Поиск по банам Steam\IP...">
    </div>
    <button type="submit" class="btn btn-default btn-small">Поиск</button>
</form>