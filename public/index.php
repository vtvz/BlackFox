<?php
declare(strict_types=1);

use Laminas\ConfigAggregator\PhpFileProvider;
use WhiteFox\Kernel;

ini_set('display_errors', 'stderr');
require __DIR__ . '/../vendor/autoload.php';

$app = new Kernel();
$app->build([new PhpFileProvider(__DIR__ . '/../config/*.php')]);

$app->run();
