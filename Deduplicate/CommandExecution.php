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
    private string $rootDir;

    public function __construct(string $rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @return string
     */
    public function getDefaultConsolePath()
    {
        return $this->rootDir.'/../bin/console';
    }

    /**
     * Execute command line in background.
     */
    public function execute()
    {
        $cmd = 'php '.$this->getDefaultConsolePath().' mautic:contacts:deduplicate:custom --notify --env='.MAUTIC_ENV;
        if ('Windows' == substr(php_uname(), 0, 7)) {
            pclose(popen('start /B '.$cmd, 'r'));
        } else {
            exec($cmd.' > /dev/null &');
        }
    }
}
