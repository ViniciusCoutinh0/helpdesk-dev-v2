<div class="row">
    <div class="col-12 mb-2">
        <div class="box">
            <div class="box-header d-flex justify-content-between align-items-center">
                <span>Chamados Abertos</span>
                <a href="<?=url('ticket.all.state', ['state' => 1]); ?>" class="btn btn-dark btn-sm">Ver Todos</a>
            </div>
            <div class="box-content p-2">
                <?php if ($open) : ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($open as $item) : ?>
                    <li class="list-group-item d-flex flex-column justify-content-between align-items-start">
                        <a class="text-reset text-decoration-none" href="<?=url('ticket.show', ['id' => $item->TICKET_CHAMADO]); ?>">
                            <span class="badge bg-success"><i class="fas fa-solid fa-unlock"></i> Aberto</span>
                            #<?=$item->ID_ARTIA; ?> - <?=mb_convert_case($item->TITULO, MB_CASE_TITLE, 'UTF-8'); ?>
                        </a>
                        <span class="fs-7" style="color: #8c8d8f;">Departamento responsável:
                            <strong><?=mb_convert_case($item->DEPARTAMENTO, MB_CASE_TITLE, 'UTF-8'); ?></strong> -
                            Criado em:
                            <strong><?=date('d/m à\s H:i', strtotime($item->INICIALIZACAO)); ?></strong></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else : ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle"></i> Nenhum chamado em aberto no momento..
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-12 mb-2">
        <div class="box">
            <div class="box-header d-flex justify-content-between align-items-center">
                <span>Chamados Finalizados</span>
                <a href="<?=url('ticket.all.state', ['state' => 2]); ?>" class="btn btn-dark btn-sm">Ver Todos</a>
            </div>
            <div class="box-content p-2">
                <?php if ($closed) : ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($closed as $item) : ?>
                    <li class="list-group-item d-flex flex-column justify-content-between align-items-start">
                        <a class="text-reset text-decoration-none" href="<?=url('ticket.show', ['id' => $item->TICKET_CHAMADO]); ?>">
                            <span class="badge bg-danger"><i class="fas fa-solid fa-lock"></i> Fechado</span>
                            #<?=$item->ID_ARTIA; ?> - <?=mb_convert_case($item->TITULO, MB_CASE_TITLE, 'UTF-8'); ?>
                        </a>
                        <span class="fs-7" style="color: #8c8d8f;">Departamento responsável:
                            <strong><?=mb_convert_case($item->DEPARTAMENTO, MB_CASE_TITLE, 'UTF-8'); ?></strong> -
                            Finalizado em:
                            <strong><?=date('d/m à\s H:i', strtotime($item->FINALIZACAO_ARTIA)); ?></strong></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else : ?>
                <div class="alert alert-danger" role="alert">
                    <i class="fas fa-exclamation-circle"></i> Nenhum chamado em aberto no momento..
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
