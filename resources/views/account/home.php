<div class="sixteen wide mobile ten wide computer column">
    <table class="ui compact table">
        <thead class="bg-none bg-red">
            <tr>
                <th colspan="2">
                    <h4>Informações da Conta.</h4>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Nome de Usuário</td>
                <td><strong> <?=$user->Username; ?></strong></td>
            </tr>
            <tr>
                <td>Email</td>
                <td><strong><?=$user->Email; ?></strong></td>
            </tr>
            <tr>
                <td>Setor do Usuário</td>
                <td><strong><?=$user->getUserSector()->Name; ?></strong></td>
            </tr>
            <tr>
                <td colspan="2">
                    <h4>Permissões de <?=$user->getUserSector()->Name; ?></h4>
                </td>
            </tr>
            <tr>
                <td>Criar</td>
                <td><?=($user->getUserRules()->Rule_Create == 'S' ? '<i class="check icon">' : '<i class="close icon">'); ?>
                </td>
            </tr>
            <tr>
                <td>Visualizar</td>
                <td><?=($user->getUserRules()->Rule_Read == 'S' ? '<i class="check icon">' : '<i class="close icon">'); ?>
                </td>
            </tr>
            <tr>
                <td>Editar</td>
                <td><?=($user->getUserRules()->Rule_Update == 'S' ? '<i class="check icon">' : '<i class="close icon">'); ?>
                </td>
            </tr>
            <tr>
                <td>Excluir</td>
                <td><?=($user->getUserRules()->Rule_Delete == 'S' ? '<i class="check icon">' : '<i class="close icon">'); ?>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td>Conta criada em</td>
                <td><?=dateFormat($user->Created_at)->format('d/m à\s H:i:s'); ?></td>
            </tr>
        </tfoot>
    </table>
</div>