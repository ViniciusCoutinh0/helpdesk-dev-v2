<?php $v->layout('_theme'); ?>
<form action="<?=str_replace('{user_id}', $data['user_id'], $router->route('post.update.user')); ?>" method="post" class="ui form js-update-user-form">
<?=csrfField(); ?>
<div class="ui two column grid">
    <?php if ($logged) :  ?>
        <div class="sixteen wide mobile ten wide computer column">
        <?php if ($user->getUserRules()->Rule_Update == 'S') : ?>
            <?php if ($load) : ?>
            <div class="ui segments">
            <div class="ui segment">
                <h4>Editar Usuário: <?=mb_convert_case($load->Username, MB_CASE_TITLE, 'UTF-8'); ?></h4>
            </div>
            <div class="ui secondary segment">
            <div class="field required">
                <label for="name">Nome:</label>
                <div class="ui left icon input">
                    <input type="text" name="name" id="name" value="<?=$load->Name; ?>" autocomplete="off" required>
                    <i class="user icon"></i>
                </div>
            </div>
            <div class="field required">
                <label for="username">Nome de Usúario:</label>
                <div class="ui left icon input">
                    <input type="text" name="username" id="username" value="<?=$load->Username; ?>" autocomplete="off" required>
                    <i class="user icon"></i>
                </div>
            </div>
            <div class="field">
                <label for="password">Senha:</label>
                <div class="ui left icon input">
                    <input type="password" name="password"  id="password">
                    <i class="lock icon"></i>
                </div>
            </div>
            <div class="field required">
                <label for="email">Email:</label>
                <div class="ui left icon input">
                    <input type="email" name="email" id="email" value="<?=$load->Email; ?>" required>
                    <i class="mail icon"></i>
                </div>
            </div>
            <div class="field required">
                <?php if ($sectors) : ?>
            <label for="sector">Setor Atual:</label>
            <select name="sector" name="sector" class="ui dropdown">
                    <?php foreach ($sectors as $sector) : ?>
                        <?php if ($sector->Framework_Sector == $load->Framework_Sector) : ?>
                        <option value="<?=$sector->Framework_Sector;?>" selected><?=mb_convert_case($sector->Name, MB_CASE_TITLE, 'UTF-8'); ?></option>
                        <?php else : ?>
                        <option value="<?=$sector->Framework_Sector;?>"><?=mb_convert_case($sector->Name, MB_CASE_TITLE, 'UTF-8'); ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
            </select>
                <?php else : ?>
            <div class="ui message">
                <p>Nenhum setor cadastro até o momento.</p>
            </div>
                <?php endif; ?>
            </div>
            <div class="field">
                <label for="state">Status do Usuário:</label>
                <select name="state" id="state" class="ui dropdown select">
                   <?php if ($load->State == 'S') : ?>
                        <option value="S">Ativo</option>
                        <option value="N">Inativo</option>
                   <?php else : ?>
                        <option value="S">Ativo</option>
                        <option value="N" selected>Inativo</option>
                   <?php endif; ?>
                </select>
            </div>
            </div>
            <div class="ui segment">
            <button class="ui labeled icon red button js-update-user-btn">
                <i class="save icon"></i>
                Atualizar Usuário
            </button>
            </div>
            </div>
                <?php if ($message) : ?>
                <div class="ui big message">
                    <p><?=$message;?></p>
                </div>
                <?php endif; ?>
            <?php else : ?>
            <div class="ui message">
                <p>Usuário não existem ou é inválido.</p>
            </div>
            <?php endif; ?>
        <?php else : ?>
            <div class="ui message">
                <p>Você não tem permissão para acessar está página.</p>
            </div>
        <?php endif; ?>
        </div>
        <?=$v->insert('account/sidebar'); ?>
    <?php else : ?>
        <div class="ui message">
            <p>Você precisa está logado para visualizar está página.</p>
        </div>
    <?php endif; ?>
</div>
</form>
<?php $v->start('javascript'); ?>
<script type="text/javascript">
    submitOnForm('.js-update-user-form', '.js-update-user-btn');
</script>
<?php $v->end(); ?>
