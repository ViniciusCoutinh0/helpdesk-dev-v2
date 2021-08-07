<form method="POST" action="<?=url('auth.sigin'); ?>" id="form-login">
    <div class="d-flex flex-wrap justify-content-center justify-content-sm-between align-items-center">
        <!-- welcome -->
        <div class="bg-login"></div>
        <!-- welcome -->
        <!-- box.container -->
        <div class="mb-2 box-size-fluid">
            <div class="box mb-2">
                <div class="box-header">HelpDesk - Promofarma</div>
                <div class="box-content p-3">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token(); ?>">
                    <div class="form-group">
                        <label for="username" class="form-label required">Username:</label>
                        <input type="text" name="username" id="username" class="form-control form-control-lg" value="<?=($data['username'] ?? null); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password" class="form-label required">Password:</label>
                        <input type="password" name="password" id="password" class="form-control form-control-lg"
                            autocomplete="off" required>
                    </div>
                    <div class="d-grid gap-2 mb-1">
                        <button class="btn btn-lg btn-danger mt-3 js-form-btn" role="button" id="btn-login">Entrar</button>
                    </div>
                </div>
            </div>
            <?php if (isset($message)) : ?>
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle"></i> <?=$message; ?>
            </div>
            <?php endif; ?>
        </div>
        <!-- box.container -->
    </div>
</form>
<?php $v->start('javascript'); ?>
<script type="text/javascript">
    onSubmit('form-login', 'btn-login');
</script>
<?php $v->end(); ?>
