<?php

$v->layout('_theme'); ?>
<form action="<?=$router->route('post.add.user'); ?>" method="post" class="ui form">
<div class="ui two column grid">
    <?php if ($logged) :  ?>
        <div class="sixteen wide mobile ten wide computer column">
            <?php if ($user->getUserRules()->Rule_Update == 'S') : ?>
                <?php if ($users) : ?>
                    <div class="ui segments">
                        <div class="ui segment">
                            <h4>Lista de Usuários</h4>
                        </div>
                        <div class="ui secondary segment">
                        <div class="ui divided items">    
                        <?php foreach ($users as $user) : ?>
                            <div class="item">
                                <div class="ui image">
                                    <img src="<?=url($user->Avatar); ?>" alt="<?=$user->Username?>">
                                </div>
                                <div class="middle aligned content">
                                    <div class="header">
                                        <?=mb_convert_case($user->Name, MB_CASE_TITLE, 'UTF-8'); ?>
                                    </div>
                                    <div class="meta">
                                       <div class="ui basic label">Setor: <div class="detail"><?=$user->getUserSector()->Name; ?></div></div>
                                        <div class="ui basic label">Estado: <?=$user->State == 'S' ? 'Ativo' : 'Inativo'; ?>
                                        </div> 
                                    </div>
                                    <div class="extra">
                                        <a href="<?=str_replace('{user_id}', $user->Framework_User, $router->route('get.update.user')); ?>" class="ui basic red labeled icon right floated button">
                                            <i class="edit icon"></i>
                                            Editar 
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        </div>
                        </div>
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
