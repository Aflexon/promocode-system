<?php

use App\Services\PromocodeService;
use PHPUnit\Framework\TestCase;

class PromocodeServiceTest extends TestCase
{
    protected PromocodeService $promocodeService;
    protected \App\Database\Connection $connection;
    protected \Faker\Generator $faker;

    public function __construct(string $name)
    {
        $this->promocodeService = new PromocodeService();
        $this->connection = \App\Database\Connection::getInstance();
        $this->faker = \Faker\Factory::create();
        parent::__construct($name);
    }

    public function setUp(): void
    {
        $this->connection->exec('TRUNCATE TABLE `promocodes`;');
    }

    protected function insertPromocodes(array $promocodes): void
    {
        $keys = implode(', ', array_keys($promocodes[0]));
        $placeholders = '';
        $values = [];
        $rowPlaceholder = '(' . str_repeat('?,', count($promocodes[0]) - 1) . '?' . ')';
        $placeholders .= str_repeat($rowPlaceholder . ',', count($promocodes) - 1) . $rowPlaceholder;
        foreach ($promocodes as $promocode) {
            array_push($values, ...array_values($promocode));
        }
        $stmt = $this->connection->prepare('INSERT INTO `promocodes` ' . '(' . $keys . ')' . ' VALUES ' . $placeholders);
        $stmt->execute($values);
    }

    public function testReceivingPromocode()
    {
        $code = PromocodeService::generateCode();
        $ip = $this->faker->ipv6;
        $userId = $this->faker->uuid();
        $this->insertPromocodes([['code' => $code]]);
        $returnedCode = $this->promocodeService->getPromocode($ip, $userId);
        $this->assertEquals($returnedCode, $code);

        $selectStmt = $this->connection->prepare('SELECT * FROM `promocodes` WHERE `code` = ?');
        $selectStmt->execute([$code]);
        $promocodeFromDB = $selectStmt->fetchObject();

        $this->assertEquals($promocodeFromDB->ip, $ip);
        $this->assertEquals($promocodeFromDB->user_id, $userId);
        $this->assertNotNull($promocodeFromDB->received_at);
    }

    public function testReceivingAlreadyReceivedPromocode()
    {
        $ip = $this->faker->ipv6;
        $userId = $this->faker->uuid();
        $code = PromocodeService::generateCode();
        $codes = [
            ['code' => PromocodeService::generateCode(), 'received_at' => null, 'user_id' => null, 'ip' => null],
            ['code' => $code, 'received_at' => date('Y-m-d H:i:s'), 'user_id' => $userId, 'ip' => $ip],
        ];
        $this->insertPromocodes($codes);
        $returnedCode = $this->promocodeService->getPromocode($ip, $userId);
        $this->assertEquals($returnedCode, $code);
    }

    public function testReceivingPromocodeWhenIPLimitReached()
    {
        $ip = $this->faker->ipv6;
        $userId = $this->faker->uuid();
        $codes = [['code' => PromocodeService::generateCode(), 'received_at' => null, 'user_id' => null, 'ip' => null]];
        for ($i = 0; $i < PromocodeService::MAX_PROMOCODES_PER_IP; $i++) {
            $codes[] = [
                'code' => PromocodeService::generateCode(),
                'received_at' => date('Y-m-d H:i:s'),
                'user_id' => $this->faker->uuid(),
                'ip' => $ip,
            ];
        }
        $this->insertPromocodes($codes);
        $returnedCode = $this->promocodeService->getPromocode($ip, $userId);
        $this->assertNull($returnedCode);
    }

    public function testReceivingPromocodeWhenAllCodesAreUsed()
    {
        for ($i = 0; $i < 10; $i++) {
            $codes[] = [
                'code' => PromocodeService::generateCode(),
                'received_at' => date('Y-m-d H:i:s'),
                'user_id' => $this->faker->uuid(),
                'ip' => $this->faker->ipv4,
            ];
        }
        $this->insertPromocodes($codes);
        $returnedCode = $this->promocodeService->getPromocode($this->faker->ipv4, $this->faker->uuid);
        $this->assertNull($returnedCode);
    }
}
