<?php

namespace CrudEntity\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\ConsoleModel;
use ZFTool\Model\Skeleton;
use ZFTool\Model\Utility;
use Zend\Console\ColorInterface as Color;
use Zend\Code\Generator;
use CrudEntity\Model\Controller as ModelController;
use CrudEntity\Model\Config as ModelConfig;
use CrudEntity\Model\Form as ModelForm;
use CrudEntity\Model\Validate as ModelValidate;
use CrudEntity\Model\Doctrine as ModelDoctrine;
use CrudEntity\Model\Form\Doctrine\ArrayForm;

class FormValidateController extends AbstractActionController
{

    private $console;

    public function apiRestEntityAction()
    {
        $this->console = $this->getServiceLocator()->get('console');
        $tmpDir  = sys_get_temp_dir();
        $request = $this->getRequest();
        $name    = $request->getParam('name');
        $module  = $request->getParam('module');
        $entity  = $request->getParam('entity');
        $path    = $request->getParam('path', '.');
        $doctrine = $this->getServiceLocator()->get("Doctrine\ORM\EntityManager");

        $modelController = new ModelController($name, $module, $path);
        $ucName = ucfirst($name);

        // criar o controller para apirest
        $modelController->setName("Api" . $name);
        $this->createApiRestController('API', $modelController);

        // create controller view
        $modelController->setName($name);
        $this->createApiRestController('VIEW', $modelController);

        // gerando o arquivo de configurações
        ModelConfig::generateConfig($module, $name, true, $path);
        $this->console->write("Configuration file regenerate!\n", Color::GREEN);

        // buscar a entidade e gerar o array para passar para os metodos que cuidam de gerar os inputs
        $arrayEntity = ModelDoctrine::readEntity($doctrine, $entity);
        $arrayInputs = ArrayForm::entityToForm($arrayEntity);

        // create class form
        $this->generateForm($name, $module, $arrayInputs, $path);

        die("okkk");
        // create class validate

        // criar o controller para view
            // add input form
            // add validation form
            // add methods index, list, edit, create
        return new ViewModel();
    }

    /**
     * Method for generate class form
     * @param  string $name     Name form
     * @param  string $module   name module
     * @param  array $elements Array of elements inputs
     * @param  string $path path module
     * @return void
     */
    private function generateForm($name, $module, array $elements, $path)
    {
        $namespace = ucfirst($module) . '\Form\\' . ucfirst($name) . 'Form';
        $modelForm = new ModelForm($name, $module, $path);

        // Gerando os formulário
        foreach ($elements as $element) {
            $modelForm->addElement($element['name'], $element['type'], $element['option']);
        }

        // generate class form
        $modelForm->generate();
    }

    /**
     * Método par gera o validador do formulário
     * @param  string $namespace namespace do validador
     * @param  string $name      Nome da classe
     * @param  string $module    Modulo da classe
     * @param  array $elements  Array de inputs
     * @param  string $path      Caminho dos arquivos
     * @return void
     */
    protected function generateValidate($namespace, $name, $module, $elements, $path)
    {
        $namespaceValidate = $namespace . '\Validate';
        $validate = new GeneratorValidate($name, $namespaceValidate);

        foreach ($elements as $element) {
            $validate->addElement($element['name'], $element);
        }

        // Gerando os validatores
        $pathValidate = "$path/module/{$module}/src/{$module}/Validate/";
        $pathFileValidate = $pathValidate . ucfirst($name). "Validate.php";

        // cria a pasta form
        @mkdir($pathValidate, 0775, true);
        file_put_contents($pathFileValidate, $validate->generate());
    }


    private function createApiRestController($typeController, $modelController)
    {
         // generate controller
        $methods = array();

        // clear all methods
        $modelController->removeAllMethods();

        if ($typeController == "API") {

            $methods = $this->methodsControllerApi();
            $modelController->getGenerator()->addUse('Zend\Mvc\Controller\AbstractRestfulController');
            $modelController->getGenerator()->setExtendedClass('AbstractRestfulController');

        } elseif ($typeController == "VIEW") {

            $methods = $this->methodsControllerView();
            $modelController->getGenerator()->addUse('Zend\View\Model\ViewModel');
            $modelController->getGenerator()->addUse('Zend\Mvc\Controller\AbstractActionController');
            $modelController->getGenerator()->setExtendedClass('AbstractActionController');
        }

        // Adiciona metodos do controller
        $modelController->addMethods($methods);
            // add input form
            // add validation form


        // valida se o controller existe antes de criar
        if ($modelController->isControllerExist()) {
            // pergunta ao usuário
            $this->console->write("File " . $modelController->getName() . " exist, really want to delete?: ");
            $read = $this->console->readChar('yn');


            if ($read == "n") {
                $this->console->write("n\n", Color::RED);
                $this->console->write("Controller was not created.\n", Color::RED);
                return;
            }

            $this->console->write("y\n", Color::GREEN);
        }

        // generate class
        $modelController->generate();

        // echo text
        $this->console->write("Controller create success!\n", Color::GREEN);
    }

    /**
     * Methods Controller Api
     * @return array Array of methods
     */
    private function methodsControllerApi()
    {
        // add methods
        return array(
            // method get
            new Generator\MethodGenerator(
                'get',
                array(),
                Generator\MethodGenerator::FLAG_PUBLIC,
                'return new JsonModel();'
            ),
            // method getList
            new Generator\MethodGenerator(
                'getList',
                array(),
                Generator\MethodGenerator::FLAG_PUBLIC,
                'return new JsonModel();'
            ),
            // method create
            new Generator\MethodGenerator(
                'create',
                array(),
                Generator\MethodGenerator::FLAG_PUBLIC,
                'return new JsonModel();'
            ),
            // method update
            new Generator\MethodGenerator(
                'update',
                array(),
                Generator\MethodGenerator::FLAG_PUBLIC,
                'return new JsonModel();'
            ),
            // method delete
            new Generator\MethodGenerator(
                'delete',
                array(),
                Generator\MethodGenerator::FLAG_PUBLIC,
                'return new JsonModel();'
            )
        );
    }

    /**
     * Methods Controller view
     * @return array Array of methods
     */
    private function methodsControllerView()
    {
        // add methods
        return array(
            // method get
            new Generator\MethodGenerator(
                'index',
                array(),
                Generator\MethodGenerator::FLAG_PUBLIC,
                'return new ViewModel();'
            ),
            // method getList
            new Generator\MethodGenerator(
                'list',
                array(),
                Generator\MethodGenerator::FLAG_PUBLIC,
                'return new ViewModel();'
            ),
            // method create
            new Generator\MethodGenerator(
                'create',
                array(),
                Generator\MethodGenerator::FLAG_PUBLIC,
                'return new ViewModel();'
            ),
            // method update
            new Generator\MethodGenerator(
                'edit',
                array(),
                Generator\MethodGenerator::FLAG_PUBLIC,
                'return new ViewModel();'
            )
        );
    }
}
