<?php

namespace App\Services;

use App\Models\AccessLog;
use App\Models\Unity;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Classe responsável pela manipulação de logs de acesso.
 *
 * Essa classe oferece métodos para salvar, filtrar, ler e formatar logs de requisições HTTP.
 */
class AccessLogService
{
    /**
     * Salva um log de acesso no banco de dados.
     *
     * @param int $id_user           ID do usuário que fez a requisição
     * @param string $requestPath    Caminho da requisição (ex: api/users)
     * @param string $requestMethod  Método HTTP (GET, POST, etc.)
     * @param string $userIp         IP do usuário
     * @param int $httpStatus        Código de status da resposta (ex: 200, 404)
     * @param string $requestAll     Corpo da requisição (dados enviados)
     * @param string $response       Corpo da resposta
     * @param string|null $before_log Estado do recurso antes da requisição (em caso de PUT, DELETE, PATCH)
     */
    public function getLogDatabase($id_user, $requestPath, $requestMethod, $userIp, $httpStatus, $requestAll, $response, $before_log)
    {
        AccessLog::create([
            'id_user'       => $id_user,
            'path'          => $requestPath,
            'method'        => $requestMethod,
            'ip'            => $userIp,
            'status'        => $httpStatus,
            'request_log'   => $requestAll,
            'response_log'  => $response,
            'before_log'    => $before_log
        ]);
    }

    /**
     * Retorna os logs formatados (para exibição).
     *
     * @return array Logs formatados com descrição e data.
     */
    public function readLogsFromDatabase()
    {
        $logs = AccessLog::orderBy('created_at', 'desc')->paginate(50);
        return $this->logsFormat($logs);
    }

    /**
     * Formata os logs para leitura, buscando nome do usuário e da unidade.
     *
     * @param mixed $logs Logs paginados
     * @return array Lista de logs formatados
     */
    public function logsFormat($logs)
    {
        $formatted = [];

        foreach ($logs as $log) {
            // Ignora logs de rotas bloqueadas para exibição
            if ($this->routeLogFilter($log->path)) continue;

            // Tenta recuperar usuário e unidade
            $user  = User::find($log->id_user);
            $unity = $user ? Unity::find($user->unity_id) : null;

            // Descrições com base em status, método e rota
            $statusDesc    = $this->getStatus((string) $log->status);
            $verbDesc      = $this->getVerb($log->method, $statusDesc);
            $routeDesc     = $this->getRoute($log->path, $log->method);
            $responseDesc  = $this->getResponseDescription($log->method, json_decode($log->response_log), $log->status);
            $dateTime      = $log->created_at->format('d/m/Y \à\s H\hi');

            $formatted[] = [
                'message'  => $this->returnLogComplete($user, $unity, $statusDesc, $verbDesc, $routeDesc, $responseDesc),
                'dateTime' => $dateTime,
            ];
        }

        return $formatted;
    }

    /**
     * Gera uma frase completa descritiva do log.
     */
    public function returnLogComplete($user, $unity, $statusDescription, $verbDescription, $routeDescription, $responseDescription)
    {
        $userName  = $user->name_user ?? 'desconhecido';
        $unityName = $unity->unity_name ?? 'desconhecido';

        return "O(A) {$userName} do(a) {$unityName}, {$verbDescription} {$routeDescription} {$responseDescription}";
    }

    /**
     * Verifica se uma rota está bloqueada para exibição (log oculto).
     */
    public function routeLogFilter($path)
    {
        $filter = config('log_access_filter.routes_block_display');

        foreach ($filter as $route) {
            $pattern = preg_replace('/\{[^}]+\}/', '[\d]+', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retorna descrição de ação com base no método HTTP.
     */
    public function getVerb($method, $status)
    {
        $verbs = config('log_http_verbs');
        $verb  = $verbs[$method] ?? $verbs['default'];
        $key   = $status['status'] ? 'true' : 'false';

        return $verb[$key] ?? ($status['status'] . '-' . $status['code']);
    }

    /**
     * Retorna descrição do status HTTP.
     */
    public function getStatus($code)
    {
        $list = config('log_status');

        return isset($list[$code])
            ? array_merge($list[$code], ['code' => $code])
            : ['message' => 'status desconhecido', 'status' => false, 'code' => $code];
    }

    /**
     * Retorna a descrição da rota e ação com base no método HTTP.
     */
    public function getRoute($path, $method)
    {
        $segments = explode('/', $path);
        $keyGroup = $segments[1] ?? 'default';

        $mapping = config("log_routes_mapping.$keyGroup") ?? config("log_routes_mapping.default");

        foreach ($mapping as $route => $verbs) {
            $pattern = preg_replace('/\{[^}]+\}/', '[\d]+', $route);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $path) && isset($verbs[$method])) {
                return $verbs[$method];
            }
        }

        return 'rota desconhecida';
    }

    /**
     * Define a descrição da resposta com base no método e status.
     */
    public function getResponseDescription($method, $response, $status)
    {
        return $this->responseVerificationDefault($response, $method, $status);
    }

    /**
     * Define comportamento padrão para verificar a resposta.
     */
    public function responseVerificationDefault($response, $method, $status)
    {
        if ($status >= 400) return 'sem sucesso.';

        $default = $response->message ?? 'com sucesso.';

        switch (strtoupper($method)) {
            case 'GET':
            case 'POST':
            case 'PUT':
            case 'DELETE':
                return $default;
            default:
                return 'verbo desconhecido';
        }
    }

    /**
     * Aplica filtros nos logs conforme os parâmetros recebidos.
     */
    public function searchLogs(array $filters)
    {
        $query = AccessLog::query()
            ->join('users', 'access_logs.id_user', '=', 'users.id')
            ->select('access_logs.*', 'users.name_user as user_name');

        // Filtros
        if (!empty($filters['user_name'])) {
            $query->where('users.name', 'LIKE', '%' . $filters['user_name'] . '%');
        }

        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('access_logs.created_at', [
                $filters['start_date'] . ' 00:00:00',
                $filters['end_date'] . ' 23:59:59'
            ]);
        } elseif (!empty($filters['start_date'])) {
            $query->whereDate('access_logs.created_at', '>=', $filters['start_date']);
        } elseif (!empty($filters['end_date'])) {
            $query->whereDate('access_logs.created_at', '<=', $filters['end_date']);
        }

        if (!empty($filters['method'])) {
            $query->where('method', strtoupper($filters['method']));
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['path'])) {
            $query->where('path', 'LIKE', '%' . $filters['path'] . '%');
        }

        return $this->logsFormat($query->orderBy('access_logs.created_at', 'desc')->paginate(20));
    }
}
