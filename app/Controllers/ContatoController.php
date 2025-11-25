<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Models/Contato.php';
require_once __DIR__ . '/../Models/Estado.php';
require_once __DIR__ . '/../Models/Cidade.php';

require_once __DIR__ . '/../Helpers/validation.php';

class ContatoController
{
    private \PDO $db;
    private Contato $contatoModel;
    private Estado $estadoModel;
    private Cidade $cidadeModel;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();

        $this->contatoModel = new Contato($this->db);
        $this->estadoModel = new Estado($this->db);
        $this->cidadeModel = new Cidade($this->db);
    }

    public function index(): void
    {
        $filters = [
            'nome'     => $_GET['nome']     ?? null,
            'telefone' => $_GET['telefone'] ?? null,
            'cpf'      => $_GET['cpf']      ?? null,
            'bro_id'   => $_GET['bro_id']   ?? null,
            'bre_id'   => $_GET['bre_id']   ?? null,
        ];

        $contatos = $this->contatoModel->all($filters);
        $estados = $this->estadoModel->all();

        $cidades = [];
        if (!empty($filters['bro_id'])) {
            $cidades = $this->cidadeModel->allByEstado((int)$filters['bro_id']);
        }

        require __DIR__ . '/../../views/layouts/header.php';
        require __DIR__ . '/../../views/contatos/index.php';
        require __DIR__ . '/../../views/layouts/footer.php';
    }

    public function create(): void
    {
        $estados = $this->estadoModel->all();

        $cidades = [];

        $contato = [
            'con_id'       => null,
            'con_nome'     => '',
            'con_telefone' => '',
            'con_cpf'      => '',
            'bro_id'       => '',
            'bre_id'       => '',
        ];

        $isEdit = false;

        require __DIR__ . '/../../views/layouts/header.php';
        require __DIR__ . '/../../views/contatos/form.php';
        require __DIR__ . '/../../views/layouts/footer.php';
    }

    public function store(): void
    {
        $errors = [];

        $nome     = $_POST['con_nome']     ?? '';
        $telefone = $_POST['con_telefone'] ?? '';
        $cpfBruto = $_POST['con_cpf']      ?? '';
        $bro_id   = $_POST['bro_id']       ?? '';
        $bre_id   = $_POST['bre_id']       ?? '';

        $cpfLimpo = sanitize_cpf($cpfBruto);
        $telLimpo = sanitize_telefone($telefone);

        if (trim($nome) === '') {
            $errors[] = 'Nome é obrigatório.';
        }

        if ($telLimpo === '') {
            $errors[] = 'Telefone é obrigatório.';
        } elseif (!validar_telefone($telLimpo)) {
            $errors[] = 'Telefone informado é inválido.';
        }

        if (trim($cpfLimpo) === '') {
            $errors[] = 'CPF é obrigatório.';
        } elseif (!validar_cpf($cpfLimpo)) {
            $errors[] = 'CPF informado é inválido.';
        } else {
            if ($this->contatoModel->existsCpf($cpfLimpo)) {
                $errors[] = 'Já existe um contato cadastrado com este CPF.';
            }
        }

        if (empty($bro_id)) {
            $errors[] = 'Estado é obrigatório.';
        }

        if (empty($bre_id)) {
            $errors[] = 'Cidade é obrigatória.';
        }

        if (!empty($errors)) {
            $contato = [
                'con_id'       => null,
                'con_nome'     => $nome,
                'con_telefone' => $telefone,
                'con_cpf'      => $cpfBruto,
                'bro_id'       => $bro_id,
                'bre_id'       => $bre_id,
            ];

            $estados = $this->estadoModel->all();
            $cidades = [];

            if (!empty($bro_id)) {
                $cidades = $this->cidadeModel->allByEstado((int)$bro_id);
            }

            $isEdit = false;

            require __DIR__ . '/../../views/layouts/header.php';
            require __DIR__ . '/../../views/contatos/form.php';
            require __DIR__ . '/../../views/layouts/footer.php';
            return;
        }

        $data = [
            'con_nome'     => $nome,
            'con_telefone' => $telLimpo,
            'con_cpf'      => $cpfLimpo,
            'bro_id'       => $bro_id,
            'bre_id'       => $bre_id,
        ];

        $this->contatoModel->create($data);

        header('Location: ?action=index');
        exit;
    }


    public function edit(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id <= 0) {
            header('Location: ?action=index');
            exit;
        }

        $contato = $this->contatoModel->find($id);

        if (!$contato) {
            header('Location: ?action=index');
            exit;
        }

        $estados = $this->estadoModel->all();

        $cidades = [];
        if (!empty($contato['bro_id'])) {
            $cidades = $this->cidadeModel->allByEstado((int)$contato['bro_id']);
        }

        $isEdit = true;

        require __DIR__ . '/../../views/layouts/header.php';
        require __DIR__ . '/../../views/contatos/form.php';
        require __DIR__ . '/../../views/layouts/footer.php';
    }

    public function update(): void
    {
        $id = isset($_POST['con_id']) ? (int) $_POST['con_id'] : 0;

        if ($id <= 0) {
            header('Location: ?action=index');
            exit;
        }

        $errors = [];

        $nome     = $_POST['con_nome']     ?? '';
        $telefone = $_POST['con_telefone'] ?? '';
        $cpfBruto = $_POST['con_cpf']      ?? '';
        $bro_id   = $_POST['bro_id']       ?? '';
        $bre_id   = $_POST['bre_id']       ?? '';

        $cpfLimpo = sanitize_cpf($cpfBruto);
        $telLimpo = sanitize_telefone($telefone);

        if (trim($nome) === '') {
            $errors[] = 'Nome é obrigatório.';
        }

        if ($telLimpo === '') {
            $errors[] = 'Telefone é obrigatório.';
        } elseif (!validar_telefone($telLimpo)) {
            $errors[] = 'Telefone informado é inválido.';
        }

        if (trim($cpfLimpo) === '') {
            $errors[] = 'CPF é obrigatório.';
        } elseif (!validar_cpf_banco($cpfLimpo)) {
            $errors[] = 'CPF informado é inválido.';
        } else {
            if ($this->contatoModel->existsCpf($cpfLimpo, $id)) {
                $errors[] = 'Já existe outro contato cadastrado com este CPF.';
            }
        }

        if (empty($bro_id)) {
            $errors[] = 'Estado é obrigatório.';
        }

        if (empty($bre_id)) {
            $errors[] = 'Cidade é obrigatória.';
        }

        if (!empty($errors)) {
            $contato = [
                'con_id'       => $id,
                'con_nome'     => $nome,
                'con_telefone' => $telefone,
                'con_cpf'      => $cpfBruto,
                'bro_id'       => $bro_id,
                'bre_id'       => $bre_id,
            ];

            $estados = $this->estadoModel->all();
            $cidades = [];

            if (!empty($bro_id)) {
                $cidades = $this->cidadeModel->allByEstado((int)$bro_id);
            }

            $isEdit = true;

            require __DIR__ . '/../../views/layouts/header.php';
            require __DIR__ . '/../../views/contatos/form.php';
            require __DIR__ . '/../../views/layouts/footer.php';
            return;
        }

        $data = [
            'con_nome'     => $nome,
            'con_telefone' => $telLimpo,
            'con_cpf'      => $cpfLimpo,
            'bro_id'       => $bro_id,
            'bre_id'       => $bre_id,
        ];

        $this->contatoModel->update($id, $data);

        header('Location: ?action=index');
        exit;
    }

    public function delete(): void
    {
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

        if ($id > 0) {
            $this->contatoModel->delete($id);
        }

        header('Location: ?action=index');
        exit;
    }

    public function cidadesPorEstado(): void
    {
        header('Content-Type: application/json');

        if (empty($_GET['bro_id'])) {
            echo json_encode([]);
            exit;
        }

        $bro_id = (int) $_GET['bro_id'];

        $cidades = $this->cidadeModel->allByEstado($bro_id);

        echo json_encode($cidades);
        exit;
    }

    public function verificarCpf(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $cpfBruto = $_POST['cpf'] ?? '';
        $conId    = isset($_POST['con_id']) ? (int) $_POST['con_id'] : null;

        $cpfLimpo = sanitize_cpf($cpfBruto);

        if (trim($cpfLimpo) === '') {
            echo json_encode([
                'success' => false,
                'type'    => 'empty',
                'message' => 'Informe o CPF.'
            ]);
            exit;
        }

        if (!validar_cpf_banco($cpfLimpo)) {
            echo json_encode([
                'success' => false,
                'type'    => 'invalid',
                'message' => 'CPF inválido.'
            ]);
            exit;
        }

        $jaExiste = $this->contatoModel->existsCpf($cpfLimpo, $conId ?: null);

        if ($jaExiste) {
            echo json_encode([
                'success' => false,
                'type'    => 'exists',
                'message' => 'Já existe um contato cadastrado com este CPF.'
            ]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'type'    => 'ok',
            'message' => 'CPF válido e disponível.'
        ]);
        exit;
    }
}
