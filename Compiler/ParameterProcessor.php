<?php

namespace Kutny\AutowiringBundle\Compiler;

use ReflectionClass;
use ReflectionParameter;
use Symfony\Component\DependencyInjection\Reference;

class ParameterProcessor
{

    public function getParameterValue(ReflectionParameter $parameter, array $classes)
    {
        $parameterClass = $parameter->getClass();

        if ($parameterClass) {
            $value = $this->processParameterClass($parameterClass, $parameter, $classes);
        } else {
            if ($parameter->isDefaultValueAvailable()) {
                $value = $parameter->getDefaultValue();
            } else {
                throw new ParameterNotFoundException('Class ' . $parameter->getDeclaringClass()->getName(
                ) . ' constructor param $' . $parameter->getName() . ' cannot be resolved');
            }
        }

        return $value;
    }

    private function processParameterClass(ReflectionClass $parameterClass, ReflectionParameter $parameter, $classes)
    {
        $class = $parameterClass->getName();

        if (isset($classes[$class])) {
            if (count($classes[$class]) === 1) {
                $value = new Reference($classes[$class][0]);
            } else {
                throw new MultipleDeclarationException('Multiple service definition for class ' . $class);
            }
        } else {
            if ($parameter->isDefaultValueAvailable()) {
                $value = $parameter->getDefaultValue();
            } else {
                throw new ServiceNotFoundException('Service not defined for class ' . $class);
            }
        }

        return $value;
    }
}
