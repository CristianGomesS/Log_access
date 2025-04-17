<?php

namespace App\Http\Middleware;

use App\Services\AccessLogService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware responsável por registrar logs de acesso.
 *
 * Este middleware verifica se o usuário está autenticado e autorizado
 * para acessar a rota requisitada. Caso positivo, registra o log da requisição
 * e da resposta no banco de dados.
 */
class AccessLogMiddleware
{
    /**
     * Serviço de log de acesso.
     *
     * @var AccessLogService
     */
    protected $logAccessService;

    /**
     * Construtor do middleware com injeção de dependência.
     *
     * @param AccessLogService $logAccessService
     */
    public function __construct(AccessLogService $logAccessService)
    {
        $this->logAccessService = $logAccessService;
    }

    /**
     * Executa a lógica do middleware.
     *
     * @param Request $request A requisição HTTP recebida.
     * @param Closure $next O próximo middleware ou controlador.
     * @return Response A resposta final da requisição.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Se não estiver autenticado, retorna erro 401
        if (!$this->isAuthenticated()) {
            return $this->unauthenticatedResponse();
        }

        // Captura os dados antes da alteração (para PUT/DELETE/PATCH)
        $beforeLog = $this->getBeforeLog($request, $request->method());

        // Executa a próxima etapa da requisição (controller ou outro middleware)
        $response = $next($request);
        $user = Auth::user();

        // Verifica se o usuário tem acesso
        if ($this->hasAccess($user)) {
            return $this->handleAuthorizedRequest($request, $response, $user, $beforeLog);
        }

        // Caso contrário, registra tentativa não autorizada
        return $this->handleUnauthorizedRequest($request, $response, $user, $beforeLog);
    }

    /**
     * Verifica se o usuário está autenticado.
     */
    private function isAuthenticated(): bool
    {
        return Auth::check();
    }

    /**
     * Resposta para usuários não autenticados.
     */
    private function unauthenticatedResponse(): Response
    {
        return response()->json(['message' => 'Usuário não autenticado.'], 401);
    }

    /**
     * Processa e registra o log de uma requisição autorizada.
     */
    private function handleAuthorizedRequest(Request $request, Response $response, $user, $beforeLog): Response
    {
        // Verifica se a rota está isenta de log
        if ($this->isExemptRoute($request)) {
            return $response;
        }

        // Registra o log no banco
        $this->storeLogsDatabase(
            $request,
            $user->id,
            $response->getContent(),
            $beforeLog,
            $response->getStatusCode()
        );

        return $response;
    }

    /**
     * Processa e registra tentativa de acesso não autorizado.
     */
    private function handleUnauthorizedRequest(Request $request, Response $response, $user, $beforeLog): Response
    {
        if ($this->isExemptRoute($request)) {
            return $this->unauthorizedAccessResponse($request, $user);
        }

        // Registra o log com status 401
        $this->storeLogsDatabase(
            $request,
            $user->id,
            $response->getContent(),
            $beforeLog,
            $response->getStatusCode()
        );

        return $response;
    }

    /**
     * Resposta para usuários sem permissão.
     */
    private function unauthorizedAccessResponse(Request $request, $user): Response
    {
        $logResponse = ['error' => 'Usuário não tem permissão para acessar essa rota.'];

        // Registra o log da tentativa de acesso negada
        $this->storeLogsDatabase(
            $request,
            $user->id,
            json_encode($logResponse),
            null,
            401
        );

        return response()->json(['message' => 'Usuário não tem permissão para acessar essa rota.'], 401);
    }

    /**
     * Verifica se o usuário possui permissão com base no perfil.
     */
    private function hasAccess($user): bool
    {
        $allowedRoles = config('log_access_filter.allowed_profiles');
        if (empty($allowedRoles)) {
            return true;
        }else{
            return in_array($user->profile_id, $allowedRoles);
        }
    }

    /**
     * Verifica se a rota está isenta de registro de log.
     */
    private function isExemptRoute(Request $request): bool
    {
        $routesPermission = config('log_access_filter.routes_permission');
        return in_array($request->path(), $routesPermission);
    }

    /**
     * Envia os dados da requisição para o serviço de log.
     */
    private function storeLogsDatabase(Request $request, int $userId, string $responseContent, ?string $beforeLog, int $status): void
    {
        $this->logAccessService->getLogDatabase(
            $userId,
            $request->path(),
            $request->method(),
            $request->ip(),
            $status,
            $request->getContent(),
            $responseContent,
            $beforeLog
        );
    }

    /**
     * Retorna o conteúdo anterior de um recurso (antes de PUT, DELETE, PATCH).
     */
    public function getBeforeLog(Request $request, string $method): ?string
    {
        // Apenas para métodos que alteram dados
        if (!in_array($method, ['PUT', 'DELETE', 'PATCH'])) {
            return null;
        }

        // Divide o caminho da requisição
        $path = explode('/', $request->path());

        // Tenta encontrar o grupo de rotas no mapeamento
        $position = config("log_models_mapping.{$path[1]}") ?? config("log_models_mapping.default");

        foreach ($position as $key => $route) {
            // Converte a rota configurada em regex para verificar correspondência
            $pattern = preg_replace('/\{[^}]+\}/', '[\d]+', $key);
            $pattern = '#^' . $pattern . '$#';

            if (preg_match($pattern, $request->path())) {
                $collection = $route['model']::where($route['primary_key'], $path[count($path) - 1])->first();
                return $collection ? json_encode($collection->getAttributes()) : null;
            }
        }

        return null;
    }
}
