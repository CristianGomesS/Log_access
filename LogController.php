<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AccessLogService;
use Illuminate\Http\Request;

/**
 * Controlador responsável pela visualização e busca de logs de acesso.
 *
 * Este controller fornece endpoints para:
 * - Listar os logs formatados
 * - Buscar logs com filtros (nome do usuário, data, método, status, rota)
 */
class LogController extends Controller
{
    /**
     * Serviço de logs de acesso.
     *
     * @var AccessLogService
     */
    protected  $accessLogService;

    /**
     * Injeta o serviço via construtor.
     *
     * @param AccessLogService $accessLogService Serviço de manipulação de logs.
     */
    public function __construct(AccessLogService $accessLogService)
    {
        $this->accessLogService = $accessLogService;
    }

    /**
     * Retorna uma lista formatada dos logs de acesso.
     *
     * Rota: GET /api/logs/formatted
     *
     * @return \Illuminate\Http\JsonResponse Lista de logs com mensagens descritivas e data/hora.
     */
    public function getFormattedLogs()
    {
        $logs = $this->accessLogService->readLogsFromDatabase();

        return response()->json($logs);
    }

    /**
     * Retorna os logs filtrados por critérios passados na requisição.
     *
     * Rota: GET /api/logs/search
     *
     * Filtros aceitos:
     * - user_name: nome parcial do usuário
     * - start_date: data inicial (YYYY-MM-DD)
     * - end_date: data final (YYYY-MM-DD)
     * - method: método HTTP (GET, POST, etc.)
     * - status: código de status (200, 404, etc.)
     * - path: rota ou parte dela
     *
     * @param Request $request Requisição com filtros
     * @return \Illuminate\Http\JsonResponse Lista de logs filtrados
     */
    public function search(Request $request)
    {
        $filters = $request->only(['name', 'start_date', 'end_date', 'method', 'status', 'path']);

        $logs = $this->accessLogService->searchLogs($filters);

        return response()->json($logs);
    }
}
