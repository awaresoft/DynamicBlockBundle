<?php

namespace Awaresoft\DynamicBlockBundle\Block;

use Application\DynamicBlockBundle\Entity\Block;
use Awaresoft\Sonata\BlockBundle\Block\BaseBlockService;
use Awaresoft\DynamicBlockBundle\Entity\Repository\BlockRepository;
use Awaresoft\DynamicBlockBundle\Exception\BlockNotExistsException;
use Awaresoft\DynamicBlockBundle\Exception\TemplateNotFoundException;
use Doctrine\ORM\EntityRepository;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BlockService
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class DynamicBlockBlock extends BaseBlockService
{
    /**
     * Set default settings
     *
     * @param OptionsResolver $resolver
     */
    public function configureSettings(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'template' => 'AwaresoftDynamicBlockBundle:Block:block_block.html.twig',
            'dynamicBlock' => null,
            'name' => null,
            'containerClass' => null
        ));
    }

    /**
     * @param FormMapper $formMapper
     * @param BlockInterface $block
     */
    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {
        $dynamicBlock = null;
        $dynamicBlockId = $block->getSetting('dynamicBlock');

        if ($dynamicBlockId) {
            $dynamicBlock = $this->getDynamicBlockRepository()->find($dynamicBlockId);
        }

        $formMapper->add('settings', 'sonata_type_immutable_array', array(
            'keys' => array(
                array('dynamicBlock', 'entity', array(
                    'class' => 'ApplicationDynamicBlockBundle:Block',
                    'query_builder' => function (EntityRepository $er) use ($block) {
                        return $er->createQueryBuilder('b')
                            ->where('b.enabled = :enabled')
                            ->andWhere('b.site = :site')
                            ->setParameter('enabled', true)
                            ->setParameter('site', $block->getPage()->getSite());
                    },
                    'data' => $dynamicBlock
                )),
                array('containerClass', 'text', array('required' => false)),
            )
        ));
    }

    /**
     * Execute block
     *
     * @param BlockContextInterface $blockContext
     * @param Response|null $response
     * @return Response
     * @throws BlockNotExistsException
     * @throws TemplateNotFoundException
     */
    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $dynamicBlock = null;

        if ($dynamicBlockName = $blockContext->getSetting('name')) {
            $dynamicBlock = $this->getDynamicBlockRepository()->findOneByName($dynamicBlockName);
        }

        if (!$dynamicBlock) {
            $dynamicBlockId = $blockContext->getBlock()->getSetting('dynamicBlock') ?: $blockContext->getSetting('dynamicBlock');

            if ($dynamicBlockId) {
                $dynamicBlock = $this->getDynamicBlockRepository()->find($dynamicBlockId);
            }
        }

        if (!$dynamicBlock) {
            throw new BlockNotExistsException();
        }

        if (!$this->container->get('templating')->exists($dynamicBlock->getTemplate()->getPath())) {
            throw new TemplateNotFoundException();
        }

        $fields = $dynamicBlock->getFields();

        foreach ($fields as $key => $field) {
            if ($field->getTemplateField()->checkType() == 'entity') {
                $object = $this->getEntityManager()->getRepository($field->getTemplateField()->getType())->find($field->getContent());
                $field->setContent($object);
                $fields[$key] = $field;
            }
        }

        return $this->renderResponse($blockContext->getTemplate(), array(
            'static_block' => $dynamicBlock,
            'fields' => $fields,
            'block_context' => $blockContext,
            'block' => $blockContext->getBlock(),
        ), $response);
    }

    /**
     * {@inheritdoc}
     */
    public function prePersist(BlockInterface $block)
    {
        $block->setSetting('dynamicBlock', is_object($block->getSetting('dynamicBlock')) ? $block->getSetting('dynamicBlock')->getId() : null);
    }

    /**
     * {@inheritdoc}
     */
    public function preUpdate(BlockInterface $block)
    {
        $block->setSetting('dynamicBlock', is_object($block->getSetting('dynamicBlock')) ? $block->getSetting('dynamicBlock')->getId() : null);
    }

    /**
     * @return BlockRepository
     */
    protected function getDynamicBlockRepository()
    {
        return $this->getEntityManager()->getRepository('ApplicationDynamicBlockBundle:Block');
    }
}