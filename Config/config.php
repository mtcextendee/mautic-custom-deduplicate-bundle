<?php

return [
    'name'        => 'MauticCustomDeduplicateBundle',
    'description' => 'Custom deduplicate for Mautic',
    'author'      => 'mtcextendee.com',
    'version'     => '1.0.1',
    'routes'      => [
        'main' => [
            'mautic_plugin_custom_deduplicate' => [
                'path'       => '/custom/deduplicate',
                'controller' => 'MauticCustomDeduplicateBundle:CustomDeduplicate:run',
            ],
        ],
    ],
    'services'    => [
        'events' => [
            'mautic.plugin.custom.deduplicate.button.subscriber' => [
                'class'     => \MauticPlugin\MauticCustomDeduplicateBundle\EventListener\ButtonSubscriber::class,
                'arguments' => [
                    'mautic.helper.integration',
                    'router',
                    'translator',
                ],
            ],
            'mautic.plugin.custom.deduplicate.contacts.subscriber' => [
                'class'     => \MauticPlugin\MauticCustomDeduplicateBundle\EventListener\CheckDeduplicateContactsListener::class,
                'arguments' => [
                    'mautic.plugin.custom.duplications',
                ],
            ],
        ],
        'others' => [
            'mautic.plugin.custom.duplications' => [
                'class'     => \MauticPlugin\MauticCustomDeduplicateBundle\Deduplicate\CustomDuplications::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                    'mautic.point.model.trigger',
                    'mautic.lead.repository.lead',
                    'mautic.plugin.custom.duplicate.fields',
                ],
            ],
            'mautic.plugin.custom.duplicate.fields' => [
                'class'     => \MauticPlugin\MauticCustomDeduplicateBundle\Deduplicate\Fields::class,
                'arguments' => [
                    'mautic.helper.integration',
                ],
            ],
            'mautic.plugin.custom.duplicate.command.execute' => [
                'class'     => \MauticPlugin\MauticCustomDeduplicateBundle\Deduplicate\CommandExecution::class,
                'arguments' => [
                    'mautic.helper.core_parameters',
                ],
            ],
        ],
        'command'=> [
            'mautic.plugin.custom.deduplicate.command' => [
                'class'     => \MauticPlugin\MauticCustomDeduplicateBundle\Command\CustomDeduplicateCommand::class,
                'arguments' => [
                    'mautic.lead.deduper',
                    'translator',
                    'mautic.core.model.notification',
                    'mautic.user.model.user',
                    'mautic.helper.integration',
                    'monolog.logger.mautic',
                ],
                'tag' => 'console.command',
            ],
        ],
        'integrations' => [
            'mautic.integration.customdeduplicate' => [
                'class'     => \MauticPlugin\MauticCustomDeduplicateBundle\Integration\CustomDeduplicateIntegration::class,
                'arguments' => [
                    'event_dispatcher',
                    'mautic.helper.cache_storage',
                    'doctrine.orm.entity_manager',
                    'session',
                    'request_stack',
                    'router',
                    'translator',
                    'monolog.logger.mautic',
                    'mautic.helper.encryption',
                    'mautic.lead.model.lead',
                    'mautic.lead.model.company',
                    'mautic.helper.paths',
                    'mautic.core.model.notification',
                    'mautic.lead.model.field',
                    'mautic.plugin.model.integration_entity',
                    'mautic.lead.model.dnc',
                ],
            ],
        ],
    ],
];
