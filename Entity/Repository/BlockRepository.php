<?php

namespace Awaresoft\DynamicBlockBundle\Entity\Repository;

use Awaresoft\DynamicBlockBundle\Entity\Block;
use Doctrine\ORM\EntityRepository;
use Sonata\PageBundle\Model\SiteInterface;

/**
 * Class DynamicBlockRepository
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class BlockRepository extends EntityRepository
{
    /**
     * @param string $name
     * @param SiteInterface $site
     *
     * @return Block
     */
    public function findOneByNameAndSite($name, SiteInterface $site)
    {
        return $this->findOneBy(array(
            'name' => $name,
            'site' => $site,
        ));
    }
}