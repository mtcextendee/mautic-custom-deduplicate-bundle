<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic
 *
 * @link        http://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticCustomDeduplicateBundle\Controller;

use Mautic\CoreBundle\Controller\CommonController;

class CustomDeduplicateController extends CommonController
{

    public function runAction()
    {

        $flashes         = [];
        $routeContext = 'contact';
        $contentTemplate = 'MauticLeadBundle:Lead:index';
        $activeLink      = '#mautic_contact_index';
        $mauticContent   = 'lead';
        $returnUrl       = $this->generateUrl(
            'mautic_'.$routeContext.'_index'
        );
        $this->get('mautic.plugin.custom.duplicate.command.execute')->execute();
            $flashes[] = [
                'type'    => 'notice',
                'msg'     => $this->translator->trans('plugin.custom.deduplication.command'),
            ];

        $postActionVars = [
            'returnUrl'       => $returnUrl,
            'viewParameters'  => [
            ],
            'contentTemplate' => $contentTemplate,
            'passthroughVars' => [
                'activeLink'    => $activeLink,
                'mauticContent' => $mauticContent,
            ],
        ];

        return $this->postActionRedirect(
            array_merge(
                $postActionVars,
                [
                    'flashes' => $flashes,
                ]
            )
        );
    }
}
