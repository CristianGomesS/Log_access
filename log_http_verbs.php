<?php
/**
 * 🔄 MAPEAMENTO DE VERBOS HTTP PARA MENSAGENS DESCRITIVAS
 *
 * Este arquivo define como cada método HTTP será interpretado nos logs,
 * separando mensagens para sucesso ('true') e falha ('false').
 *
 * Como usar:
 * - Você pode alterar os textos de cada verbo para adaptar ao contexto do seu sistema.
 * - Se algum verbo não estiver listado, será usado o grupo "default".
 */

return [
    'GET' => [
        'true' => 'consultou',
        'false' => 'tentou consultar',
    ],
    'POST' => [
        'true' => 'criou',
        'false' => 'tentou criar',
    ],
    'PUT' => [
        'true' => 'atualizou',
        'false' => 'tentou atualizar',
    ],
    'PATCH' => [
        'true' => 'atualizou parcialmente',
        'false' => 'tentou atualizar parcialmente',
    ],
    'DELETE' => [
        'true' => 'removeu',
        'false' => 'tentou remover',
    ],
    'default' => [
        'true' => 'executou uma ação desconhecida',
        'false' => 'tentou executar uma ação desconhecida',
    ]
];
