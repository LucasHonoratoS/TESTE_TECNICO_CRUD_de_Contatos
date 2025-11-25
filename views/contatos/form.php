<?php
$contato = $contato ?? [
    'con_id'       => null,
    'con_nome'     => '',
    'con_telefone' => '',
    'con_cpf'      => '',
    'bro_id'       => '',
    'bre_id'       => '',
];

$isEdit = $isEdit ?? false;

$formAction = $isEdit ? '?action=update' : '?action=store';
$pageTitle = $isEdit ? 'Editar Contato' : 'Novo Contato';
?>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <strong>Ops!</strong> Encontramos os seguintes problemas:
                <ul class="mb-0">
                    <?php foreach ($errors as $erro): ?>
                        <li><?= htmlspecialchars($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 m-0"><?= $pageTitle ?></h1>
            <a href="?action=index" class="btn btn-outline-secondary">Voltar</a>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="<?= $formAction ?>" id="form_contato" novalidate>
                    <?php if ($isEdit && !empty($contato['con_id'])): ?>
                        <input type="hidden" name="con_id" value="<?= (int)$contato['con_id'] ?>">
                    <?php endif; ?>
                    <div class="mb-3">
                        <label class="form-label">Nome <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            name="con_nome"
                            class="form-control"
                            required
                            maxlength="255"
                            value="<?= htmlspecialchars($contato['con_nome']) ?>"
                        >
                        <div class="form-text">Informe o nome completo do contato.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefone <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            name="con_telefone"
                            class="form-control"
                            required
                            maxlength="15"
                            data-mask="telefone"
                            value="<?= htmlspecialchars($contato['con_telefone']) ?>"
                        >
                        <div class="form-text">
                            Aceitar fixo ou celular, com ou sem 9º dígito. Ex: (11) 98765-4321
                        </div>
                        <div id="telefone_feedback" class="form-text"></div> <!-- NOVO -->
                    </div>
                    <div class="mb-3">
                        <label class="form-label">CPF <span class="text-danger">*</span></label>
                        <input
                            type="text"
                            name="con_cpf"
                            class="form-control"
                            required
                            maxlength="14"
                            data-mask="cpf"
                            value="<?= htmlspecialchars($contato['con_cpf']) ?>"
                        >
                        <div class="form-text">
                            CPF no formato 000.000.000-00.
                        </div>
                        <div id="cpf_feedback" class="form-text"></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Estado <span class="text-danger">*</span></label>
                            <select name="bro_id" id="select_estado" class="form-select" required>
                                <option value="">-- Selecione --</option>

                                <?php foreach ($estados as $estado): ?>
                                    <option
                                        value="<?= $estado['bro_id'] ?>"
                                        <?= ($contato['bro_id'] == $estado['bro_id']) ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($estado['bro_nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cidade <span class="text-danger">*</span></label>
                            <select name="bre_id" id="select_cidade" class="form-select" required>
                                <option value="">-- Selecione --</option>
                                <?php foreach ($cidades as $cidade): ?>
                                    <option
                                        value="<?= $cidade['bre_id'] ?>"
                                        <?= ($contato['bre_id'] == $cidade['bre_id']) ? 'selected' : '' ?>
                                    >
                                        <?= htmlspecialchars($cidade['bre_nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-3">
                        <button type="submit" class="btn btn-primary">
                            <?= $isEdit ? 'Salvar alterações' : 'Cadastrar' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
