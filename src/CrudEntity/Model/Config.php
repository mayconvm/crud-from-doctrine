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
     * @param  string $path   Caminho do arquivos
     * @return void
     */
    public static function generateConfig($module, $path)
    {
        self::setPath($path);
        self::setModule($module);

        // alterar o config.php do módulo adicionando
        copy("$path/module/$module/config/module.config.php", "$path/module/$module/config/module.config.old");

        $moduleConfig = include "$path/module/$module/config/module.config.php";
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
