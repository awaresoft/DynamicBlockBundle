<?php

namespace Awaresoft\DynamicBlockBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class DynamicBlockExtension extends \Twig_Extension
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('display_dynamic_block_content', [$this, 'displayDynamicBlockContent'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'dynamic_block_extensions';
    }

    /**
     * @param $name
     *
     * @return null
     */
    public function displayDynamicBlockContent($name)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $cmsPage = $this->container->get('sonata.page.cms_manager_selector')->retrieve();
        $page = $cmsPage->getCurrentPage();
        $site = null;

        if ($page) {
            $site = $page->getSite();
        }

        $dynamicBlock = $em->getRepository('ApplicationDynamicBlockBundle:Block')->findOneByNameAndSite($name, $site);

        if (!$dynamicBlock) {
            return null;
        }

        $blockInterface = $this->container->get('sonata.block.context_manager.default');
        $blockService = $this->container->get('awaresoft.dynamic_block.block.dynamic_block');
        $blockContext = $blockInterface->get(['type' => 'awaresoft.dynamic_block.block.dynamic_block']);
        $blockContext->getBlock()->setSetting('dynamicBlock', $dynamicBlock->getId());
        $blockContent = $blockService->execute($blockContext);

        return $blockContent->getContent();
    }

    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }
}
