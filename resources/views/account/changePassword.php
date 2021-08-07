<?php $v->layout('_theme'); ?>
<?php if ($logged) : ?>
<form action="<?=str_replace('{user_id}', $user->Framework_User, $router->route('post.change.password'));?>"
    method="post" class="ui form">
    <div class="ui two column grid">
        <!-- Content -->
        <div class="sixteen wide mobile ten wide computer column">
            <div class="ui segments">
                <div class="ui segment">
                    <h4>Alterar Senha</h4>
                </div>
                <div class="ui segment">
                    <div class="field required">
                        <label for="oldPassword">Senha atual:</label>
                        <div class="ui left icon input">
                            <input type="password" name="oldPassword" id="oldPassword" autocomplete="off" required>
                            <i class="lock icon"></i>
                        </div>
                    </div>
                </div>
                <div class="ui segment">
                    <div class="field required">
                        <label for="newPassword">Nova senha:</label>
                        <div class="ui left icon input">
                            <input type="password" name="newPassword" id="newPassword" autocomplete="off" required>
                            <i class="lock icon"></i>
                        </div>
                    </div>
                    <div class="field required">
                        <label for="confirmPassword">Digite novamente:</label>
                        <div class="ui left icon input">
                            <input type="password" name="confirmPassword" id="confirmPassword" autocomplete="off"
                                required>
                            <i class="lock icon"></i>
                        </div>
                    </div>
                </div>
              
                <div class="ui segment">
                    <button class="ui red button">Alterar Senha</button>
                </div>
            </div>
            <?php if (is_string($message)) : ?>
            <div class="ui big message">
               <p><?=$message;?></p>
            </div>
            <?php endif; ?>
        </div>
        <!-- Content -->
        <!-- Sidebar -->
        <?=$v->insert('account/sidebar'); ?>
        <!-- Sidebar -->
    </div>
</form>
<?php else : ?>
<div class="ui message">
    <p>Você precisa está logado para visualizar está página.</p>
</div>
<?php endif; ?>
