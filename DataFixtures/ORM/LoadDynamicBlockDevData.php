<?php

namespace Awaresoft\DynamicBlockBundle\DataFixtures\ORM;

use Application\DynamicBlockBundle\Entity\Block;
use Application\DynamicBlockBundle\Entity\BlockHasField;
use Application\DynamicBlockBundle\Entity\Template;
use Application\DynamicBlockBundle\Entity\TemplateHasField;
use Awaresoft\Doctrine\Common\DataFixtures\AbstractFixture as AwaresoftAbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class LoadDynamicBlockDevData
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class LoadDynamicBlockDevData extends AwaresoftAbstractFixture
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
        return array('dev');
    }

    /**
     * {@inheritDoc}
     */
    public function doLoad(ObjectManager $manager)
    {
        $this->createDynamicBlock($manager);
    }

    protected function createDynamicBlock(ObjectManager $manager)
    {
        $faker = $this->getFaker();

        $template = new Template();
        $template
            ->setName($faker->text('10'))
            ->setSite($this->getReference('page-site'))
            ->setPath('TestBundle:DynamicBlock:test.html.twig')
            ->setEnabled(true);
        $manager->persist($template);

        $templateFields[0] = new TemplateHasField();
        $templateFields[0]
            ->setName($faker->text('15'))
            ->setTemplate($template)
            ->setType('text');
        $manager->persist($templateFields[0]);

        $templateFields[1] = new TemplateHasField();
        $templateFields[1]
            ->setName($faker->text('20'))
            ->setTemplate($template)
            ->setType('text');
        $manager->persist($templateFields[1]);

        $template->setFields($templateFields);

        $block = new Block();
        $block
            ->setName($faker->text('12'))
            ->setSite($this->getReference('page-site'))
            ->setTemplate($template)
            ->setEnabled(true);
        $manager->persist($block);

        $blockFields[0] = new BlockHasField();
        $blockFields[0]
            ->setBlock($block)
            ->setTemplateField($templateFields[0])
            ->setContent($faker->text('255'));
        $manager->persist($blockFields[0]);

        $blockFields[1] = new BlockHasField();
        $blockFields[1]
            ->setBlock($block)
            ->setTemplateField($templateFields[1])
            ->setContent($faker->text('255'));
        $manager->persist($blockFields[1]);

        $block->setFields($blockFields);

        $manager->flush();
    }
}
