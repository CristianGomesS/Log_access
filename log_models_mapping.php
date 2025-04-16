<?php
/**
 * 🧩 MAPEAMENTO DE ROTAS PARA MODELOS ELOQUENT
 *
 * Este arquivo define quais modelos devem ser utilizados para capturar
 * o estado de um recurso antes de alterações (PUT, DELETE, PATCH).
 *
 * Como usar:
 * - Agrupe por tipo de entidade (users, profiles, etc).
 * - Use rotas com {id} e informe: model + primary_key.
 */

return [

    'users' => [
        'api/users/{id}' => [
            'model' => App\Models\User::class,
            'table' => 'users',
            'primary_key' => 'id',
        ],
    ],

    'profiles' => [
        'api/profile/update/{id}' => [
            'model' => App\Models\Profile::class,
            'table' => 'profiles',
            'primary_key' => 'id',
        ],
    ],

    // Usado como fallback caso não encontre uma rota correspondente
    'default' => [
        '' => [
            'model' => App\Models\User::class,
            'table' => 'users',
            'primary_key' => 'id',
        ],
    ],
];
