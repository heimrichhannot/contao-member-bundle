<?php

/*
 * Copyright (c) 2018 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\MemberBundle\Filter;

use Contao\System;
use HeimrichHannot\FilterBundle\Filter\Type\ChoiceType;
use HeimrichHannot\FilterBundle\Model\FilterConfigElementModel;
use HeimrichHannot\FilterBundle\QueryBuilder\FilterQueryBuilder;
use HeimrichHannot\UtilsBundle\Database\DatabaseUtil;
use Symfony\Component\Form\FormBuilderInterface;

class CityType extends ChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function buildQuery(FilterQueryBuilder $builder, FilterConfigElementModel $element)
    {
        $filter = $this->config->getFilter();
        $data = $this->config->getData();

        if (isset($data['city']) && null !== $data['city'] && '' !== $data['city']) {
            $value = $data['city'];
            $alias = 'tl_member_address_'.$element->field;
            $tlMember = $filter['dataContainer'];
            $builder->leftJoin($tlMember, 'tl_member_address', $alias, "$alias.pid=$tlMember.id");

            $orX = $builder->expr()->orX($tlMember.'.city= :cityValue', $alias.".city= :$alias");
            $builder->andWhere($orX);

            $builder->setParameter(':cityValue', $value, \PDO::PARAM_STR);
            $builder->setParameter(":$alias", $value, \PDO::PARAM_STR);
            $builder->groupBy($tlMember.'.id');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FilterConfigElementModel $element, FormBuilderInterface $builder)
    {
        $builder->add($this->getName($element), \Symfony\Component\Form\Extension\Core\Type\ChoiceType::class, $this->getOptions($element, $builder));
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices(FilterConfigElementModel $element)
    {
        return System::getContainer()->get('huh.member.choice.city')->getCachedChoices([$element, $this->config->getFilter()]);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOperator(FilterConfigElementModel $element)
    {
        return DatabaseUtil::OPERATOR_EQUAL;
    }
}
