<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$_ENV['CONFIG_APP_NAME']; ?></title>
    <link rel="stylesheet" href="<?=asset('resources/css/font.min.css'); ?>">
    <link rel="stylesheet" href="<?=asset('resources/css/app.min.css'); ?>">
    <link rel="shortcut icon" href="<?=asset('favicon.ico'); ?>" />
</head>

<body>
    <?php if (Session()->has('USER_ID')) : ?>
    <header id="header">
        <a id="brand" href="<?=url('app.home'); ?>">
            <img class="avatar" src="<?=asset('storage/avatar/' . mb_strtoupper($user->Username[0]) . '.png'); ?>" alt="avatar.png" />
            <?=mb_convert_case($user->Username, MB_CASE_TITLE);?>                       
        </a>
        <nav id="nav">
            <button aria-label="Abrir Menu" id="btn-mobile" aria-controls="menu" aria-haspopup="true"
                aria-expanded="false">Menu
                <span id="icon"></span>
            </button>
            <ul id="menu" role="menu">
                <li><a href="<?=url('ticket.store.view', ['user' => $user->Framework_User]); ?>">Novo Chamado</a></li>
                <li><a href="<?=url('app.home'); ?>">Lista de Chamados</a></li>
                <li><a href="<?=url('account.view', ['user' => $user->Framework_User]); ?>">Configurações da Conta</a></li>
                <li><a href="<?=url('auth.signout'); ?>">Sair</a></li>
            </ul>
        </nav>
    </header>
    <?php endif; ?>
    <main class="container">
        <?=$v->section('content'); ?>
    </main>
</body>

<script src="<?=asset('resources/javascript/leopard.min.js'); ?>"></script>
<!-- <script src="https://kit.fontawesome.com/d6a7e4aac6.js" crossorigin="anonymous"></script> -->
<?php if (Session()->has('USER_ID')) : ?>
    <script type="text/javascript">
        execToggle();
    </script>
<?php endif; ?>
</html>
<?=$v->section('javascript'); ?>
</html>
