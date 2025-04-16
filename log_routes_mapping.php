<?php
/**
 * 📚 MAPEAMENTO DE ROTAS PARA DESCRIÇÕES HUMANAS
 *
 * Este arquivo serve para traduzir rotas + verbos em mensagens legíveis.
 *
 * Como usar:
 * - Crie grupos por entidade (ex: 'users', 'profiles').
 * - Cada rota contém os métodos e sua respectiva descrição.
 */

return [

    'users' => [
        'api/users' => [
            'GET' => 'todos os usuários',
            'POST' => 'um novo usuário',
        ],
        'api/users/{id}' => [
            'GET' => 'detalhes de um usuário',
            'PUT' => 'um usuário atualizado',
            'DELETE' => 'um usuário deletado',
        ],
    ],

    'profiles' => [
        'api/profile' => [
            'GET' => 'todos os perfis',
            'POST' => 'um novo perfil',
        ],
        'api/profile/update/{id}' => [
            'PUT' => 'um perfil atualizado',
        ],
    ],

    'logs' => [
        'api/logs/formatted' => [
            'GET' => 'todos os logs formatados',
        ],
        'api/logs/search' => [
            'GET' => 'pesquisa de logs',
        ],
    ],

    'default' => [
        'api/dashboard' => [
            'GET' => 'painel do sistema',
        ],
    ],
];
