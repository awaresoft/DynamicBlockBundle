<?php

namespace Awaresoft\DynamicBlockBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\MappedSuperclass()
 *
 * @author Bartosz Malec <b.malec@awaresoft.pl>
 */
class TemplateHasField
{
    /**
     * Available field types in admin
     */
    const AVAILABLE_FIELD_TYPES = [
        'content' => [
            'text' => 'text',
            'wysiwyg' => 'wysiwyg'
        ],
        'entity' => [

        ]
    ];

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @var int
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Application\DynamicBlockBundle\Entity\Template", inversedBy="fields")
     *
     * @var Template
     */
    protected $template;

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
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     */
    protected $position;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $type;

    /**
     * @ORM\OneToMany(targetEntity="Application\DynamicBlockBundle\Entity\BlockHasField", cascade={"all"}, mappedBy="templateField")
     *
     * @var ArrayCollection
     */
    protected $blockFields;

    public function __construct()
    {
        $this->blockFields = new ArrayCollection();
        $this->enabled = false;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     *
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Check which type belongs to object
     *
     * @return string
     */
    public function checkType()
    {
        if ($this->isEntity()) {
            return 'entity';
        }

        return $this->type;
    }

    /**
     * @return BlockHasField[]
     */
    public function getBlockFields()
    {
        return $this->blockFields;
    }

    /**
     * @param BlockHasField[] $blockFields
     *
     * @return TemplateHasField
     */
    public function setBlockFields($blockFields)
    {
        $this->blockFields = $blockFields;

        return $this;
    }

    /**
     * @param BlockHasField $block
     */
    public function addBlockField(BlockHasField $block)
    {
        $this->blockFields[] = $block;
    }

    /**
     * Check if type is entity
     *
     * @return bool
     */
    public function isEntity()
    {
        return strstr($this->type, ':') !== false ? true : false;
    }
}