<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/Controllers/ContatoController.php';

$action = $_GET['action'] ?? 'index';

$controller = new ContatoController();

switch ($action) {
    case 'create':
        $controller->create();
        break;

    case 'store':
        $controller->store();
        break;

    case 'edit':
        $controller->edit();
        break;

    case 'update':
        $controller->update();
        break;

    case 'delete':
        $controller->delete();
        break;

    case 'verificarCpf':
        $controller->verificarCpf();
        break;

    case 'cidadesPorEstado':
        $controller->cidadesPorEstado();
        break;

    default:
        $controller->index(); // listagem
        break;
}
