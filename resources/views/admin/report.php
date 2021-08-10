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
                                <input type="date" name="first_day" id="first_day" class="form-control" min="2020-01-01" max="<?=date('Y-m-d'); ?>" value="<?=old('first_day'); ?>">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group">
                                <label class="form-label required" for="last_day">Data Final:</label>
                                <input type="date" name="last_day" id="last_day" class="form-control" min="2020-01-01" max="<?=date('Y-m-d'); ?>" value="<?=old('last_day'); ?>">
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
            <?php if(count($data)):  ?>
                <a href="<?=url('admin.output.report', ['first' => old('first_day'), 'last' => old('last_day')]); ?>" target="_blank" class="text-reset text-decoration-none" rel="noopener noreferrer" id="link-report">
                    <i class="fas fa-solid fa-download"></i> Download File.
                </a>
            <?php endif; ?>
        </div>
        <?=$v->insert('account/sidebar'); ?>
    </div>
</form>
    <?php $v->start('javascript'); ?>
    <script type="text/javascript">
        const form = document.getElementById('form-report');
        const btn  = document.getElementById('btn-report'); 
        const link = document.getElementById('link-report');

        form.addEventListener('submit', function () {
            btn.setAttribute('disabled', true);
            btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span><span> Gerando o Arquivo por favor aguarde...</span>';
            link.style.display = 'none';
        });
    </script>
    <?php $v->end(); ?>
<?php endif; ?>
