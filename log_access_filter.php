<?php
/**
 * 🔐 CONFIGURAÇÃO DE CONTROLE DE ACESSO AO LOG
 *
 * Este arquivo define três conjuntos de regras:
 *
 * 1. 'routes_block_display' → Rotas que **não devem ser exibidas** em listagens visuais de logs (como menus ou painéis).
 * 2. 'routes_permission' → Rotas que exigem **verificação extra de permissão** antes do acesso.
 * 3. 'allowed_profiles' → Lista de perfis de usuário (por ID) autorizados a gravar logs.
 *
 * Como usar:
 * - Adicione aqui as rotas específicas do seu sistema conforme necessidade.
 * - Os nomes das rotas devem ser caminhos exatos da URL, podendo conter {id}.
 */

return [

    // Exemplo: estas rotas NÃO devem aparecer nos logs visuais
    'routes_block_display' => [
        'api/auth/logout',
        'api/auth/me',
        'api/dashboard',
    ],

    // Exemplo: estas rotas exigem validação de permissão extra
    'routes_permission' => [
        'api/logs/formatted',
        'api/logs/search',
    ],

    // Apenas perfis com ID listado aqui poderão registrar logs
    'allowed_profiles' => [
        1, // Administrador
        // 2, // Coordenador
    ],
];
