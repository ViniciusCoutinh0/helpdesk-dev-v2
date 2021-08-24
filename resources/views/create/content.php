<div class="box mb-2">
    <input type="hidden" name="csrf_token" value="<?=csrf_token(); ?>">
    <div class="box-header">Informações do Chamado</div>
    <div class="box-content p-2 border-bottom">
        <div class="form-group">
            <label for="title" class="form-label required">Assunto:</label>
            <input type="text" name="title" id="title" placeholder="Preenchimento automático" class="form-control"
                readonly required value="<?=old('title');?>">
        </div>
    </div>
    <div id="custom-fields-block" class="d-none border-bottom">
        <div class="box-header">Campos Obrigatórios:</div>
        <div class="box-content p-2">
            <div id="custom-fields"></div>
        </div>
    </div>
    <div class="box-content p-2 border-bottom mb-2">
        <div class="form-group">
            <div class="d-flex justify-content-between align-items-center">
                <label for="message" class="form-label required">Messagem:</label>
                <span class="fs-7" style="color: #8c8d8f;">Máximo de Caracteres: <span id="counter">3000</span>/3000</span>
            </div>
            <div class="form-floating">
                <textarea name="message" id="message" maxlength="3000" style="height: 250px;" class="form-control" required><?=old('message');?></textarea>
                <label for="floatingTextarea">Seja o máximo possível descritivo na mensagem:</label>
            </div>
        </div>
    </div>
    <div class="box-content p-2 mb-2">
        <div class="form-group">
            <label for="attachment" class="form-label">Desejar anexar arquivos?</label>
            <input type="file" class="form-control" name="attachment[]" id="attachment" multiple>
            <span class="fs-7" style="color: #8c8d8f;">Apenas arquivos no formato: <strong>JPEG/JPG/PNG, EXCEL e PDF.</strong></span>
        </div>
    </div>
</div>
<button class="btn btn-danger btn-sm-lg mb-2" id="create-btn" role="button">Criar Chamado</button>
<?php if ($message) : ?>
    <?=$message; ?>
<?php endif; ?>
