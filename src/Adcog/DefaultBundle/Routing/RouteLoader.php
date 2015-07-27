<?php

namespace Adcog\DefaultBundle\Routing;

use Sensio\Bundle\FrameworkExtraBundle\Routing\AnnotatedRouteControllerLoader;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class RouteLoader
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class RouteLoader extends AnnotatedRouteControllerLoader
{
    /**
     * {@inheritdoc}
     */
    protected function getDefaultRouteName(\ReflectionClass $class, \ReflectionMethod $method)
    {
        $controller = trim(preg_replace(
            ['/controller/', '/action(_\d+)?$/', '/__/'],
            ['', '\\1', '_'],
            Container::underscore($class->getShortName())
        ), '_');

        $action = trim(preg_replace(
            ['/action(_\d+)?$/', '/__/'],
            ['\\1', '_'],
            Container::underscore($method->getName())
        ), '_');

        return sprintf('%s_%s', $controller, $action);
    }
}
