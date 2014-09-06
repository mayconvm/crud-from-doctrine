<?php

namespace CrudEntity\Model;

use Zend\Code\Generator;
use Zend\Code\Reflection;
use ZFTool\Model\Skeleton;

class Form
{
    private $classGenerator;

    private $element;

    private $options = array(
        'method' => 'POST'
    );

    /**
     * Método construtor da classe
     * @param string $name      Nome do formulário
     * @param string $namesapce NameSpace do formulário
     * @param string $path      Caminho para o arquivo ser gerado
     * @param array $options      Opções do formulário
     */
    public function __construct($name, $namesapce, $options)
    {
        // $fileReflection  = new Reflection\FileReflection($path . "/" . $name . ".php", true);
        // $classReflection = $fileReflection->getClass($name."Form");
        $this->classGenerator = new Generator\ClassGenerator();
        $this->classGenerator->addUse('Zend\Form\Form')
                            ->setExtendedClass('Form')
                            ->setNamespaceName($namesapce)
                            ->setName($name."Form");

        $this->options = $options;
    }

    /**
     * Método para adicionar um elemento no formulário
     * @param string $name   Nome do input
     * @param strin $tipo   tipo do input
     * @param array $option array do input
     */
    public function addElement($name, $tipo, $option)
    {
        $classUse = $this->usesType($tipo);
        $this->classGenerator->addUse($classUse);

        $arrayOption = Skeleton::exportConfig($option);

        $this->element[$name] = <<<ELEMENT_FORM
\n# ELEMENTO {$name} #
\${$name} = new {$classUse}('{$name}');
\${$name}->setLabel('{$name}');\n
\${$name}->setOptions({$arrayOption});\n
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
        $toString .= '$this->setOptions(' . Skeleton::exportConfig($this->options) . ');'. "\n";

        // adiciona os elementos
        foreach ($this->element as $name => $elemento) {
            $toString .= $elemento;
            $toString .= '$this->add($' . $name . ');' . "\n";
        }


        $this->classGenerator->addMethods(array(
            new Generator\MethodGenerator(
                '__construct',
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

        // cria o arquivo de formulário
        // $pathForm = "$path/module/{$module}/src/{$module}/Form/";
        // $pathFileForm = $pathForm . ucfirst($name). "Form.php";

        // // cria a pasta form
        // @mkdir($pathForm, 0775, true);
        // file_put_contents($pathFileForm, $form->generate());

        return $fileGenerator->generate();
    }

    /**
     * Método para retornar o tipo a ser instanciado na classe
     * @param  string $tipo tipo do input
     * @return string       Namespace da classe a ser usada na classe
     */
    public function usesType($tipo)
    {
        switch($tipo)
        {
            case 'text':
                return 'Zend\Form\Element\Text';
                break;
            case 'password':
                return 'Zend\Form\Element\password';
                break;
            case 'selec':
                return 'Zend\Form\Element\Selec';
                break;
            case 'hidden':
                return 'Zend\Form\Element\Hidden';
                break;
            case 'textarea':
                return 'Zend\Form\Element\Textarea';
                break;
            case 'password':
                return 'Zend\Form\Element\Submit';
                break;
            case 'csrf':
                return 'Zend\Form\Element\Csrf';
                break;

        }
    }
}
