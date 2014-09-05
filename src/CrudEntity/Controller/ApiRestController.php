<?php

namespace CrudEntity\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\ConsoleModel;
use ZFTool\Model\Skeleton;
use ZFTool\Model\Utility;
use Zend\Console\ColorInterface as Color;
use Zend\Code\Generator;
use CrudEntity\Model\Controller;
use CrudEntity\Model\Config;

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
        $modelController = new Model\Controller($name, $module, $path);

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

        // gerando o arquivo de configurações
        Model\Config::generateConfig($module, $path);
    }
}
