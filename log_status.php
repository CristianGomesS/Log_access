<?php
/**
 * 📊 CÓDIGOS HTTP E SUAS INTERPRETAÇÕES
 *
 * Este arquivo traduz códigos HTTP para mensagens legíveis e status lógico (true = sucesso).
 *
 * Como usar:
 * - Adicione aqui qualquer código adicional do seu sistema.
 * - A mensagem será usada nos logs e auditorias.
 */

return [
    '200' => ['message' => 'sucesso', 'status' => true],
    '201' => ['message' => 'criado com sucesso', 'status' => true],
    '204' => ['message' => 'sem conteúdo', 'status' => true],
    '400' => ['message' => 'requisição inválida', 'status' => false],
    '401' => ['message' => 'não autorizado', 'status' => false],
    '403' => ['message' => 'acesso proibido', 'status' => false],
    '404' => ['message' => 'não encontrado', 'status' => false],
    '422' => ['message' => 'erro de validação', 'status' => false],
    '500' => ['message' => 'erro interno do servidor', 'status' => false],
];
