<?php $v->layout('_theme'); ?>
<?php if ($logged) :?>
<form method="POST" action="<?=$router->route('post.all.tickets'); ?>" class="ui form">
    <div class="ui two column grid">
        <div class="sixteen wide mobile ten wide computer column">
            <div class="ui segments">
                <div class="ui segment">
                    <h4>Relátorio de Chamados</h4>
                </div>
                <div class="ui secondary segment">
                    <div class="three fields">
                        <div class="field required">
                            <label for="first_day">Dia Inicial:</label>
                            <div class="ui input">
                                <input type="date" name="first_day" id="first_day" required min="2020-01-01"
                                    max="<?=dateFormat()->format('Y-m-d'); ?>" value="<?=$_POST['first_day'] ?? '' ?>">
                            </div>
                        </div>
                        <div class="field required">
                            <label for="last_day">Dia Final:</label>
                            <div class="ui input">
                                <input type="date" name="last_day" id="last_day" required min="2020-01-01"
                                    max="<?=dateFormat()->format('Y-m-d'); ?>" value="<?=$_POST['last_day'] ?? '' ?>">
                            </div>
                        </div>
                        <div class="field" style="margin-top: 25px;">
                            <button class="ui basic fluid button red">Gerar</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($tickets) : ?>
            <a class="ui labeled icon basic button" href="<?=str_replace(['{first}', '{last}'], [$_POST['first_day'], $_POST['last_day']], $router->route('get.all.csv')); ?>"
                target="_blank" rel="noopener noreferrer"><i class="file icon"></i> Exportar <strong>CSV</strong> do Relátorio de
                Chamados</a>
            <table class="ui collapsing table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Título</th>
                        <th>Setor</th>
                        <th>Cliente</th>
                        <th>Responsável</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tickets as $ticket) : ?>
                    <tr>
                        <td data-inverted="" data-tooltip="ID Artia: <?= $ticket->ID_ARTIA; ?>"
                            data-position="top center"><?=$ticket->TICKET_CHAMADO; ?></td>
                        <td data-inverted="" data-tooltip="<?=$ticket->CATEGORIA; ?>" data-position="top center">
                            <?=$ticket->TITULO; ?></td>
                        <td><?=mb_convert_case($ticket->SETOR, MB_CASE_TITLE, 'UTF-8'); ?></td>
                        <td><?=mb_convert_case($ticket->USUARIO, MB_CASE_TITLE, 'UTF-8'); ?></td>
                        <td><?=mb_convert_case($ticket->PROCFIT_USUARIO, MB_CASE_TITLE, 'UTF-8'); ?></td>
                        <td style="text-align: center;"><?=($ticket->ESTADO == 1 ? '<a class="ui green empty circular label"></a>' : '<a class="ui grey empty circular label"></a>'); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6">Total de Chamados: <strong><?=count($tickets); ?></strong></td>
                    </tr>
                </tfoot>
            </table>
            <?php endif; ?>
            <?php if ($message) : ?>
            <div class="ui message">
                <p><?=$message; ?></p>
            </div>
            <?php endif; ?>
        </div>
        <?=$v->insert('account/sidebar'); ?>
    </div>
</form>

<?php endif; ?>
