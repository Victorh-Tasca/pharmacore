<?php
// Inicia a sessão para controle de login
session_start();

$envFile = __DIR__ . '/../.env';

if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Ignora comentários (linhas que começam com #)
        if (strpos(trim($line), '#') === 0) {
            continue;
        }

        // Divide a linha em nome=valor
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            // Remove aspas (se existirem) do valor
            if (strlen($value) > 1 && $value[0] == '"' && $value[strlen($value) - 1] == '"') {
                $value = substr($value, 1, -1);
            }

            // Define a variável de ambiente para que getenv() funcione
            putenv("$name=$value");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Define o tipo de conteúdo da resposta como JSON
header("Content-Type: application/json; charset=UTF-8");

// Permite requisições de qualquer origem (em produção, restrinja ao seu domínio)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");