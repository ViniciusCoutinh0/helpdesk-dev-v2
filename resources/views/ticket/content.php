<div class="box mb-2">
    <div class="box-header d-flex flex-column flex-wrap">
        <h4>
            Detalhes do Chamado: #<?=$ticket->ID_ARTIA ?> - <?=html_entity_decode($ticket->TITULO); ?>
        </h4>
        <div class="d-flex justify-content-between align-items-center">
            <span class="fs-7" style="color: #8c8d8f;">Histórico</span>
            <?php if ($ticket->ATUALIZACAO) : ?>
                <span class="fs-7" style="color: #8c8d8f;">Primeira Resposta em: <?=date('d/m á\s H:i', strtotime($ticket->ATUALIZACAO));  ?></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="box-content p-2">
        <?php $decode = json_decode($ticket->MENSAGEM); ?>
        <div class="d-flex p-1 align-items-start border-bottom">
            <img class="avatar" src="<?=asset('storage/avatar/' . mb_strtoupper($ticket->USUARIO[0]) . '.png'); ?>" alt="avatar.png">
            <div class="d-flex flex-column flex-wrap">
                <div class="d-flex justify-content-start align-items-center">
                    <strong class="mx-2"><?=mb_convert_case($ticket->USUARIO, MB_CASE_TITLE, 'UTF-8'); ?></strong>
                    <div class="d-flex justify-content-start flex-fill">
                        <span class="mx-2 fs-7 d-none d-sm-block" style="color: #8c8d8f;">
                            <?=mb_convert_case($ticket->SETOR, MB_CASE_TITLE, 'UTF-8'); ?>
                        </span>
                        <span class="fs-7" style="color: #8c8d8f;"><?=date('d/m à\s H:i:s', strtotime($ticket->INICIALIZACAO)); ?></span>
                    </div>
                </div>
                <div class="p-2">
                   <p class="text-break"> 
                       <?=nl2br(html_entity_decode($decode->MESSAGE)); ?>
                    </p>
                </div>
            </div>
        </div>
        <?php if ($commits) : ?>
            <?php foreach ($commits as $commit) : ?>
            <div class="d-flex p-1 align-items-start border-bottom">
            <img class="avatar" src="<?=asset('storage/avatar/' . mb_strtoupper($commit->USUARIO[0]) . '.png'); ?>" alt="avatar.png">
            <div class="d-flex flex-column flex-wrap w-100">
                <div class="d-flex justify-content-start align-items-center">
                    <strong class="mx-2"><?=mb_convert_case($commit->USUARIO, MB_CASE_TITLE, 'UTF-8'); ?></strong>
                    <div class="d-flex justify-content-start flex-fill">
                        <span class="mx-2 fs-7 d-none d-sm-block" style="color: #8c8d8f;">
                            <?=mb_convert_case($commit->SETOR, MB_CASE_TITLE, 'UTF-8'); ?>
                        </span>
                        <span class="fs-7" style="color: #8c8d8f;"><?=date('d/m à\s H:i:s', strtotime($commit->INICIALIZACAO)); ?></span>
                    </div>
                </div>
                <div class="p-2">
                   <p class="text-break"> 
                       <?=nl2br(html_entity_decode($commit->COMENTARIO)); ?>
                    </p>
                </div>
                <?php if ($commit->ENDERECO) : ?>
                    <?php $explode = explode('&', $commit->ENDERECO);
                    $clear = array_filter($explode); ?>
                    <div class="d-flex flex-wrap p-2">
                    <?php foreach ($clear as $item) : ?>
                        <a href="<?=defaultUrl() . $item; ?>" class="fs-7 text-reset text-decoration-none" target="_blank" rel="noopener noreferrer" style="margin-right: .5rem;">
                        <i class="fas fa-solid fa-file"></i> Anexo
                        </a>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="box-header border-top">Arquivos anexados:</div>
    <div class="box-content p-2">
        <?php if ($attachments) : ?>
            <?php foreach ($attachments as $attachment) : ?>
                <div class="d-flex flex-column flex-wrap p-2 border-bottom">
                    <a class="text-reset text-decoration-none" href="<?=defaultUrl() . $attachment->ENDERECO;?>" target="_blank" rel="noopener noreferrer"><i class="fa fa-solid fa-file"></i> Anexo</a>
                    <span class="fs-7" style="color: #8c8d8f;">
                        Enviado Por: <?=mb_convert_case($attachment->USUARIO, MB_CASE_TITLE, 'UTF-8'); ?> ás <?=date('d/m á\s H:i', strtotime($attachment->ENVIADO)); ?>
                    </span>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div class="alert alert-info" role="alert">
            <i class="fas fa-exclamation-circle"></i>  Nenhum arquivo anexado no chamado.
            </div>
        <?php endif; ?>
    </div>
    <?php if ($ticket->ESTADO == 1) : ?>
    <div class="box-content p-2 border-top">
    <input type="hidden" name="csrf_token" value="<?=csrf_token(); ?>">
    <label for="message" class="form-label required">Responder:</label>
        <div class="form-floating mb-2">
            <textarea name="message" id="message" maxlength="3000" style="height: 150px;" class="form-control" required><?=old('message');?></textarea>
            <label for="floatingTextarea">Seja o máximo possível descritivo na mensagem:</label>
        </div>
        <div class="form-group mb-2">
            <label for="attachment" class="form-label">Desejar anexar arquivos?</label>
            <input type="file" class="form-control" name="files[]" id="files" multiple>
            <span class="fs-7" style="color: #8c8d8f;">Apenas arquivos no formato: <strong>JPEG/JPG/PNG, EXCEL e PDF.</strong></span>
        </div>
        <button class="btn btn-danger btn-lg" id="commit-btn">Responder</button>
    </div>
    <?php endif; ?>
</div>
<?php if ($message) : ?>
    <?=$message; ?>
<?php endif; ?>
