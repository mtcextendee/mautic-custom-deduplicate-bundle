<?php

namespace MauticPlugin\MauticCustomDeduplicateBundle\Integration;

use Mautic\LeadBundle\Form\Type\LeadFieldsType;
use Mautic\PluginBundle\Integration\AbstractIntegration;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilder;

class CustomDeduplicateIntegration extends AbstractIntegration
{
    /**
     * @return string
     */
    public function getName()
    {
        // should be the name of the integration
        return 'CustomDeduplicate';
    }

    /**
     * @return string
     */
    public function getAuthenticationType()
    {
        /* @see \Mautic\PluginBundle\Integration\AbstractIntegration::getAuthenticationType */
        return 'none';
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return 'plugins/MauticCustomDeduplicateBundle/Assets/img/icon.png';
    }

    /**
     * @param \Mautic\PluginBundle\Integration\Form|FormBuilder $builder
     * @param array                                             $data
     * @param string                                            $formArea
     */
    public function appendToForm(&$builder, $data, $formArea)
    {
        if ('features' == $formArea) {
            $builder->add(
                'uniqueFields',
                LeadFieldsType::class,
                [
                    'label'      => 'plugin.custom.deduplication.form.unique.fields',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'        => 'form-control',
                    ],
                    'required' => false,
                    'multiple' => true,
                ]
            );

            $builder->add(
                'skipFields',
                LeadFieldsType::class,
                [
                    'label'      => 'plugin.custom.deduplication.form.skipped.fields',
                    'label_attr' => ['class' => 'control-label'],
                    'attr'       => [
                        'class'        => 'form-control',
                    ],
                    'required' => false,
                    'multiple' => true,
                ]
            );
        }
    }
}
