<?php
// views/contatos/index.php
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 m-0">Contatos</h1>
    <a href="?action=create" class="btn btn-primary">Novo Contato</a>
</div>
<form method="GET" class="card p-3 mb-4 shadow-sm">
    <input type="hidden" name="action" value="index">

    <div class="row g-3 align-items-end">

        <!-- Nome -->
        <div class="col-12 col-md-2">
            <label class="form-label">Nome</label>
            <input 
                type="text" 
                name="nome" 
                class="form-control"
                value="<?= htmlspecialchars($_GET['nome'] ?? '') ?>"
            >
        </div>

        <!-- Telefone -->
        <div class="col-12 col-md-2">
            <label class="form-label">Telefone</label>
            <input 
                type="text" 
                name="telefone"
                class="form-control"
                value="<?= htmlspecialchars($_GET['telefone'] ?? '') ?>"
            >
        </div>

        <!-- CPF -->
        <div class="col-12 col-md-2">
            <label class="form-label">CPF</label>
            <input 
                type="text" 
                name="cpf"
                class="form-control"
                value="<?= validar_cpf_view($_GET['cpf'] ?? '') ?>"
            >
        </div>

        <!-- Estado -->
        <div class="col-12 col-md-3">
            <label class="form-label">Estado</label>
            <select name="bro_id" class="form-select" id="select_estado">
                <option value="">-- Todos --</option>

                <?php foreach ($estados as $estado): ?>
                    <option 
                        value="<?= $estado['bro_id'] ?>"
                        <?= (($_GET['bro_id'] ?? '') == $estado['bro_id']) ? 'selected' : '' ?>
                    >
                        <?= $estado['bro_nome'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Cidade -->
        <div class="col-12 col-md-3">
            <label class="form-label">Cidade</label>
            <select name="bre_id" class="form-select" id="select_cidade">
                <option value="">-- Todas --</option>

                <?php foreach ($cidades as $cidade): ?>
                    <option 
                        value="<?= $cidade['bre_id'] ?>"
                        <?= (($_GET['bre_id'] ?? '') == $cidade['bre_id']) ? 'selected' : '' ?>
                    >
                        <?= $cidade['bre_nome'] ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Botões de filtro -->
        <div class="col-12 d-flex gap-2 mt-2">
            <button class="btn btn-success">Filtrar</button>
            <a href="?action=index" class="btn btn-secondary">Limpar</a>
        </div>

    </div>
</form>
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered m-0">
            <thead class="table-dark">
                <tr>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>CPF</th>
                    <th>Estado</th>
                    <th>Cidade</th>
                    <th width="150">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($contatos)): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">Nenhum contato encontrado</td>
                </tr>
                <?php else: ?>
                <?php foreach ($contatos as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['con_nome']) ?></td>
                        <td><?= validar_telefone_view($c['con_telefone']) ?></td>
                        <td><?= validar_cpf_view($c['con_cpf']) ?></td>
                        <td><?= htmlspecialchars($c['bro_nome']) ?></td>
                        <td><?= htmlspecialchars($c['bre_nome']) ?></td>
                        <td class="text-center">
                            <a href="?action=edit&id=<?= $c['con_id'] ?>" class="btn btn-sm btn-primary">
                                Editar
                            </a>
                            <a 
                                href="?action=delete&id=<?= $c['con_id'] ?>"
                                class="btn btn-sm btn-danger"
                                onclick="return confirm('Deseja realmente excluir?')"
                            >
                                Excluir
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
