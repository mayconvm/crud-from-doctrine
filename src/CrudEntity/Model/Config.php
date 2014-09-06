<?php

namespace CrudEntity\Model;

use ZFTool\Model\Skeleton;

class Config
{

    private static $path;

    private static $module;

    private static $head = <<<EOD
<?php
/**
 * Arquivo gerado pelo ZFTool/Maycon
 *
 * @see https://github.com/mayconvm/ZFTool
 */

EOD;

    /**
     * Método para adicionar os dados de configurações par APIRest
     * @param  string $module Nome do módulo
     * @param  string $name   Name controller
     * @param  booleand $view   Se deve criar a rota para view ou não
     * @param  string $path   Caminho do arquivos
     * @return void
     */
    public static function generateConfig($module, $name, $view = false, $path = ".")
    {
        self::setPath($path);
        self::setModule($module);

        // alterar o config.php do módulo adicionando
        copy("$path/module/$module/config/module.config.php", "$path/module/$module/config/module.config.old");

        $moduleConfig = include "$path/module/$module/config/module.config.php";

        $nameController = ucfirst($module) . '\Controller\\Api' . ucfirst($name);
        $namespaceController = ucfirst($module) . '\Controller\\Api' . ucfirst($name) . "Controller";

        // add array of controller invokables
        $moduleConfig['controllers']['invokables'][$nameController] = $namespaceController;

        // add array of controller router APIRest
        $moduleConfig['router']['routes'][$nameController] = array(
                'type' => 'Zend\Mvc\Router\Http\Segment',
                'options' => array(
                    'route'    => '/Api/' . ucfirst($name) . '[/][/:id]',
                    'constraints' => array(
                        'id'     => '[0-9]*',
                    ),
                    'defaults' => array(
                        'controller' => $nameController,
                    ),
                ),
        );

        if ($view) {
            $nameControllerView = ucfirst($module) . '\Controller\\View' . ucfirst($name);
            $namespaceControllerView = ucfirst($module) . '\Controller\\View' . ucfirst($name) . "Controller";

            // add array of controller invokables
            $moduleConfig['controllers']['invokables'][$nameControllerView] = $namespaceControllerView;

            // add array of controller router View
            $moduleConfig['router']['routes'][$nameControllerView] = array(
                    'type' => 'Zend\Mvc\Router\Http\Segment',
                    'options' => array(
                        'route'    => '/' . ucfirst($name) . '[/][:action][/:id]',
                        'constraints' => array(
                            'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            'id'     => '[0-9]*',
                        ),
                        'defaults' => array(
                            'controller' => $nameControllerView,
                            'action'     => 'index',
                        ),
                    ),
            );
        }

        // add array of view option ViewJson
        $moduleConfig['view_manager']['strategies'] = array('ViewJsonStrategy');

        $conteudo = self::$head;
        $conteudo .= "return " . Skeleton::exportConfig($moduleConfig, 2) . ";";

        return file_put_contents("$path/module/$module/config/module.config.php", $conteudo);
    }

    /**
     * Method for generate class configuration
     * @param  string $module Module
     * @param  string $path   Path module
     * @return booleand         File creat
     */
    public function generate($module, $path)
    {
        self::setPath($path);
        self::setModule($module);

        self::generateConfig($module, $path);
    }

    /**
     * Method for set path
     * @param string $path path module
     */
    public static function setPath($path)
    {
        self::$path = $path;
    }

    /**
     * Method for set name module
     * @param string $module name module
     */
    public static function setModule($module)
    {
        if (!is_file(self::$path . "/module/$module/config/module.config.php")) {
            throw new \Exception("File configuration no exist.", 1);
        }

        self::$module = $module;
    }
}
