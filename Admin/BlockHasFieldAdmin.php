<?php

namespace Awaresoft\DynamicBlockBundle\Admin;

use Awaresoft\Sonata\AdminBundle\Admin\AbstractAdmin as AwaresoftAbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class BlockHasFieldAdmin extends AwaresoftAbstractAdmin
{
    /**
     * @inheritdoc
     */
    protected $baseRoutePattern = 'awaresoft/dynamicblock/blockhasfield';

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('block.name')
            ->add('templateField.name');
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $object = $this->getSubject();

        if($object) {
            $type = $object->getTemplateField()->checkType();
            $em = $this->getConfigurationPool()->getContainer()->get('doctrine.orm.entity_manager');
            $data = null;

            if ($type == 'wysiwyg') {
                $formMapper
                    ->add('content', 'textarea', array('required' => false, 'attr' => array('class' => 'ckeditor')));
            } else if ($type == 'entity') {
                if ($object->getContent()) {
                    $data = $em->getRepository($object->getTemplateField()->getType())->find($object->getContent());
                }
                $formMapper
                    ->add('content', 'entity', array(
                        'class' => $object->getTemplateField()->getType(),
                        'data' => $data,
                        'required' => false
                    ));
            } else {
                $formMapper
                    ->add('content', 'textarea');
            }
        } else {
            $formMapper
                ->add('content', 'textarea');
        }
    }

}
