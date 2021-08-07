<div class="box mb-2">
    <div class="box-content p-2">
        <div class="form-group">
            <input type="text" name="words" id="words" class="form-control form-control-lg" placeholder="Informe o assunto.."
                autocomplete="off" required>
        </div>
        <div class="ui fluid category search">
            <div class="results" id="results"></div>
        </div>
    </div>
    <div class="box-header border-top"><i class="fas fa-info-circle" style="color: #8c8d8f;"></i> Informações Adicionais</div>
    <div class="box-content p-2">
        <?php if ($sector->Name === 'Suporte T.i') : ?>
        <div class="form-group mb-2">
            <?php if ($sectors) : ?>
            <label for="section_user" class="form-label required">Usuário:</label>
            <select name="section_user" id="section_user" class="form-select" required>
                <?php foreach ($sectors as $item) : ?>
                <option value="<?=$item->Sector; ?>:<?=$item->Framework_User; ?>">
                    <?=mb_convert_case($item->Username, MB_CASE_TITLE, 'UTF-8'); ?> - <?=mb_convert_case($item->Sector, MB_CASE_TITLE, 'UTF-8'); ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>
        </div>
        <div class="form-check">
            <input type="checkbox" name="on_duty" id="on_duty" class="form-check-input">
            <label for="on_duty" class="form-check-label">Plantão?</label>
        </div>
        <?php else : ?>
        <div class="form-group mb-2">
            <label for="section" class="form-label required">Setor:</label>
            <select name="section" id="section" class="form-select" required>
                <option value="<?=$sector->Name;?>" selected><?=mb_convert_case($sector->Name, MB_CASE_TITLE, 'UTF-8') ?></option>
            </select>
        </div>
        <?php endif; ?>
        <?php if ($sector->Name === 'Lojas') : ?>
        <div class="form-group mb-2">
            <label for="employee" class="form-label required">Nº Balconista:</label>
            <input type="number" name="employee" id="employee" class="form-control" maxlength="4" placeholder="Número do Balconista" autocomplete="off" required>
        </div>
        <div class="form-group">
            <label for="computer" class="form-label">Acesso Remoto:</label>
            <input type="text" name="computer" id="computer" class="form-control" autocomplete="off">
        </div>
        <?php endif; ?>
    </div>
</div>

