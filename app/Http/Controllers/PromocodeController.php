<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Cookies;
use App\Http\Request;
use App\Http\Response;
use App\Http\Services\PromocodeService;
use Ramsey\Uuid\Uuid;

class PromocodeController
{
    public function index(Request $request): Response
    {
        // здесь user_id простая эмуляция некой системы аутентификации
        // в реальной жизни у юзера будет сессия или JWT TOKEN откуда мы бы считывали user_id
        $userId = $request->getCookies()['userId'] ?? Uuid::uuid4()->toString();
        return new Response(
            200,
            Cookies::toHeaders([
                'userId' => ['value' => $userId, 'expires' => '1 year', 'samesite' => 'strict', 'httponly' => true],
            ]),
            file_get_contents('../views/promocode-form.html')
        );
    }

    public function receivePromocode(Request $request): Response
    {
        $promocodeService = new PromocodeService();
        $userId = $request->getCookies()['userId'] ?? null;
        if (!$userId) {
            return new Response(401, [], 'Unauthorized');
        }
        $promocode = $promocodeService->getPromocode($request->getServerParam('REMOTE_ADDR'), $userId);
        if (!$promocode) {
            return new Response(200, [], 'Промокоды закончились');
        }
        return new Response(303, ['Location: ' . $promocodeService->getPartnerUrl($promocode)], $promocode);
    }
}
