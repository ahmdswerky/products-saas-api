<?php

namespace App\Helpers;

use Illuminate\Routing\ResourceRegistrar as RoutingResourceRegistrar;

class ResourceRegistrar extends RoutingResourceRegistrar
{
    protected function addResourceUpdate($name, $base, $controller, $options)
    {
        $name = $this->getShallowName($name, $options);

        $uri = $this->getResourceUri($name).'/{'.$base.'}';

        $action = $this->getResourceAction($name, $controller, 'update', $options);

        return $this->router->match(['POST'], $uri, $action);
    }
}
