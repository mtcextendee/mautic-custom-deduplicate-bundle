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
        $this->setEvent($event);

        $buttons = [
            [
                'label'        => 'plugin.custom.deduplication',
                'icon'         => 'fa fa-user',
                'context'      => 'contact',
            ],
        ];

        foreach ($buttons as $button) {
            $this->addButtonGenerator($button['label'], $button['icon'], $button['context']);
        }
    }


    /**
     * @param        $objectAction
     * @param        $btnText
     * @param        $icon
     * @param        $context
     * @param int    $priority
     * @param null   $target
     * @param string $header
     *
     */
    private function addButtonGenerator($btnText, $icon, $context, $priority = 1, $target = null, $header = '')
    {
        $event    = $this->getEvent();
        $route    = $this->router->generate(
            'mautic_plugin_custom_deduplicate'
        );
        $attr     = [
            'href'        => $route,
            'data-toggle' => 'ajax',
            'data-method'       => 'POST',
            'data-confirm-text' => $this->translator->trans('plugin.custom.deduplication.continue'),
        ];

        switch ($target){
            case '_blank':
                $attr['data-toggle'] = '';
                $attr['data-method'] = '';
                $attr['target'] = $target;
                break;
            case '#MauticSharedModal':
                $attr['data-toggle'] = 'ajaxmodal';
                $attr['data-method'] = '';
                $attr['data-target'] = $target;
                $attr['data-header'] = $header;
                break;
        }

        $button =
            [
                'attr'      => $attr,
                'btnText'   => $this->translator->trans($btnText),
                'iconClass' => $icon,
                'priority'  => $priority,
            ];
        $event
            ->addButton(
                $button,
                ButtonHelper::LOCATION_PAGE_ACTIONS,
                ['mautic_'.$context.'_index', []]
            );
    }

    /**
     * @return mixed
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * @param mixed $event
     */
    public function setEvent($event)
    {
        $this->event = $event;
    }
}
