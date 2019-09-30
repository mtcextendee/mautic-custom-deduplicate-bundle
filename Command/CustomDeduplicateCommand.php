<?php

/*
 * @copyright   2019 Mautic Contributors. All rights reserved
 * @author      Mautic, Inc.
 *
 * @link        https://mautic.org
 *
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace MauticPlugin\MauticCustomDeduplicateBundle\Command;

use Mautic\CoreBundle\Model\NotificationModel;
use Mautic\LeadBundle\Command\DeduplicateCommand;
use Mautic\LeadBundle\Deduplicate\ContactDeduper;
use Mautic\PluginBundle\Helper\IntegrationHelper;
use Mautic\UserBundle\Model\UserModel;
use Monolog\Logger;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\TranslatorInterface;

class CustomDeduplicateCommand extends DeduplicateCommand
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var NotificationModel
     */
    private $notificationModel;

    /**
     * @var ContactDeduper
     */
    private $contactDeduper;

    /**
     * @var UserModel
     */
    private $userModel;

    /**
     * @var IntegrationHelper
     */
    private $integrationHelper;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * DeduplicateCommand constructor.
     *
     * @param ContactDeduper      $contactDeduper
     * @param TranslatorInterface $translator
     * @param NotificationModel   $notificationModel
     * @param UserModel           $userModel
     * @param IntegrationHelper   $integrationHelper
     * @param Logger              $logger
     */
    public function __construct(ContactDeduper $contactDeduper, TranslatorInterface $translator, NotificationModel $notificationModel, UserModel $userModel, IntegrationHelper $integrationHelper, Logger $logger)
    {
        parent::__construct($contactDeduper, $translator);

        $this->notificationModel = $notificationModel;
        $this->translator = $translator;
        $this->contactDeduper = $contactDeduper;
        $this->userModel = $userModel;
        $this->integrationHelper = $integrationHelper;
        $this->logger = $logger;
    }

    public function configure()
    {
        parent::configure();

        $this->setName('mautic:contacts:deduplicate:custom')
            ->setDescription('Custom merge contacts based on same unique identifiers')
            ->addOption(
                '--newer-into-older',
                null,
                InputOption::VALUE_NONE,
                'By default, this command will merge older contacts and activity into the newer. Use this flag to reverse that behavior.'
            )
            ->addOption(
                '--notify',
                null,
                InputOption::VALUE_NONE,
                'Notify progress in notification area.'
            )
            ->setHelp(
                <<<'EOT'
The <info>%command.name%</info> command will dedpulicate contacts based on unique identifier values. 

<info>php %command.full_name%</info>
EOT
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $integration = $this->integrationHelper->getIntegrationObject('CustomDeduplicate');
        if (!$integration || !$integration->getIntegrationSettings()->getIsPublished()) {
            $message = $this->translator->trans('plugin.custom.deduplication.disabled');
            $output->writeln(
                $message
            );
            $this->sendDisabledNotification();
            $this->logger->debug($message);
            return 0;
        }

        $notify = (bool) $input->getOption('notify');
        $key = __CLASS__;
        if (!$this->checkRunStatus($input, $output, $key)) {
            if ($notify) {
                $this->sendProgressNotification();
            }
            return 0;
        }


        if ($notify) {
            $this->sendStartNotification();
        }

        define('MAUTIC_CUSTOM_DEDUPLICATE_COMMAND', 1);
        $newerIntoOlder = (bool) $input->getOption('newer-into-older');
        $count          = $this->contactDeduper->deduplicate($newerIntoOlder, $output);

        $output->writeln('');
        $output->writeln(
            $this->translator->trans(
                'plugin.custom.deduplication.notification.result.count',
                [
                    '%count%' => $count,
                ]
            )
        );

        if ($notify) {
            $this->sendEndNotification($count);
        }

    }

    private function sendStartNotification()
    {
        $this->notificationModel->addNotification(
            '',
            'info',
            false,
            $this->translator->trans('plugin.custom.deduplication.notification.start.header'),
            'fa-clone',
            null,
            $this->userModel->getSystemAdministrator()
        );
    }

    private function sendProgressNotification()
    {
        $this->notificationModel->addNotification(
            '',
            'info',
            false,
            $this->translator->trans(
                'plugin.custom.deduplication.notification.progress.header'
            ),
            'fa-clone',
            null,
            $this->userModel->getSystemAdministrator()
        );
    }

    /**
     * @param $count
     */
    private function sendEndNotification($count)
    {
        $this->notificationModel->addNotification(
            $this->translator->trans('plugin.custom.deduplication.notification.result.count',[
                '%count%' => $count,
            ]),

            'info',
            false,
            $this->translator->trans(
                'plugin.custom.deduplication.notification.result.header'
            ),
            'fa-clone',
            null,
            $this->userModel->getSystemAdministrator()
        );
    }

    private function sendDisabledNotification()
    {
        $this->notificationModel->addNotification(
            '',
            'info',
            false,
            $this->translator->trans(
                'plugin.custom.deduplication.disabled'
            ),
            'fa-clone',
            null,
            $this->userModel->getSystemAdministrator()
        );
    }
}
