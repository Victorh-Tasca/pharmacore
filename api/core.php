<?php
// Inicia a sessão para controle de login
session_start();

// Define o tipo de conteúdo da resposta como JSON
header("Content-Type: application/json; charset=UTF-8");

// Permite requisições de qualquer origem (em produção, restrinja ao seu domínio)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");