<?php

namespace CrudEntity\Model\Form\Doctrine;

use CrudEntity\Model\Form\Doctrine\Type as ModelType;

class ArrayForm
{

    /**
     * Method parse doctrine to array
     * @param  Doctrine\ORM\Mapping\ClassMetada $class
     * @return array
     */
    public static function entityToForm (\Doctrine\ORM\Mapping\ClassMetadata $class)
    {
        $elements = array();

        $inputs = $class->fieldMappings;

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
}
