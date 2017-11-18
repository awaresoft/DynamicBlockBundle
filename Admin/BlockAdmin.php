<?php

namespace Awaresoft\DynamicBlockBundle\Admin;

use Application\DynamicBlockBundle\Entity\Block;
use Application\DynamicBlockBundle\Entity\BlockHasField;
use Awaresoft\Sonata\AdminBundle\Admin\AbstractAdmin as AwaresoftAbstractAdmin;
use Awaresoft\DynamicBlockBundle\Entity\Repository\TemplateRepository;
use Awaresoft\DynamicBlockBundle\Entity\Repository\BlockHasFieldRepository;
use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;

/**
 * BlockAdmin class
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class BlockAdmin extends AwaresoftAbstractAdmin
{
    /**
     * @inheritdoc
     */
    protected $baseRoutePattern = 'awaresoft/dynamicblock/block';

    /**
     * @inheritdoc
     */
    protected $multisite = true;

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('site')
            ->add('template')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('enabled')
            ->add('deletable')
            ->add('fields');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('site')
            ->add('template')
            ->add('enabled', null, array('editable' => true));

        $editable = false;
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $editable = true;
        }

        $listMapper
            ->add('deletable', null, ['editable' => $editable]);

        $listMapper
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array()
                )
            ));
    }

    /**
     * {@inheritdoc}
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $this->prepareFilterMultisite($datagridMapper);

        $datagridMapper
            ->add('name')
            ->add('template.name')
            ->add('enabled')
            ->add('deletable');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /**
         * @var Block $object
         */
        $object = $this->getSubject();
        $this->em = $this->getEntityManager();

        $formMapper
            ->with($this->trans('admin.admin.form.group.main'), array('class' => 'col-xs-12 col-md-6'))->end()
            ->with($this->trans('admin.admin.form.group.fields'), array('class' => 'col-xs-12 col-md-6'))->end();

        $formMapper
            ->with($this->trans('admin.admin.form.group.main'))
            ->add('name')
            ->add('template', 'entity', array(
                'class' => 'ApplicationDynamicBlockBundle:Template',
                'required' => false,
                'query_builder' => function (TemplateRepository $repo) {
                    return $repo->queryAllEnabled();
                }
            ))
            ->add('enabled', null, array(
                'required' => false
            ));

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            $formMapper
                ->add('deletable', null, [
                    'required' => false,
                ]);
        }

        $formMapper
            ->add('site', null, array('required' => true, 'attr' => ['readonly' => true]));

        $formMapper
            ->end();

        if ($object && $object->getId() && $object->getTemplate()) {
            $this->checkFields();
            $fieldsHelp = $this->prepareFieldsHelp();

            $formMapper
                ->with($this->trans('admin.admin.form.group.fields'))
                ->add('fields', 'sonata_type_collection', array(
                    'btn_add' => false,
                    'type_options' => array('delete' => false),
                    'required' => false,
                    'by_reference' => false,
                    'label' => false,
                    'attr' => array(
                        'class' => 'special-fields'
                    )
                ), array(
                    'edit' => 'inline',
                    'inline' => 'table',
                ))
                ->end();

            $formMapper->setHelps(array(
                'fields' => $fieldsHelp,
                'name' => $this->trans('admin.admin.help.sluggable_field'),
            ));
        }
    }

    /**
     * Prepare help string for BlockFields
     *
     * @return string
     */
    protected function prepareFieldsHelp()
    {
        $string = array();
        $block = $this->getSubject();
        $blockFieldRepo = $this->getDynamicBlockHasFieldRepository();
        $blockFields = $blockFieldRepo->findByBlock($block);

        foreach ($blockFields as $i => $blockField) {
            $string[] = '<span class="special-field-info">Field  - ' . $blockField->getTemplateField()->getName() . '</span>';
        }

        return implode('<br />', $string);
    }

    /**
     * Check if fields or template has been changed for selected block
     * 1) check if all fields are visible
     * 2) check if template or some fields has been changed
     */
    protected function checkFields()
    {
        /**
         * @var $block Block
         */
        $block = $this->getSubject();

        // check if all fields are created
        foreach ($block->getTemplate()->getFields() as $templateField) {
            if (!$block->getFieldByTemplateField($templateField)) {
                $newField = new BlockHasField();
                $newField->setTemplateField($templateField);
                $block->addField($newField);
                $this->em->persist($newField);
            }
        }

        $this->em->flush();

        // check if template or some fields has been changed
        foreach ($block->getFields() as $field) {;
            if (!$block->getTemplate()->getFields()->contains($field->getTemplateField())) {
                $block->removeField($field);
                $this->em->remove($field);
            }
        }

        $this->em->flush();
    }

    /**
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getConfigurationPool()->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * @return BlockHasFieldRepository
     */
    protected function getDynamicBlockHasFieldRepository()
    {
        return $this->em->getRepository('ApplicationDynamicBlockBundle:BlockHasField');
    }
}
