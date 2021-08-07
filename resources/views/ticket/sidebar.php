<div class="box mb-2">
    <div class="box-header d-flex flex-wrap align-items-center">
        <img class="avatar" src="<?=asset('resources/images/departamentos/' . $ticket->DEPARTAMENTO . '.jpg'); ?>"
            alt="<?=$ticket->DEPARTAMENTO; ?>.jpg">
        <div class="d-flex flex-column p-1">
            <span><?=mb_convert_case($ticket->DEPARTAMENTO, MB_CASE_TITLE, 'UTF-8'); ?></span>
            <span class="fs-7" style="color: #8c8d8f;">Departamento Responsável pelo chamado.</span>
        </div>
    </div>
    <div class="box-content p-2">
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                Departamento: <strong><?=mb_convert_case($ticket->DEPARTAMENTO, MB_CASE_TITLE, 'UTF-8');?></strong>
            </li>
            <li class="list-group-item">
                Categoria: <strong> <?=mb_convert_case($ticket->CATEGORIA, MB_CASE_TITLE, 'UTF-8');?></strong>
            </li>
            <li class="list-group-item">
                Responsável: <strong><?=mb_convert_case($ticket->PROC_NOME, MB_CASE_TITLE, 'UTF-8'); ?></strong>
            </li>
            <li class="list-group-item">
                Criado em: <strong><?=date('d/m à\s H:i', strtotime($ticket->INICIALIZACAO)); ?></strong>
            </li>
            <li class="list-group-item">
                Prazo Estimado: <strong><?=date('d/m à\s H:i', strtotime($ticket->PRAZO_ARTIA)); ?></strong>
            </li>
            <?php if ($ticket->ESTADO == 2) : ?>
                <?php if (is_null($ticket->FINALIZACAO_ARTIA)) : ?>
                    <li class="list-group-item">
                        Finalizado em: <strong><?=date('d/m à\s H:i', strtotime($ticket->FINALIZACAO)); ?></strong>
                    </li>
                <?php else : ?>
                    <li class="list-group-item">
                    Finalizado em: <strong><?=date('d/m à\s H:i', strtotime($ticket->FINALIZACAO_ARTIA)); ?></strong>
                    </li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
    </div>
    <div class="box-header border-top">
        Cliente
    </div>
    <div class="box-content p-2">
        <ul class="list-group list-group-flush">
            <li class="list-group-item">
                Cliente: <strong><?=mb_convert_case($ticket->USUARIO, MB_CASE_TITLE, 'UTF-8');?></strong>
            </li>
            <li class="list-group-item">
                Acesso Remoto: <strong> <?=mb_convert_case($ticket->COMPUTADOR, MB_CASE_TITLE, 'UTF-8');?></strong>
            </li>
        </ul>
    </div>
    <?php $decode = json_decode($ticket->MENSAGEM); ?>
    <?php if (isset($decode->FIELDS)) : ?>
    <div class="box-header border-top">
        Informações Complementares
    </div>
    <div class="box-content p-2">
        <ul class="list-group list-group-flush">
            <?php foreach ($decode->FIELDS as $field) : ?>
            <li class="list-group-item">
                <span class="required"><?=mb_convert_case($field->FIELD_NAME, MB_CASE_TITLE, 'UTF-8');?></span>: <strong><?=mb_convert_case(html_entity_decode($field->FIELD_VALUE), MB_CASE_TITLE, 'UTF-8');?></strong>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>
    <?php if ($ticket->ESTADO == 1) : ?>
    <div class="box-header bg-success text-light p-3 border">
        Aberto
    </div>
    <?php else : ?>
    <div class="box-header bg-danger text-light p-3 border">
        Fechado
    </div>
    <?php endif; ?>
</div>
