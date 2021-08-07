<div class="sixteen wide mobile six wide computer column">
    <div class="ui segments">
        <div class="ui segment">
            <h4>Ações</h4>
        </div>

        <div class="ui basic segment">
            <a href="<?=str_replace('{user_id}', $user->Framework_User, $router->route('get.change.avatar')); ?>"
                class="ui basic fluid button red">Alterar Avatar</a>
        </div>
        <div class="ui basic segment">
            <a href="<?=str_replace('{user_id}', $user->Framework_User, $router->route('get.change.password')); ?>"
                class="ui basic fluid button red">Alterar Senha</a>
        </div>

        <?php if ($user->getUserSector()->Name == 'Suporte T.i') : ?>
        <div class="ui segment">
            <h4>Administrativo</h4>
        </div>
        <div class="ui basic segment">
            <a href="<?=$router->route('get.all.tickets'); ?>" class="ui basic fluid button red">Relatório Chamados</a>
        </div>
        <?php endif; ?>
        <?php if ($user->getUserRules()->Rule_Update == 'S') : ?>
        <div class="ui basic segment">
            <a href="<?=str_replace('{user_id}', $user->Framework_User, $router->route('get.add.user')); ?>"
                class="ui basic fluid button red">Adicionar Usuário</a>
        </div>
        <div class="ui basic segment">
            <a href="<?=str_replace('{user_id}', $user->Framework_User, $router->route('get.add.sector')); ?>"
                class="ui basic fluid button red">Adicionar Setor</a>
        </div>
        <div class="ui basic segment">
            <a href="<?=$router->route('get.list.user'); ?>" class="ui basic fluid button red">Editar Usuário</a>
        </div>
        <div class="ui basic segment">
            <a href="<?=$router->route('get.list.sector'); ?>" class="ui basic fluid button red">Editar Setor</a>
        </div>
        <?php endif; ?>
    </div>
</div>
