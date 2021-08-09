<?php $v->layout('_theme'); ?>
<?php if (Session()->has('USER_ID')) :?>
<form method="POST" action="<?=url('admin.create.report', ['user' => $user->Framework_User]); ?>" id="form-report">
    <div class="row">
        <div class="col-12 col-sm-8 mb-2">
            <div class="box mb-2">
                <div class="box-header">
                    <h4>Relátorio de Chamados</h4>
                </div>
                <div class="box-content p-2">
                    <div class="row">
                        <input type="hidden" name="csrf_token" value="<?=csrf_token(); ?>">
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label class="form-label required" for="first_day">Data Inicial:</label>
                                <input type="date" name="first_day" id="first_day" class="form-control" min="2020-01-01" max="<?=date('Y-m-d'); ?>">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label class="form-label required" for="last_day">Data Final:</label>
                                <input type="date" name="last_day" id="last_day" class="form-control" min="2020-01-01" max="<?=date('Y-m-d'); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-content p-2 border-top">
                    <button class="btn btn-danger" id="btn-report">Gerar Relatório</button>
                </div>
            </div>
            <?php if ($message) : ?>
                <?=$message; ?>    
            <?php endif; ?>
        </div>
        <?=$v->insert('account/sidebar'); ?>
    </div>
</form>
    <?php $v->start('javascript'); ?>
    <script type="text/javascript">
        onSubmit('form-report', 'btn-report');
    </script>
    <?php $v->end(); ?>
<?php endif; ?>
