<?php

$v->layout('_theme'); ?>
<form action="<?=$router->route('post.add.user'); ?>" method="post" class="ui form">
<div class="ui two column grid">
    <?php if ($logged) :  ?>
        <div class="sixteen wide mobile ten wide computer column">
            <?php if ($user->getUserRules()->Rule_Update == 'S') : ?>
                <?php if ($sectors) : ?>
                <div class="ui segments">
                    <div class="ui segment">
                        <h4>Lista de Setores Cadastrados</h4>
                    </div>
                    <div class="ui secondary segment">
                        <table class="ui basic table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Setor</th>
                                    <th>Cadastrado</th>
                                    <th>Atualizado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $count = 0; foreach ($sectors as $sector) :  ?>
                                    <tr>
                                        <td><?=$sector->Framework_Sector; ?></td>
                                        <td><a href="<?=str_replace('{sector_id}', $sector->Framework_Sector, $router->route('get.update.sector')); ?>"><?=mb_convert_case($sector->Name, MB_CASE_TITLE, 'UTF-8'); ?></a></td>
                                        <td><?=date('d/m/Y à\s H:i:s', strtotime($sector->Created_at)) ?></td>
                                        <td><?=($sector->Updated_at ? date('d/m/Y à\s H:i:s', strtotime($sector->Updated_at)) : '-') ?></td>
                                    </tr>
                                    <?php  $count++;
                                endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4">Total de Setores: <strong><?=$count; ?></strong></td>
                                </tr>
                            </tfoot>
                        </table>
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
