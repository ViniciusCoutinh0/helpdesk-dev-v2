<?php $v->layout('_theme'); ?>
<form action="<?=$router->route('post.add.sector'); ?>" method="post" class="ui form js-update-sector-form">
<?=csrfField(); ?>
<div class="ui two column grid">
    <?php if ($logged) :  ?>
        <div class="sixteen wide mobile ten wide computer column">
            <?php if ($user->getUserRules()->Rule_Create == 'S') : ?>
            <div class="ui segments">
            <div class="ui segment">
                <h4>Adicionar Setor</h4>
            </div>
            <div class="ui two column grid">
                <div class="column">
                <div class="ui basic segment">
                    <div class="field required">
                        <label for="sector">Nome do Setor:</label>
                        <div class="ui input">
                            <input type="text" name="sector" id="sector" autocomplete="off" value="<?=($data ? $data['sector'] : ''); ?>" placeholder="Ex: Suporte T.i" required>
                        </div>
                    </div>
                    <div class="ui message">
                        <div class="content">
                            <h5>Observações:</h5>
                        </div>
                        <p class="text-mini">Para Administradores marcar todas as opções.</p>
                    </div>
                </div>
                </div>
                <div class="column">
                <div class="ui basic segment">
                <h4>Permissões:</h4>
                <div class="field">
                    <div class="ui checkbox">
                        <input type="checkbox" name="create" id="create" tabindex="0" <?=(isset($data['create']) == 'on' ? 'checked' : '') ?>>
                        <label for="create"><span data-inverted="" data-tooltip="Permite adicionar Usuários e Setores no Sistema." data-position="top center">Criar</span></label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox">
                        <input type="checkbox" name="read" id="read" tabindex="0" <?=(isset($data['read']) == 'on' ? 'checked' : '') ?>>
                        <label for="read"><span data-inverted="" data-tooltip="Permite visualizar chamados e configurações básicas da conta." data-position="top center">Visualizar</span></label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox">
                        <input type="checkbox" name="update" id="update" tabindex="0" <?=(isset($data['update']) == 'on' ? 'checked' : '') ?>>
                        <label for="update"><span data-inverted="" data-tooltip="Permite editar Usuários e Setores no Sistema." data-position="top center">Editar</span></label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox">
                        <input type="checkbox" name="delete" id="delete" tabindex="0" <?=(isset($data['delete']) == 'on' ? 'checked' : '') ?>>
                        <label for="delete"><span data-inverted="" data-tooltip="Permite excluir Usuários e Setores no Sistema." data-position="top center">Excluir</span></label>
                    </div>
                </div>
                </div>
                </div>
            </div>
            <div class="ui segment">
                <button class="ui labeled icon red button js-update-sector-btn">
                    <i class="save icon"></i>
                    Adicionar Setor
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
    submitOnForm('.js-update-sector-form', '.js-update-sector-btn');
</script>
<?php $v->end(); ?>
