<?php

function sanitize_cpf(string $cpf): string
{
    return preg_replace('/\D/', '', $cpf);
}

function validar_cpf_banco(string $cpf): bool
{
    $cpf = sanitize_cpf($cpf);

    if (strlen($cpf) !== 11) {
        return false;
    }

    if (preg_match('/^(\\d)\\1{10}$/', $cpf)) {
        return false;
    }

    $sum = 0;
    for ($i = 0, $peso = 10; $i < 9; $i++, $peso--) {
        $sum += (int)$cpf[$i] * $peso;
    }

    $resto = $sum % 11;
    $digito1 = ($resto < 2) ? 0 : 11 - $resto;

    if ($digito1 !== (int)$cpf[9]) {
        return false;
    }

    $sum = 0;
    for ($i = 0, $peso = 11; $i < 10; $i++, $peso--) {
        $sum += (int)$cpf[$i] * $peso;
    }

    $resto = $sum % 11;
    $digito2 = ($resto < 2) ? 0 : 11 - $resto;

    if ($digito2 !== (int)$cpf[10]) {
        return false;
    }

    return true;
}

function validar_cpf_view(string $cpf): string
{
    $cpf = preg_replace('/\D/', '', $cpf);

    if (strlen($cpf) !== 11) {
        return $cpf;
    }

    return substr($cpf, 0, 3) . '.' .
           substr($cpf, 3, 3) . '.' .
           substr($cpf, 6, 3) . '-' .
           substr($cpf, 9, 2);
}

function sanitize_telefone(string $telefone): string
{
    return preg_replace('/\D/', '', $telefone);
}

function validar_telefone_banco(string $telefone): bool
{
    $tel = sanitize_telefone($telefone);

    if (strlen($tel) > 11 && substr($tel, 0, 2) === '55') {
        $tel = substr($tel, 2);
    }

    $len = strlen($tel);

    if ($len !== 10 && $len !== 11) {
        return false;
    }

    if (substr($tel, 0, 1) === '0') {
        return false;
    }

    return true;
}

function validar_telefone_view(string $telefone): string
{
    $tel = sanitize_telefone($telefone);

    if (strlen($tel) > 11 && substr($tel, 0, 2) === '55') {
        $tel = substr($tel, 2);
    }

    $len = strlen($tel);

    if ($len === 10) {
        return sprintf(
            '(%s) %s-%s',
            substr($tel, 0, 2),
            substr($tel, 2, 4),
            substr($tel, 6, 4)
        );
    }

    if ($len === 11) {
        return sprintf(
            '(%s) %s-%s',
            substr($tel, 0, 2),
            substr($tel, 2, 5),
            substr($tel, 7, 4)
        );
    }

    return $telefone;
}
