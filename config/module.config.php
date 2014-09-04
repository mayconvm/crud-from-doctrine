<?php
return array(
    'CrudEntity' => array(
        'disable_usage' => false,    // set to true to disable showing available ZFTool commands in Console.
    ),

    // -----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=-----=

    'controllers' => array(
        'invokables' => array(
            'CrudEntity\Controller\ApiRest'      => 'CrudEntity\Controller\ApiRestController',
        ),
    ),

    'console' => array(
        'router' => array(
            'routes' => array(
                
                'apiRest' => array(
                    'options' => array(
                        'route'    => 'create apirest <name> <modulo> [<path>]',
                        'defaults' => array(
                            'controller' => 'CrudEntity\Controller\ApiRest',
                            'action'     => 'apiRest',
                        ),
                    ),
                ),

                'formValidateForEntity' => array(
                    'options' => array(
                        'route'    => 'create form-validate-entity <name> <modulo> <entidade> [<path>]',
                        'defaults' => array(
                            'controller' => 'CrudEntity\Controller\ApiRest',
                            'action'     => 'apiRestEntity',
                        ),
                    ),
                ),

            ),
        ),
    ),

    'diagnostics' => array(
        'ZF' => array(
            'PHP Version' => array('PhpVersion', '5.3.3'),
        )
    )
);
