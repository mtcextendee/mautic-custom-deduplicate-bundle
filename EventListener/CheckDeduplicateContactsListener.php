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

use Mautic\CoreBundle\EventListener\CommonSubscriber;
use Mautic\LeadBundle\Event\DuplicateContactsEvent;
use MauticPlugin\MauticCustomDeduplicateBundle\Deduplicate\CustomDuplications;
use MauticPlugin\MauticCustomDeduplicateBundle\Integration\ECronTesterIntegration;

class CheckDeduplicateContactsListener extends CommonSubscriber
{

    /**
     * @var CustomDuplications
     */
    private $customDuplications;

    /**
     * CheckDeduplicateContactsListener constructor.
     *
     * @param CustomDuplications $customDuplications
     */
    public function __construct(CustomDuplications $customDuplications)
    {

        $this->customDuplications = $customDuplications;
    }

    public static function getSubscribedEvents()
    {
        return [
          //  LeadEvents::CHECK_FOR_DUPLICATE_CONTACTS_EVENT => ['checkForDuplicateContacts', 0],
        ];
    }

    public function checkForDuplicateContacts(DuplicateContactsEvent $event)
    {
        if (!defined('MAUTIC_CUSTOM_DEDUPLICATE_COMMAND')) {
            return;
        }
        $duplications = $this->customDuplications->customCheckForDuplicateContacts($event->getFields());
        $event->setDuplicates($duplications);
    }

}
