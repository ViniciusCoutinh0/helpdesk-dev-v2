<div class="content-center">
    <div class="d-flex flex-wrap flex-column align-items-center justify-content-center">
        <div class="bg-login my-4"></div>
        <div class="box-size-fluid">
            <div class="box mb-2">
                <div class="box-header text-center">
                    <h4>HelpDesk - Promofarma.</h4>
                </div>
                <div class="box-content p-2">
                    <form method="post" action="<?=url('auth.sigin'); ?>" id="form-login">
                        <input type="hidden" name="csrf_token" value="<?=csrf_token();?>">
                        <div class="form-group">
                            <label for="username" class="form-label required">Username:</label>
                            <input type="text" name="username" id="username" class="form-control form-control-lg"
                                value="<?=($data['username'] ?? null); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password" class="form-label required">Password:</label>
                            <input type="password" name="password" id="password" class="form-control form-control-lg"
                                autocomplete="off" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button class="btn btn-lg btn-danger my-2" role="button" id="btn-login">Entrar</button>
                        </div>
                    </form>
                </div>
            </div>
            <?php if ($message) : ?>
                <?=$message; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
