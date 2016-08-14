<?php

namespace Awaresoft\DynamicBlockBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class DynamicBlockTemplateRepository
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class TemplateRepository extends EntityRepository
{
    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function queryAllEnabled()
    {
        return $this->createQueryBuilder('o')
            ->where('o.enabled = :enabled')
            ->setParameter('enabled', true);
    }
}