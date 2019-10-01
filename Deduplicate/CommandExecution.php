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

use Mautic\CoreBundle\Helper\CoreParametersHelper;

class CommandExecution
{
    /**
     * @var CoreParametersHelper
     */
    private $coreParametersHelper;

    public function __construct(CoreParametersHelper $coreParametersHelper)
    {
        $this->coreParametersHelper = $coreParametersHelper;
    }

    /**
     * @return string
     */
    public function getDefaultConsolePath()
    {
        return $this->coreParametersHelper->getParameter('kernel.root_dir').'/console';
    }

    /**
     * Execute command line in background
     */
    public function execute()
    {
        $cmd = 'php '.$this->getDefaultConsolePath().' mautic:contacts:deduplicate:custom --notify --env='.MAUTIC_ENV;

        if (substr(php_uname(), 0, 7) == "Windows") {
            pclose(popen("start /B ".$cmd, "r"));
        } else {
            exec($cmd." > /dev/null &");
        }
    }
}
