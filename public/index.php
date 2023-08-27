<?php
declare(strict_types=1);

use App\Http\Kernel;

require __DIR__ . '/../vendor/autoload.php';

$httpKernel = new Kernel();
$httpKernel->run();

