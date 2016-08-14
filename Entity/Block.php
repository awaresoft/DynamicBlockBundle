<?php

namespace Awaresoft\DynamicBlockBundle\Entity;

use Application\DynamicBlockBundle\Entity\BlockHasField;
use Application\DynamicBlockBundle\Entity\Template;
use Awaresoft\Sonata\PageBundle\Entity\Site;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass()
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class Block
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Awaresoft\Sonata\PageBundle\Entity\Site")
     *
     * @var Site
     */
    protected $site;

    /**
     * @ORM\ManyToOne(targetEntity="Application\DynamicBlockBundle\Entity\Template")
     *
     * @Assert\NotBlank()
     *
     * @var Template
     */
    protected $template;

    /**
     * @ORM\OneToMany(targetEntity="Application\DynamicBlockBundle\Entity\BlockHasField", cascade={"all"}, mappedBy="block")
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $fields;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="create")
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="update")
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var boolean
     */
    protected $enabled;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var boolean
     */
    protected $deletable;

    public function __construct()
    {
        $this->name = '';
        $this->fields = new ArrayCollection();
        $this->deletable = true;
        $this->enabled = true;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param Template $template
     *
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isDeletable()
    {
        return $this->deletable;
    }

    /**
     * @param boolean $deletable
     *
     * @return Block
     */
    public function setDeletable($deletable)
    {
        $this->deletable = $deletable;

        return $this;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $fields
     *
     * @return $this
     */
    public function setFields($fields)
    {
        $this->fields = new ArrayCollection();

        foreach ($fields as $field) {
            $this->addField($field);
        }

        return $this;
    }

    /**
     * @param BlockHasField $field
     */
    public function addField(BlockHasField $field)
    {
        $field->setBlock($this);
        $field->getTemplateField()->addBlockField($field);
        $this->fields[] = $field;
    }

    /**
     * @param BlockHasField $field
     */
    public function removeField(BlockHasField $field)
    {
        $this->fields->removeElement($field);
    }

    /**
     * @param TemplateHasField $templateField
     *
     * @return BlockHasField|null
     */
    public function getFieldByTemplateField(TemplateHasField $templateField)
    {
        foreach ($this->fields as $field) {
            if ($field->getTemplateField() === $templateField) {
                return $field;
            }
        }

        return null;
    }

    /**
     * @return Site
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param Site $site
     *
     * @return $this
     */
    public function setSite($site)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}