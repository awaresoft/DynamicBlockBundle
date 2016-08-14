<?php

namespace Awaresoft\DynamicBlockBundle\Controller;

use Awaresoft\Sonata\AdminBundle\Controller\CRUDController as AwaresoftCRUDController;
use Awaresoft\Sonata\AdminBundle\Reference\Type\EntityObjectType;
use Awaresoft\Sonata\AdminBundle\Traits\ControllerHelperTrait;

/**
 * Class TemplateCRUDController
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class TemplateCRUDController extends AwaresoftCRUDController
{
    use ControllerHelperTrait;

    /**
     * @inheritdoc
     */
    public function preDeleteAction($object)
    {
        $message = $this->checkObjectHasRelations($object, $this->admin, [
            new EntityObjectType($this->container, $object, 'Application\DynamicBlockBundle\Entity\Block', 'template', 'admin_awaresoft_dynamicblock_dynamicblock_edit'),
        ]);

        return $message;
    }

    /**
     * @inheritdoc
     */
    public function batchActionDeleteIsRelevant(array $idx)
    {
        $message = null;

        foreach ($idx as $id) {
            $object = $this->admin->getObject($id);
            $message .= $this->checkObjectHasRelations($object, $this->admin, [
                new EntityObjectType($this->container, $object, 'Application\DynamicBlockBundle\Entity\Block', 'template', 'admin_awaresoft_dynamicblock_dynamicblock_edit'),
            ]);
        }

        if (!$message) {
            return true;
        }

        return $message;
    }
}
