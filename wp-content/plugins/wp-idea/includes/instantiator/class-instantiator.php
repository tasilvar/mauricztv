<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\instantiator;

use bpmj\wpidea\scopes\Abstract_Scope;
use Psr\Container\ContainerInterface;

class  Instantiator {
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function create(string $class_name, Abstract_Scope $scope): ?object
    {
        $object = $this->create_object($class_name, $scope);

        if ($object === null) {
            return null;
        }

        $this->maybe_initiate($object);

        return $object;
    }

    private function create_object(string $class_name, Abstract_Scope $scope): ?object
    {
        if (!class_exists($class_name)) {
            return null;
        }

        if (!$scope->is_current_request_in_scope()) {
            return null;
        }

        return $this->container->get($class_name);
    }

    private function maybe_initiate(object $object): void
    {
        if($object instanceof Interface_Initiable) {
            $object->init();
        }
    }

}
