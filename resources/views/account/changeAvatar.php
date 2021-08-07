<?php $v->layout('_theme'); ?>
<?php if ($logged) : ?>
<form action="<?=str_replace('{user_id}', $user->Framework_User, $router->route('post.change.avatar'));?>" method="post"
    enctype="multipart/form-data" class="ui form">
    <div class="ui two column grid">
        <!-- Content -->
        <div class="sixteen wide mobile ten wide computer column">
            <div class="ui segments">
                <div class="ui segment">
                    <h4>Alterar Avatar</h4>
                </div>
                <div class="ui segment">
                    <div class="ui two divided column grid">
                        <div class="three wide column">
                           <div class="flex">
                               <span>Atual</span>
                                <img class="img" src="<?=url($user->Avatar); ?>"
                                    alt="Avatar de: <?=mb_convert_case($user->Username, MB_CASE_TITLE);?>"
                                    title=" Avatar de: <?=mb_convert_case($user->Username, MB_CASE_TITLE);?>">
                           </div>
                        </div>
                        <div class="thirteen wide middle aligned column">
                            <div class="field">
                                <label for="newImage">Nova Image:</label>
                                <input type="file" name="newImage" id="newImage" accept=".jpg, .png" required>
                            </div>
                            <div class="field">
                                <p class="text-mini">
                                    <i class="info circle icon"></i> Formatos permitidos: <strong>jpg e png</strong>. |
                                    Tamanho máximo: <strong>1MB</strong>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ui segment">
                    <button class="ui red button">Alterar Avatar</button>
                </div>
            </div>
            <?php if ($message) : ?>
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
