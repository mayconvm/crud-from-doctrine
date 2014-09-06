<?php

namespace CrudEntity\Model;

use Zend\Code\Generator;
use Zend\Filter\Word\CamelCaseToDash as CamelCaseToDashFilter;
use CrudEntity\Model\Generator\ClassGenerator;

class Controller
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $module;

    /**
     * @var string
     */
    private $path = ".";

    /**
     * @var string
     */
    private $pathFull;

    /**
     * @var array
     */
    private $arrayMethods;

    /**
     * @var ClassGenerator
     */
    private $generator;

    /**
     * @var boolean
     */
    private $fileExist = false;

    /**
     * Método construtor
     * @param string $name   Nome da API
     * @param string $module Nome do módulo
     * @param string $path   Caminho para o módulo
     */
    public function __construct($name, $module, $path)
    {
        $this->generator = new ClassGenerator();

        $this->setPathFull($name, $module, $path);

        $ucName     = ucfirst($this->name);
        $ucModule = ucfirst($this->module);
        $controller = $ucName . 'Controller';

        // Gerar um controller com a classe abstrata de webservice
        $this->generator->setNamespaceName($ucModule . '\Controller')
             ->addUse('Zend\Mvc\Controller\AbstractRestfulController')
             ->addUse('Zend\View\Model\JsonModel');

        // adicionar os métodos get, getlist, create, update, delete
        $this->generator->setName($controller)
             ->setExtendedClass('AbstractRestfulController');
    }

    /**
     * Method get class 'ClassGenerator'
     * @return CrudEntity\Model\Generator\ClassGenerator
     */
    public function getGenerator()
    {
        return $this->generator;
    }

    /**
     * Méthod set full path
     * @param string $name   Name file
     * @param string $module name module
     * @param string $path   path module
     */
    protected function setPathFull($name, $module, $path)
    {
        $this->setName($name);
        $this->setModule($module);
        $this->setpath($path);

        $this->removeAllMethods();
    }

    /**
     * Method remove all methods
     * @return void
     */
    public function removeAllMethods()
    {
        $this->generator->removeAllMethods();
        $this->arrayMethods = array();
    }

    /**
     * Method get full path
     * @return string Full path
     */
    protected function getPathFull()
    {
        $ucName = ucfirst($this->name);
        $ucModule = ucfirst($this->module);

        return $this->path . '/module/' . $ucModule . '/src/' . $ucModule . '/Controller/' . $ucName.'Controller.php';
    }

    /**
     * Método para gerar o controller
     * @return void
     */
    public function generate()
    {
        $this->generator->addmethods($this->arrayMethods);

        $file = new Generator\FileGenerator(
            array(
                'classes'  => array($this->generator),
            )
        );

        return file_put_contents($this->getPathFull(), $file->generate());
    }

    /**
     * Método para adicionar métodos no controller a ser gerado
     * @param Generator\MethodGenerator $method Classe MethodGenerator
     */
    public function addMethod(Generator\MethodGenerator $method)
    {
        $this->arrayMethods[] = $method;
    }

    /**
     * Method for add array methods
     * @param array $methods Array of methods
     */
    public function addMethods(array $methods)
    {
        foreach ($methods as $method) {
            $this->addMethod($method);
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

        if (file_exists($this->path."/module/" . ucfirst($this->module) ."/src/" . ucfirst($this->module) . "/Controller/" . ucfirst($this->name) . "Controller.php")) {
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
    public function isControllerExist()
    {
        return $this->fileExist;
    }

    /**
     * Method return nem class
     * @return string name class
     */
    public function getName()
    {
        return $this->name;
    }
}
