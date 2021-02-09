<?php
declare(strict_types=1);

namespace WhiteFox\Routing;

use FastRoute\RouteParser\Std;
use InvalidArgumentException;
use League\Route\Router;

class UrlGenerator
{
    private Std $routeParser;

    /**
     * @param Router $router
     */
    public function __construct(private Router $router)
    {
        $this->routeParser = new Std();
    }

    public function relativeUrlFor(string $routeName, array $data = [], array $queryParams = []): string
    {
        $route   = $this->router->getNamedRoute($routeName);
        $pattern = $route->getPath();

        $segments    = [];
        $segmentName = '';

        /*
         * $routes is an associative array of expressions representing a route as multiple segments
         * There is an expression for each optional parameter plus one without the optional parameters
         * The most specific is last, hence why we reverse the array before iterating over it
         */
        $expressions = array_reverse($this->routeParser->parse($pattern));
        foreach ($expressions as $expression) {
            foreach ($expression as $segment) {
                /*
                 * Each $segment is either a string or an array of strings
                 * containing optional parameters of an expression
                 */
                if (is_string($segment)) {
                    $segments[] = $segment;
                    continue;
                }

                /*
                 * If we don't have a data element for this segment in the provided $data
                 * we cancel testing to move onto the next expression with a less specific item
                 */
                if (!array_key_exists($segment[0], $data)) {
                    $segments    = [];
                    $segmentName = $segment[0];
                    break;
                }

                $segments[] = $data[$segment[0]];
            }

            /*
             * If we get to this logic block we have found all the parameters
             * for the provided $data which means we don't need to continue testing
             * less specific expressions
             */
            if (!empty($segments)) {
                break;
            }
        }

        if (empty($segments)) {
            throw new InvalidArgumentException('Missing data for URL segment: ' . $segmentName);
        }

        $url = implode('', $segments);
        if ($queryParams) {
            $url .= '?' . http_build_query($queryParams);
        }

        return $url;
    }
}
