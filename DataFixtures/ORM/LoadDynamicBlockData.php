<?php

namespace Awaresoft\DynamicBlockBundle\DataFixtures\ORM;

use Awaresoft\Doctrine\Common\DataFixtures\AbstractFixture as AwaresoftAbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Awaresoft\SettingBundle\Entity\Setting;
use Awaresoft\SettingBundle\Entity\SettingHasFields;

/**
 * Class LoadDynamicBlockData
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class LoadDynamicBlockData extends AwaresoftAbstractFixture
{
    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }

    /**
     * {@inheritDoc}
     */
    public function getEnvironments()
    {
        return array('dev', 'prod');
    }

    /**
     * {@inheritDoc}
     */
    public function doLoad(ObjectManager $manager)
    {
        $this->createSettings($manager);
    }

    /**
     * Create dynamic block settting
     *
     * @param ObjectManager $manager
     */
    protected function createSettings(ObjectManager $manager)
    {
        $setting = new Setting();
        $setting
            ->setName('DYNAMIC_BLOCK')
            ->setEnabled(true)
            ->setHidden(true)
            ->setInfo('Dynamic block module parameters.');
        $manager->persist($setting);

        $settingField = new SettingHasFields();
        $settingField->setSetting($setting);
        $settingField->setName('PROVIDER');
        $settingField->setValue('Awaresoft');
        $settingField->setInfo('Bundle provider.');
        $settingField->setEnabled(true);
        $manager->persist($settingField);

        $settingField = new SettingHasFields();
        $settingField->setSetting($setting);
        $settingField->setName('BUNDLE');
        $settingField->setValue('DynamicBlockBundle');
        $settingField->setInfo('Bundle name.');
        $settingField->setEnabled(true);
        $manager->persist($settingField);

        $settingField = new SettingHasFields();
        $settingField->setSetting($setting);
        $settingField->setName('CONTROLLER');
        $settingField->setValue('DynamicBlock');
        $settingField->setInfo('Directory (controller) in bundle.');
        $settingField->setEnabled(true);
        $manager->persist($settingField);

        $settingField = new SettingHasFields();
        $settingField->setSetting($setting);
        $settingField->setName('FORMAT');
        $settingField->setValue('html');
        $settingField->setInfo('Format of file e.g. html.');
        $settingField->setEnabled(true);
        $manager->persist($settingField);

        $settingField = new SettingHasFields();
        $settingField->setSetting($setting);
        $settingField->setName('ENGINE');
        $settingField->setValue('twig');
        $settingField->setInfo('Engine of file, e.g. twig.');
        $settingField->setEnabled(true);
        $manager->persist($settingField);

        $manager->flush();
    }
}
