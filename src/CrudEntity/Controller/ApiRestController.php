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

class ApiRestController extends AbstractActionController
{

    public function apiRestAction()
    {
        $console = $this->getServiceLocator()->get('console');
        $tmpDir  = sys_get_temp_dir();
        $request = $this->getRequest();
        $name    = $request->getParam('name');
        $module  = $request->getParam('modulo');
        $path    = $request->getParam('path', '.');

        // generate controller
        $modelController = new ModelController($name, $module, $path);

        // add methods
        // method get
        $modelController->addMethod(
            new Generator\MethodGenerator(
                'get',
                array(),
                Generator\MethodGenerator::FLAG_PUBLIC,
                'return new JsonModel();'
            )
        );

        // method getList
        $modelController->addMethod(
            new Generator\MethodGenerator(
                'getList',
                array(),
                Generator\MethodGenerator::FLAG_PUBLIC,
                'return new JsonModel();'
            )
        );

        // method create
        $modelController->addMethod(
            new Generator\MethodGenerator(
                'create',
                array(),
                Generator\MethodGenerator::FLAG_PUBLIC,
                'return new JsonModel();'
            )
        );

        // method update
        $modelController->addMethod(
            new Generator\MethodGenerator(
                'update',
                array(),
                Generator\MethodGenerator::FLAG_PUBLIC,
                'return new JsonModel();'
            )
        );

        // method delete
        $modelController->addMethod(
            new Generator\MethodGenerator(
                'delete',
                array(),
                Generator\MethodGenerator::FLAG_PUBLIC,
                'return new JsonModel();'
            )
        );

        // valida se o controller existe antes de criar
        if ($modelController->isControllerExist()) {
            // pergunta ao usuário
            $console->write("File exist, really want to delete?: ");
            $read = $console->readChar('yn');


            if ($read == "n") {
                $console->write("n\n", Color::RED);
                $console->write("Controller was not created.\n", Color::RED);
                return;
            }

            $console->write("y\n", Color::GREEN);
        }

        // generate class
        $modelController->generate();

        // echo text
        $console->write("Controller create success!\n", Color::GREEN);

        // gerando o arquivo de configurações
        ModelConfig::generateConfig($module, $path);
        $console->write("Configuration file regenerate!\n", Color::GREEN);
    }
}
