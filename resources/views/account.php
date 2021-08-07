<?php $v->layout('_theme'); ?>
<?php if ($logged) : ?>
<div class="ui two column grid">
    <!-- Content --> 
        <?=$v->insert('account/home'); ?>
    <!-- Content -->
    <!-- Sidebar -->
        <?=$v->insert('account/sidebar'); ?>
    <!-- Sidebar --> 
</div>
<?php else : ?>
<div class="ui negative message">
    <p>Você precisa está logado para visualizar está página.</p>
</div>
<?php endif; ?>
