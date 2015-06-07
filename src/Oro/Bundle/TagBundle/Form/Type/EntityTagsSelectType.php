<?php

namespace Oro\Bundle\TagBundle\Form\Type;

use Doctrine\Bundle\DoctrineBundle\Registry;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

use Oro\Bundle\SecurityBundle\ORM\Walker\AclHelper;

class EntityTagsSelectType extends AbstractType
{
    /** @var AclHelper */
    protected $aclHelper;

    /** @var Registry */
    protected $doctrine;

    public function __construct(AclHelper $aclHelper, Registry $doctrine)
    {
        $this->doctrine  = $doctrine;
        $this->aclHelper = $aclHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'oro_tag_entity_tags_selector';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'entity';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $choices = function (Options $options) {
            if (empty($options['entity_class'])) {
                return [];
            }

            return $this->aclHelper->apply($this->doctrine->getRepository('OroTagBundle:Tag')
                ->createQueryBuilder('t')
                ->join('t.tagging', 'tagging')
                ->where('tagging.entityName = :entity')
                ->setParameter('entity', $options['entity_class']))
                ->getResult();
        };

        $resolver->setDefaults(
            [
                'class'        => 'OroTagBundle:Tag',
                'property'     => 'name',
                'entity_class' => null,
                'choices'      => $choices
            ]
        );
    }
}
