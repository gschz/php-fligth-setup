<?php

declare(strict_types=1);

namespace app\utils;

use flight\Engine;

class ApiResponse
{
    /** @param Engine<object> $engine */
    public static function success(Engine $engine, mixed $data, int $status = 200): void
    {
        $engine->json(['success' => true, 'data' => $data], $status);
    }

    /** @param Engine<object> $engine */
    public static function error(Engine $engine, string $message, int $status = 400, mixed $errors = null): void
    {
        $payload = ['success' => false, 'message' => $message];
        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        $engine->json($payload, $status);
    }

    /**
     * @param Engine<object> $engine
     * @param array<string, mixed> $extra
     */
    public static function paginated(Engine $engine, mixed $data, int $total, int $page, int $perPage, array $extra = []): void
    {
        $engine->json(array_merge([
            'success' => true,
            'data'    => $data,
            'meta'    => [
                'total'    => $total,
                'page'     => $page,
                'per_page' => $perPage,
                'pages'    => (int)ceil($total / max(1, $perPage)),
            ],
        ], $extra));
    }
}
