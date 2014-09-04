<?php

namespace CrudEntity\Model;

use Zend\Code\Generator;
use Zend\Code\Reflection;
use ZFTool\Model\Skeleton;

class Validate
{
    private $classGenerator;

    private $element;

    private $options;

    /**
     * Método construtor da classe
     * @param string $name      Nome do
     * @param string $namesapce namespace do validate
     */
    public function __construct($name, $namesapce)
    {
        $this->classGenerator = new Generator\ClassGenerator();
        $this->classGenerator->addUse('Zend\InputFilter\InputFilter')
                            ->addUse('Zend\InputFilter\InputFilterAwareInterface')
                            ->addUse('Zend\InputFilter\InputFilterInterface')
                            ->setImplementedInterfaces(array('InputFilterAwareInterface'))
                            ->setNamespaceName($namesapce)
                            ->setName($name."Validate");

        $this->classGenerator->addMethods(array(
            new Generator\MethodGenerator(
                'setInputFilter',
                array('InputFilterInterface $inputFilter'),
                Generator\MethodGenerator::FLAG_PUBLIC,
                'throw new \Exception("Not used");'
            ),
        ));
    }

    /**
     * Método para adicionar um elemento
     * @param string $name   Nome do input
     * @param array $options array do input
     */
    public function addElement($name, $options)
    {
        // filters
        $arrayFilter = Skeleton::exportConfig($options);

        $this->element[$name] = <<<ELEMENT_FORM
    # ELEMENT {$name} #
    \$inputFilter->add($arrayFilter);\n\n
ELEMENT_FORM;
    }

    /**
     * Método para renderizar o metodo construtor e adicionar os elementos
     * @return [type] [description]
     */
    protected function render()
    {
        $toString = "";

        // Adiciona as opções do formulário
        $toString .= <<<INI_FILE
if (!\$this->inputFilter) { \n
    \$inputFilter = new InputFilter();\n
INI_FILE;

        // adiciona os elementos
        foreach ($this->element as $name => $elemento) {
            $toString .= $elemento;
        }

        $toString .= <<<FOOT_FILE
    \$this->inputFilter = \$inputFilter;
}

return \$this->inputFilter;
FOOT_FILE;


        $this->classGenerator->addMethods(array(
            new Generator\MethodGenerator(
                'getInputFilter',
                array(),
                Generator\MethodGenerator::FLAG_PUBLIC,
                $toString
            ),
        ));
    }

    /**
     * Método para saida da classe gerada
     * @return string Classe gerada
     */
    public function generate()
    {
        $this->render();

        $fileGenerator = new Generator\FileGenerator(
            array(
                'classes'  => array($this->classGenerator),
            )
        );

        return $fileGenerator->generate();
    }
}
