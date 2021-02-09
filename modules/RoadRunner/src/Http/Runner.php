<?php
declare(strict_types=1);

namespace RoadRunner\Http;

use Psr\Http\Server\RequestHandlerInterface;
use Spiral\Goridge;
use Spiral\RoadRunner;
use Throwable;
use WhiteFox\Http\RunnerInterface;

class Runner implements RunnerInterface
{
    public function run(RequestHandlerInterface $handler): void
    {
        $relay  = new Goridge\SocketRelay("/tmp/rr.socket", type: Goridge\SocketRelay::SOCK_UNIX);
        $worker = new RoadRunner\Worker($relay);
        $psr7   = new RoadRunner\PSR7Client($worker);

        while ($req = $psr7->acceptRequest()) {
            try {
                $response = $handler->handle($req);

                $psr7->respond($response);
            } catch (Throwable $e) {
                $psr7->getWorker()->error((string)$e);
            }
        }
    }
}
