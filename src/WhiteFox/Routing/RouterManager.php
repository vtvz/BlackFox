<?php
declare(strict_types=1);

namespace WhiteFox\Routing;

use League\Route\RouteCollectionInterface;
use League\Route\RouteGroup;
use League\Route\Router;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use WhiteFox\Routing\Attribute\Group;
use WhiteFox\Routing\Attribute\Route;

class RouterManager
{
    public function __construct(private Router $router)
    {
    }

    public function fromAttributes(object|string $controller): void
    {
        $class = new ReflectionClass($controller);

        $registerActions = static function (RouteCollectionInterface $router, string $name = '') use ($class) {
            $methods = $class->getMethods(ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {
                $attributes = $method->getAttributes(Route::class, ReflectionAttribute::IS_INSTANCEOF);

                foreach ($attributes as $attribute) {
                    /** @var Route $attributeInstance */
                    $attributeInstance = $attribute->newInstance();
                    /** @var \League\Route\Route $route */
                    $route = $router->map(
                        $attributeInstance->method,
                        $attributeInstance->pattern,
                        $class->getName() . '::' . $method->getName()
                    )->setName($name . $attributeInstance->name);

                    foreach ($attributeInstance->middlewares as $middleware) {
                        $route->lazyMiddleware($middleware);
                    }
                }
            }
        };

        $groupAttrs = $class->getAttributes(Group::class);
        if (empty($groupAttrs)) {
            $registerActions($this->router);
        } else {
            /** @var Group $attributeInstance */
            $attributeInstance = $groupAttrs[0]->newInstance();

            /** @var RouteGroup $group */
            $group = $this->router->group(
                $attributeInstance->pattern,
                fn(RouteGroup $router) => $registerActions($router, $attributeInstance->name)
            )->setName($attributeInstance->name);

            foreach ($attributeInstance->middlewares as $middleware) {
                $group->lazyMiddleware($middleware);
            }
        }
    }
}
