<?php

namespace Awaresoft\DynamicBlockBundle\Admin;

use Awaresoft\Sonata\AdminBundle\Admin\AbstractAdmin as AwaresoftAbstractAdmin;
use Application\DynamicBlockBundle\Entity\TemplateHasField;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

/**
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class TemplateHasFieldAdmin extends AwaresoftAbstractAdmin
{
    /**
     * @inheritdoc
     */
    protected $baseRoutePattern = 'awaresoft/dynamicblock/templatehasfield';

    /**
     * @param \Sonata\AdminBundle\Datagrid\ListMapper $listMapper
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('name')
            ->add('type')
        ;
    }

    /**
     * @param \Sonata\AdminBundle\Form\FormMapper $formMapper
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $choices = TemplateHasField::AVAILABLE_FIELD_TYPES;

        $formMapper
            ->add('name')
            ->add('type', 'choice', array(
                'choices' => $choices
            ))
        ;
    }
}
