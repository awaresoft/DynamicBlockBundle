<?php

namespace Awaresoft\DynamicBlockBundle\Entity\Repository;

use Awaresoft\DynamicBlockBundle\Entity\Block;
use Awaresoft\DynamicBlockBundle\Entity\BlockHasField;
use Doctrine\ORM\EntityRepository;

/**
 * Class DynamicBlockHasFieldRepository
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class BlockHasFieldRepository extends EntityRepository
{
    /**
     * @param Block $block
     *
     * @return BlockHasField[]
     */
    public function findByBlock(Block $block)
    {
        return $this->findBy(array(
            'block' => $block
        ));
    }
}