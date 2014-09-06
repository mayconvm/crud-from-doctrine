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
     * @var string
     */
    private $name;


    /**
     * @var string
     */
    private $module;

    /**
     * Método construtor da classe
     * @param string $name      Nome do formulário
     * @param string $namesapce NameSpace do formulário
     * @param string $path      Caminho para o arquivo ser gerado
     * @param array $options      Opções do formulário
     */
    public function __construct($name, $module, $path, $options = array())
    {
        $this->setPath($path);
        $this->setModule($module);
        $this->setName($name);

        $namesapce = ucfirst($this->module) . "\Form";

        // $fileReflection  = new Reflection\FileReflection($path . "/" . $name . ".php", true);
        // $classReflection = $fileReflection->getClass($name."Form");
        $this->classGenerator = new Generator\ClassGenerator();
        $this->classGenerator->addUse('Zend\Form\Form')
                            ->setExtendedClass('Form')
                            ->setNamespaceName($namesapce)
                            ->setName(ucfirst($this->name)."Form");

        $this->setOptions($options);
    }

    /**
     * Method set options
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = array_merge($options, $this->options);
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

        $ucModule = ucfirst($this->module);

        // cria o arquivo de formulário
        $pathForm = $this->path . "/module/{$ucModule}/src/{$ucModule}/Form/";
        $pathFileForm = $pathForm . ucfirst($name). "Form.php";

        // // cria a pasta form
        @mkdir($pathForm, 0775, true);
        file_put_contents($pathFileForm, $form->generate());

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

    /**
     * Método para setar o nome do controller
     * @param string $name Nome
     */
    public function setName($name)
    {
        if (empty($name)) {
            return;
        }

        if (file_exists($this->path."/module/" . ucfirst($this->module) ."/src/" . ucfirst($this->module) . "/Form/" . ucfirst($name) . "Form.php")) {
            $this->fileExist = true;
        }

        $this->name = $name;
    }

    /**
     * Método para setar o módulo do controller
     * @param string $module Nome do módulo
     */
    public function setModule($module)
    {
        if (empty($module)) {
            return;
        }

        if (!file_exists($this->path."/module") || !file_exists($this->path."/config/application.config.php")) {
            throw new \Exception("O diretório " . $this->path . " não é um módulo ZF2.");
        }

        $this->module = $module;
    }

    /**
     * Método para setar o caminho do módulo
     * @param string $path Caminho do módulo
     */
    public function setPath($path = ".")
    {
        if (empty($path)) {
            return;
        }

        $this->path = $path;
    }

    /**
     * Method for valid file exist
     * @return boolean If any file
     */
    public function isFormExist()
    {
        return $this->fileExist;
    }
}
