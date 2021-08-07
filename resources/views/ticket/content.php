<div class="box mb-2">
    <div class="box-header d-flex flex-column flex-wrap">
        <h4>
            Detalhes do Chamado: #<?=$ticket->ID_ARTIA ?> - <?=html_entity_decode($ticket->TITULO); ?>
        </h4>
        <div class="d-flex justify-content-between align-items-center">
            <span class="fs-7" style="color: #8c8d8f;">Historico de iterações</span>
            <span class="fs-7" style="color: #8c8d8f;">Utima Atualização: aaaa</span>
        </div>
    </div>
    <div class="box-content p-2">
        <?php $decode = json_decode($ticket->MENSAGEM); ?>
        <?php for ($i = 0; $i < 3; $i++) : ?>
        <div class="d-flex p-1 align-items-center border-bottom">
            <img class="avatar" src="http://localhost/helpdesk-dev/resources/images/user.png">
            <div class="d-flex flex-column flex-wrap">
                <div class="d-flex align-items-center">
                    <strong class="mx-2">Vinicius Alves</strong>
                    <div class="d-flex justify-content-between flex-fill">
                        <span class="mx-2 fs-7 d-none d-sm-block">Desenvolvimento</span>
                        <span class="fs-7" style="color: #8c8d8f;"><?=date('d/m à\s H:i:s'); ?></span>
                    </div>
                </div>
                <div class="flex-fill p-2">
                    <?=nl2br(html_entity_decode($decode->MESSAGE)); ?>
                </div>
                <div class="d-flex p-2">
                    <a href="#" class="fs-7 text-reset text-decoration-none">Anexo</a>
                    <a href="#" class="fs-7 text-reset text-decoration-none">Anexo</a>
                </div>
            </div>
        </div>
        <?php endfor; ?>
    </div>
    <div class="box-header border-top">Arquivos anexados:</div>
    <div class="box-content p-2">
        aaa
    </div>
    <div class="box-content p-2 border-top">
    <label for="message" class="form-label required">Responder:</label>
        <div class="form-floating mb-2">
            <textarea name="message" id="message" maxlength="3000" style="height: 150px;" class="form-control" required><?=old('message');?></textarea>
            <label for="floatingTextarea">Seja o máximo possível descritivo na mensagem:</label>
        </div>

        <div class="form-group mb-2">
            <label for="attachment" class="form-label">Desejar anexar arquivos?</label>
            <input type="file" class="form-control" name="attachment[]" id="attachment" multiple>
            <span class="fs-7" style="color: #8c8d8f;">Apenas arquivos no formato: <strong>JPEG/JPG/PNG, EXCEL e PDF.</strong></span>
        </div>

        <button class="btn btn-danger btn-lg">Responder</button>
    </div>
            
    
</div>
