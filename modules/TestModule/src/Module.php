<?php
declare(strict_types=1);

namespace TestModule;

use Cache\Adapter\Redis\RedisCachePool;
use DI;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Container\ContainerInterface;
use Redis;
use Symfony\Component\Cache\Adapter\Psr16Adapter;
use Symfony\Contracts\Cache\CacheInterface;
use TestModule\Controller\TestController;
use WhiteFox\ModuleManager\AbstractModule;
use WhiteFox\ModuleManager\Provider\ConfigProviderCommonTrait;
use WhiteFox\ModuleManager\Provider\ConfigProviderInterface;
use WhiteFox\Routing\RouterManager;

class Module extends AbstractModule implements ConfigProviderInterface
{
    use ConfigProviderCommonTrait;

    public function __construct(private RouterManager $router, private ContainerInterface $container)
    {
    }

    public function register(): void
    {
        $container = $this->container;

        $container->set(
            Redis::class,
            function (): Redis {
                $client = new Redis();
                $client->connect(getenv('REDIS_HOST'), (int)getenv('REDIS_PORT'));
                $client->auth(getenv('REDIS_PASSWORD'));

                return $client;
            }
        );

        $container->set(RedisCachePool::class, DI\autowire()->constructor(DI\get(Redis::class)));

        $container->set(CacheItemPoolInterface::class, DI\get(RedisCachePool::class));
        $container->set(\Psr\SimpleCache\CacheInterface::class, DI\get(RedisCachePool::class));

        $container->set(CacheInterface::class, DI\get(Psr16Adapter::class));

        $this->router->fromAttributes(TestController::class);
    }
}
