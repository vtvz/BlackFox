<?php
declare(strict_types=1);

namespace Application;

use DI;
use Invoker\InvokerInterface;
use Laminas\Diactoros\ResponseFactory;
use League\Route\RouteCollectionInterface;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use Middlewares\TrailingSlash;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Relay\Relay;
use WhiteFox\ModuleManager\AbstractModule;

class Module extends AbstractModule
{

    public function __construct(private ContainerInterface $container, private InvokerInterface $invoker)
    {
    }

    public function register(): void
    {
        $this->container->set(ResponseFactoryInterface::class, DI\get(ResponseFactory::class));

        $strategy = new ApplicationStrategy();
        $strategy->setContainer($this->container);

        $router = new Router();
        $router->setStrategy($strategy);

        $this->container->set(Router::class, $router);
        $this->container->set(RouteCollectionInterface::class, $router);

        $this->container->set(
            RequestHandlerInterface::class,
            fn() => new Relay(
                [
                    $this->invoker->call(
                        [new TrailingSlash(true), 'redirect'],
                        [DI\get(ResponseFactoryInterface::class)]
                    ),
                    new TrailingSlash(),
                    $router,
                ]
            )
        );
    }
}
