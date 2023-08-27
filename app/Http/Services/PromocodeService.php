<?php
declare(strict_types=1);

namespace App\Http\Services;

use App\Http\Database\Connection;

class PromocodeService
{
    const PARTNER_URL = 'https://www.google.com/';

    const MAX_PROMOCODES_PER_IP = 1000;

    const DEFAULT_PROMOCODE_LENGTH = 10;

    protected Connection $connection;

    public function __construct()
    {
        $this->connection = Connection::getInstance();
    }

    public static function generateCode(): string
    {
        // специально не используем символы "ловушки", которые можно спутать с другими типа 0-O I-1-l
        // на случай если промокод будет вводится руками
        $characters = '23456789qwertyuipasdfghjkzxcvbnmQWERTYUIPASDFGHJKLZXCVBNM';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < self::DEFAULT_PROMOCODE_LENGTH; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function getPartnerUrl(string $promocode): string
    {
        return self::PARTNER_URL . '?query=' . urlencode($promocode);
    }

    protected function acquirePromocode(string $ip, string $userId)
    {
        try {
            $updateStmt = $this->connection
                ->prepare('UPDATE `promocodes` SET `received_at` = NOW(), `ip` = ?, `user_id` = ? WHERE `received_at` IS NULL LIMIT 1');
            $updateStmt->execute([$ip, $userId]);
        } catch (\PDOException $err) {
            if ($err->errorInfo[1] !== 1062) {
                throw $err;
            }
        }
    }

    /**
     * Чтобы избежать RaceCondition для проверки на лимит для IP используется user-defined lock:
     * https://dev.mysql.com/doc/refman/8.0/en/locking-functions.html
     *
     * Правило один юзер = один промокод, гарантируется с помощью UNIQUE индекса на user_id
     */
    public function getPromocode(string $ip, string $userId): string | null
    {
        $lockName = 'getPromocode:' . $ip;
        $this->connection->prepare('SELECT GET_LOCK(?, -1)')->execute([$lockName]);
        try {
            $countStmt = $this->connection->prepare('SELECT COUNT(*) FROM `promocodes` WHERE `ip` = ?');
            $countStmt->execute([$ip]);
            $count = $countStmt->fetchColumn();
            if ($count >= self::MAX_PROMOCODES_PER_IP) {
                return null;
            }

            $this->acquirePromocode($ip, $userId);

            $promocodeStmt = $this->connection
                ->prepare('SELECT `code` FROM `promocodes` WHERE `user_id` = ? LIMIT 1');
            $promocodeStmt->execute([$userId]);

            return $promocodeStmt->fetchColumn() ?: null;
        } finally {
            $this->connection->prepare('SELECT RELEASE_LOCK(?);')->execute([$lockName]);
        }
    }
}
