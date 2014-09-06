<?php

namespace CrudEntity\Model\Form\Doctrine;

class Type
{
    /**
     * Método para retornar um tipo de input para o formato no banco
     * @param  string $type Tipo do campo no banco
     * @return strin       Tipo do input
     */
    public static function convertTypeDoctrine($type)
    {
        switch($type)
        {
            case 'integer':
            case 'decimal':
            case 'float':
            case 'string':
            case 'date':
                return 'text';
            case 'text':
                return 'textarea';
        }
    }
}
