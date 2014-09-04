<?php

namespace CrudEntity\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\ConsoleModel;
use ZFTool\Model\Skeleton;
use ZFTool\Model\Utility;
use Zend\Console\ColorInterface as Color;
use Zend\Code\Generator;
use Zend\Code\Reflection;
use Zend\Filter\Word\CamelCaseToDash as CamelCaseToDashFilter;
use ZFTool\Model\Auxiliares;
use Zend\Code\Annotation\Parser;
use ZFTool\Model\Auxiliares\Form as GeneratorForm;
use ZFTool\Model\Auxiliares\Validate as GeneratorValidate;

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

        // gerando o controller
        $this->creatController($name, $module, $path);
        
        // gerando o arquivo de configurações
        $this->generateConfig($module, $path);
    }
}
