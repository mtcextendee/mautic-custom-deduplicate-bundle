<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticCustomDeduplicateBundle\Deduplicate;

use Doctrine\ORM\EntityManager;
use Mautic\LeadBundle\Entity\Lead;
use Mautic\LeadBundle\Entity\LeadRepository;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use Mautic\PointBundle\Model\TriggerModel;

class Fields
{
    /**
     * @var IntegrationHelper
     */
    private $integrationHelper;

    /**
     * @var array
     */
    private $settings;

    public function __construct(IntegrationHelper $integrationHelper)
    {
        $this->integrationHelper = $integrationHelper;
        $integration             = $this->integrationHelper->getIntegrationObject('CustomDeduplicate');
        if ($integration && $integration->getIntegrationSettings()->getIsPublished()) {
            $this->settings = $integration->mergeConfigToFeatureSettings();
        }
    }

    /**
     * @return array
     */
    public function getFieldsToSkip()
    {
        return isset($this->settings['skipFields']) ? $this->settings['skipFields'] : [];
    }

    /**
     * @return array
     */
    public function getCustomUniqueFields()
    {
        return isset($this->settings['uniqueFields']) ? $this->settings['uniqueFields'] : [];
    }

    /**
     * @param $fields
     *
     * @return bool
     */
    public function hasNotEmptyFieldsToSkip($fields)
    {
        foreach ($fields as $alias=>$value) {
            if (!empty($this->settings['skipFields'][$alias])) {
                return true;
            }
        }

        return false;
    }
}
