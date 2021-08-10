<div class="col-12 col-sm-4">
<div class="box">
    <div class="box-header">
        <h4>Ações</h4>
    </div>
    <div class="box-content p-2">
        <ul class="list-group">
            <li class="list-group-item">
                <a href="<?=url('account.password', ['user' => $user->Framework_User]); ?>"
                    class="text-reset text-decoration-none">Alterar Senha</a>
            </li>
        </ul>
    </div>
    <?php if ($sector->Name === 'Suporte T.i') : ?>
    <div class="box-header border-top">
        <h4>Administrativo</h4>
    </div>
    <div class="box-content p-2">
        <ul class="list-group">
            <li class="list-group-item">
                <a href="<?=url('admin.view.report');?>" class="text-reset text-decoration-none">Relatório de Chamados</a>
            </li>
            <li class="list-group-item">
                <a href="<?=url('admin.view.create.user');?>" class="text-reset text-decoration-none">Novo Usuário</a>
            </li>
            <li class="list-group-item">
                <a href="#" class="text-reset text-decoration-none">Novo Setor</a>
            </li>
            <li class="list-group-item">
                <a href="<?=url('admin.list.all.users'); ?>" class="text-reset text-decoration-none">Editar Usuário</a>
            </li>
            <li class="list-group-item">
                <a href="#" class="text-reset text-decoration-none">Editar Setor</a>
            </li>
        </ul>
    </div>
    <?php endif; ?>
</div>
