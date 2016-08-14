<?php

namespace Awaresoft\DynamicBlockBundle\Admin;

use Application\DynamicBlockBundle\Entity\Template;
use Awaresoft\SettingBundle\Entity\Setting;
use Awaresoft\Sonata\AdminBundle\Admin\AbstractAdmin as AwaresoftAbstractAdmin;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Symfony\Bundle\FrameworkBundle\Templating\TemplateReference;
use Symfony\Component\Finder\Finder;

/**
 * BlockAdmin class
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class TemplateAdmin extends AwaresoftAbstractAdmin
{
    /**
     * @inheritdoc
     */
    protected $baseRoutePattern = 'awaresoft/dynamicblock/template';

    /**
     * @param Template $object
     * @return void
     */
    public function preUpdate($object)
    {
        $this->updateCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('path')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('enabled')
            ->add('fields');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('name')
            ->add('path')
            ->add('enabled', null, array('editable' => true));

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
        $datagridMapper
            ->add('name')
            ->add('path')
            ->add('enabled');
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with($this->trans('admin.admin.form.group.main'), array('class' => 'col-xs-12 col-md-6'))->end()
            ->with($this->trans('admin.admin.form.group.fields'), array('class' => 'col-xs-12 col-md-6'))->end();

        $formMapper
            ->with($this->trans('admin.admin.form.group.main'))
            ->add('name');

        $this->showPathField($formMapper);

        $formMapper
            ->add('enabled', null, array(
                'required' => false
            ))
            ->end();

        $formMapper
            ->with($this->trans('admin.admin.form.group.fields'))
            ->add('fields', 'sonata_type_collection', array(
                'required' => false,
                'by_reference' => false,
                'label' => false
            ), array(
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => false
            ))
            ->end();

        $formMapper->setHelps(array(
            'path' => $this->trans('admin.admin.help.only_for_admins'),
            'name' => $this->trans('admin.admin.help.sluggable_field'),
        ));
    }

    /**
     * Prepare path field
     *
     * @param FormMapper $formMapper
     *
     * @return TemplateAdmin
     */
    protected function showPathField(FormMapper $formMapper)
    {
        if ($templatePath = $this->getTemplateSetting()) {
            if ($templates = $this->findTemplate($templatePath)) {
                return $this->preparePathFieldFromSetting($formMapper, $templates);
            }

            return $this->preparePathField($formMapper);
        }

        return $this->preparePathField($formMapper);
    }

    /**
     * Prepare select if find files in catalogue from settings
     *
     * @param FormMapper $formMapper
     * @param $templates
     *
     * @return $this
     */
    protected function preparePathFieldFromSetting(FormMapper $formMapper, $templates)
    {
        return $formMapper
            ->add('path', 'choice', array(
                'choices' => $templates
            ));
    }

    /**
     * Prepare simple input
     *
     * @param FormMapper $formMapper
     *
     * @return $this
     */
    protected function preparePathField(FormMapper $formMapper)
    {
        return $formMapper
            ->add('path');
    }

    /**
     * Check if template setting is set and return setting object
     *
     * @return Setting|null
     */
    protected function getTemplateSetting()
    {
        $settingService = $this->getConfigurationPool()->getContainer()->get('awaresoft.setting');
        $dynamicBlockSetting = $settingService->get('DYNAMIC_BLOCK', true);

        if ($dynamicBlockSetting && $dynamicBlockSetting->isEnabled()) {
            $provider = $dynamicBlockSetting->getFields()->get('PROVIDER');
            $bundle = $dynamicBlockSetting->getFields()->get('BUNDLE');
            $controller = $dynamicBlockSetting->getFields()->get('CONTROLLER');
            $format = $dynamicBlockSetting->getFields()->get('FORMAT');
            $engine = $dynamicBlockSetting->getFields()->get('ENGINE');

            if (
                $provider && $provider->isEnabled() && $provider->getValue()
                && $bundle && $bundle->isEnabled() && $bundle->getValue()
                && $controller && $controller->isEnabled() && $controller->getValue()
                && $format && $format->isEnabled() && $format->getValue()
                && $engine && $engine->isEnabled() && $engine->getValue()
            ) {
                return $provider->getValue() . '/' . $bundle->getValue() . '/Resources/views/' . $controller->getValue();
            }
        }

        return null;
    }

    /**
     * Find templates in application structure, and return names
     *
     * @param string $path
     *
     * @return array
     */
    protected function findTemplate($path)
    {
        $settingService = $this->getConfigurationPool()->getContainer()->get('awaresoft.setting');
        $dynamicBlockSetting = $settingService->get('DYNAMIC_BLOCK', true);
        $provider = $dynamicBlockSetting->getFields()->get('PROVIDER');
        $bundle = $dynamicBlockSetting->getFields()->get('BUNDLE');
        $controller = $dynamicBlockSetting->getFields()->get('CONTROLLER');
        $format = $dynamicBlockSetting->getFields()->get('FORMAT');
        $engine = $dynamicBlockSetting->getFields()->get('ENGINE');

        $documentRoot = $this->configurationPool->getContainer()->get('request')->server->get('DOCUMENT_ROOT');
        $finder = new Finder();
        $finder->files()->in($documentRoot . '/../src/' . $path)->depth(0);
        $files = [];

        foreach ($finder as $file) {
            $absolutePath = str_replace('.' . $format->getValue() . '.' . $engine->getValue(), '', $file->getRelativePathname());
            $templateReference = new TemplateReference($provider->getValue() . $bundle->getValue(), $controller->getValue(), $absolutePath, $format->getValue(), $engine->getValue());
            $files[$templateReference->getLogicalName()] = $templateReference->getLogicalName();
        }

        return $files;
    }

}
