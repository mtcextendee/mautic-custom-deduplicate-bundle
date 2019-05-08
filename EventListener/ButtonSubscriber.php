<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticCustomDeduplicateBundle\EventListener;

use Mautic\CoreBundle\CoreEvents;
use Mautic\CoreBundle\Event\CustomButtonEvent;
use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\CoreBundle\Templating\Helper\ButtonHelper;
use Mautic\EmailBundle\Entity\Email;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use MauticPlugin\MauticCustomDeduplicateBundle\Integration\ECronTesterIntegration;
use MauticPlugin\MauticMailTesterBundle\Integration\MailTesterIntegration;

class ButtonSubscriber extends CommonSubscriber
{
    /**
     * @var IntegrationHelper
     */
    protected $integrationHelper;

    private $event;

    private $objectId;

    /**
     * ButtonSubscriber constructor.
     *
     * @param IntegrationHelper $helper
     */
    public function __construct(IntegrationHelper $integrationHelper)
    {
        $this->integrationHelper = $integrationHelper;
    }

    public static function getSubscribedEvents()
    {
        return [
            CoreEvents::VIEW_INJECT_CUSTOM_BUTTONS => ['injectViewButtons', 0],
        ];
    }

    /**
     * @param CustomButtonEvent $event
     */
    public function injectViewButtons(CustomButtonEvent $event)
    {
        $integration = $this->integrationHelper->getIntegrationObject('CustomDeduplicate');

        if (false === $integration || !$integration->getIntegrationSettings()->getIsPublished()) {
            return;
        }

        $route = $this->router->generate(
            'mautic_plugin_custom_deduplicate'
        );
        $attr  = [
            'href'              => $route,
            'data-toggle'       => 'ajax',
            'data-method'       => 'POST',
            'data-confirm-text' => $this->translator->trans('plugin.custom.deduplication.continue'),
        ];

        $button =
            [
                'attr'     => $attr,
                'priority' => -1,
                'confirm'  => [
                    'btnClass'      => false,
                    'iconClass'     => 'fa fa-clone',
                    'btnText'       => $this->translator->trans('plugin.custom.deduplication'),
                    'message'       => $this->translator->trans('plugin.custom.deduplication.command.alert'),
                    'confirmText'   => $this->translator->trans('plugin.custom.deduplication.command.run'),
                    'confirmAction' => $route,
                ],
            ];

        $event
            ->addButton(
                $button,
                ButtonHelper::LOCATION_PAGE_ACTIONS,
                ['mautic_contact_index', []]
            );
    }

}
