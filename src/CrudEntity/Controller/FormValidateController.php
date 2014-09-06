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
use CrudEntity\Model\Form\Doctrine\Type as ModelType;

class FormValidateController extends AbstractActionController
{

    public function apiRestEntityAction()
    {
        $console = $this->getServiceLocator()->get('console');
        $tmpDir  = sys_get_temp_dir();
        $request = $this->getRequest();
        $name    = $request->getParam('name');
        $module  = $request->getParam('module');
        $entity  = $request->getParam('entity');
        $path    = $request->getParam('path', '.');

        // criar o controller para apirest
        $this->createApiRestController($console);

        // buscar a entidade e gerar o array para passar para os metodos que cuidam de gerar os inputs
        $arrayEntity = ModelDoctrine::readEntity($doctrine, $entity);
        $arrayInputs = $this->entityToForm($arrayEntity);

        // create class form
        $this->generateForm($name);

        // create class validate

        // criar o controller para view
            // add input form
            // add validation form
            // add methods index, list, edit, create
        return new ViewModel();
    }

    private function generateForm($name, $module, $elements)
    {
        $namespace = ucfirst($module) . '\Form\\' . ucfirst($name) . 'Form';
        $modelForm = new ModelForm($name, $namespace);

        // Gerando os formulário
        foreach ($elements as $element) {
            $form->addElement($element['name'], $element['type'], $element['option']);
        }
    }

    /**
     * Método para gerar um array com as configurações do input, extraindo os dados da entidade
     * @param  array  $inputs Array com os dados extraidos do annotations do doctrine
     * @return array         Array de inputs extraido do campos
     */
    private function entityToForm(array $inputs)
    {
        $elements = array();

        foreach ($inputs as $input) {
            $elements[] = array(
                'name' => $input['fieldName'],
                'type' => ModelType::convertTypeDoctrine($input['type']),
                'required' => $input['nullable'],
                'attributes' => array(
                    'id' => $input['fieldName'],
                ),
                'option' => array(
                    'label' => $input['fieldName'],
                ),
                'filters'  => array(
                     array('name' => 'StripTags'),
                     array('name' => 'StringTrim'),
                 ),
                'validators' => array(
                     array(
                         'name'    => 'StringLength',
                         'options' => array(
                             'encoding' => 'UTF-8',
                             'min'      => 1,
                             'max'      => $input['length']?: 255,
                         ),
                     ),
                 ),
            );
        }

        return $elements;
    }


    private function createApiRestController($typeController, $console)
    {
         // generate controller
        $modelController = new ModelController($name, $module, $path);
        $methods = array();

        if ($typeController == "API") {
            $methods = $this->methodsControllerApi();
        } elseif ($typeController == "VIEW") {
            $methods = $this->methodsControllerView();
        }

        // Adiciona metodos do controller
        $modelController->addMehtods($methods);
            // add input form
            // add validation form


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
        ModelConfig::generateConfig($module, $name, true, $path);
        $console->write("Configuration file regenerate!\n", Color::GREEN);
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
                'return new JsonModel();'
            ),
            // method getList
            new Generator\MethodGenerator(
                'list',
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
                'edit',
                array(),
                Generator\MethodGenerator::FLAG_PUBLIC,
                'return new JsonModel();'
            )
        );
    }
}
