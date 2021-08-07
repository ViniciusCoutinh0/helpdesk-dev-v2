<?php $v->layout('_theme');?>
<form action="<?=$router->route('post.add.user'); ?>" method="post" class="ui form js-admin-adduser-form">
<?=csrfField(); ?>
<div class="ui two column grid">
    <?php if ($logged) :  ?>
        <div class="sixteen wide mobile ten wide computer column">
            <?php if ($user->getUserRules()->Rule_Create == 'S') : ?>
                <div class="ui segments">
                    <div class="ui segment">
                        <h4>Adicionar Usuário</h4>
                    </div>
                    <div class="ui secondary segment">
                    <div class="field required">
                            <label for="username">Nome:</label>
                            <div class="ui left icon input">
                                <input type="text" name="name" id="name" autocomplete="off" value="<?=($data['name'] ?? '');?>" required>
                                <i class="user icon"></i>
                            </div>
                        </div>
                        <div class="field required">
                            <label for="username">Nome de Usúario:</label>
                            <div class="ui left icon input">
                                <input type="text" name="username" id="username" value="<?=($data['username'] ?? '');?>" autocomplete="off" required>
                                <i class="user icon"></i>
                            </div>
                        </div>
                        <div class="field required">
                            <label for="password">Senha:</label>
                            <div class="ui left icon input">
                                <input type="password" name="password" id="password" required>
                                <i class="lock icon"></i>
                            </div>
                        </div>
                        <div class="field required">
                            <label for="email">Email:</label>
                            <div class="ui left icon input">
                                <input type="email" name="email" id="email" value="<?=($data['email'] ?? '');?>" autocomplete="off"  required>
                                <i class="mail icon"></i>
                            </div>
                        </div>
                        <div class="field required">
                            <?php if ($sectors) : ?>
                            <label for="sector">Setor:</label>
                            <select name="sector" name="sector" class="ui dropdown">
                                <?php foreach ($sectors as $sector) : ?>
                                    <?php if (intval($sector->Framework_Sector) == intval($data['sector'])) : ?>
                                        <option value="<?=$sector->Framework_Sector;?>" selected><?=mb_convert_case($sector->Name, MB_CASE_TITLE, 'UTF-8'); ?></option>
                                    <?php else : ?>
                                        <option value="<?=$sector->Framework_Sector;?>"><?=mb_convert_case($sector->Name, MB_CASE_TITLE, 'UTF-8'); ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                            <?php else : ?>
                                <div class="ui message">
                                    <p>Nenhum setor cadastro</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="ui segment">
                        <button class="ui labeled icon red button js-admin-adduser-btn">
                            <i class="save icon"></i>
                            Adicionar Usuário
                        </button>
                    </div>
                </div>
                <?php if (isset($message)) : ?>
                <div class="ui big message">
                    <p><?=$message;?></p>
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
    submitOnForm('.js-admin-adduser-form', '.js-admin-adduser-btn');
</script>
<?php $v->end(); ?>
