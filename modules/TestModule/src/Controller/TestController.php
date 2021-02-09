<?php
declare(strict_types=1);

namespace TestModule\Controller;

use DateTimeInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use WhiteFox\Routing\Attribute as Route;
use WhiteFox\Routing\UrlGenerator;

#[Route\Group('test.', '/')]
class TestController
{
    public function __construct(
        private ContainerInterface $container,
        private UrlGenerator $url
    ) {
    }

    #[Route\Get('redis', '/redis')]
    public function redisAction(): ResponseInterface
    {
        $data = $this->container->get(CacheInterface::class)->get(
            'cache_item',
            function (ItemInterface $item) {
                $item->expiresAfter(10);

                return date(DateTimeInterface::ATOM);
            }
        );

        return new JsonResponse(['cached' => $data]);
    }

    #[Route\Get('test', '/[{name}]')]
    public function testAction(
        ServerRequestInterface $request
    ): ResponseInterface {
        $name = $request->getAttribute('name', 'world');

        return new JsonResponse(
            [
                'hello' => $name,
                'url'   => $this->url->relativeUrlFor('test.test', ['name' => $name]),
            ]
        );
    }
}
