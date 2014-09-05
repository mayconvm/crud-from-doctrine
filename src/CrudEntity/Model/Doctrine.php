<?php

namespace CrudEntity;

class Doctrine
{
    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     * Method construct
     * @param Doctrine\ORM\EntityManager $entityManager Entity manager doctrine
     */
    public function __construct(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Método para ler o annotations do doctrine
     * @param  string $entidade namespace da classe
     * @return [type]           Objeto de mapeamento da entidade
     */
    public function readEntityDoctrine($entidade)
    {
        return $this->entityManager->getClassMetadata($entidade);
    }

    /**
     * Method for read annotation entity
     * @param  Doctrine\ORM\EntityManager $entityManager Entity Manager doctrin
     * @param  class                   $entity        Class entity
     * @return array                                  Object of description entity
     */
    public static function readEntity(\Doctrine\ORM\EntityManager $entityManager, $entity)
    {
        return $entityManager->getClassMetadata($entity);
    }
}
