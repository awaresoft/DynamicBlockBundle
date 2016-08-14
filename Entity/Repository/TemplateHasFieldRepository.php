<?php

namespace Awaresoft\DynamicBlockBundle\Entity\Repository;

use Awaresoft\DynamicBlockBundle\Entity\Template;
use Awaresoft\DynamicBlockBundle\Entity\TemplateHasField;
use Doctrine\ORM\EntityRepository;

/**
 * Class DynamicBlockTemplateHasFieldRepository
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class TemplateHasFieldRepository extends EntityRepository
{
    /**
     * @param Template $template
     *
     * @return TemplateHasField[]
     */
    public function findByTemplate(Template $template)
    {
        return $this->findBy(array(
            'template' => $template
        ));
    }
}