<?php
declare(strict_types=1);

namespace SapiRunner\Http;

use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Laminas\HttpHandlerRunner\Emitter\EmitterStack;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Laminas\HttpHandlerRunner\Emitter\SapiStreamEmitter;
use Laminas\HttpHandlerRunner\RequestHandlerRunner;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;
use WhiteFox\Http\RunnerInterface;

class Runner implements RunnerInterface
{
    public function __construct(private ResponseFactoryInterface $responseFactory)
    {
    }

    public function run(RequestHandlerInterface $handler): void
    {
        $sapiStreamEmitter  = new SapiStreamEmitter();
        $conditionalEmitter = new class ($sapiStreamEmitter) implements EmitterInterface {
            public function __construct(private EmitterInterface $emitter)
            {
            }

            public function emit(ResponseInterface $response): bool
            {
                if (!$response->hasHeader('Content-Disposition')
                    && !$response->hasHeader('Content-Range')
                ) {
                    return false;
                }

                return $this->emitter->emit($response);
            }
        };

        $stack = new EmitterStack();
        $stack->push(new SapiEmitter());
        $stack->push($conditionalEmitter);

        $runner = new RequestHandlerRunner(
            $handler,
            $stack,
            [ServerRequestFactory::class, 'fromGlobals'],
            function (Throwable $e) {
                $response = $this->responseFactory->createResponse(500);
                $response->getBody()->write(
                    sprintf(
                        'An error occurred: %s',
                        $e->getMessage()
                    )
                );

                return $response;
            }
        );

        $runner->run();
    }
}
