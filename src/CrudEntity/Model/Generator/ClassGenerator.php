<?php

namespace CrudEntity\Model\Generator;

use Zend\Code\Generator\ClassGenerator as GeneratorClass;

class ClassGenerator extends GeneratorClass
{

    /**
     * Method for remove all methods
     * @return this
     */
    public function removeAllMethods()
    {
        $this->methods = array();

        return $this;
    }
}
