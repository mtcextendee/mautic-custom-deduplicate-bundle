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
use Mautic\LeadBundle\Entity\LeadRepository;
use Mautic\PointBundle\Model\TriggerModel;

class CustomDuplications
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var TriggerModel
     */
    private $triggerModel;

    /**
     * @var LeadRepository
     */
    private $leadRepository;

    /**
     * @var Fields
     */
    private $fields;

    /**
     * CustomDuplications constructor.
     *
     * @param EntityManager  $entityManager
     * @param TriggerModel   $triggerModel
     * @param LeadRepository $leadRepository
     * @param Fields         $fields
     */
    public function __construct(
        EntityManager $entityManager,
        TriggerModel $triggerModel,
        LeadRepository $leadRepository,
        Fields $fields
    ) {

        $this->entityManager  = $entityManager;
        $this->triggerModel   = $triggerModel;
        $this->leadRepository = $leadRepository;
        $this->fields = $fields;
    }

    /**
     * @param $fields
     *
     * @return array
     */
    public function customCheckForDuplicateContacts($fields)
    {
        // skip these contacts
        if (!$this->fields->hasNotEmptyFieldsToSkip($fields)) {
            $uniqueData = $this->getCustomUniqueData($fields);
            if (!empty($uniqueData)) {
                return $this->getContactsByUniqueFields($uniqueData);
            }
        }

    }

    /**
     * @param      $uniqueFieldsWithData
     * @param null $leadId
     * @param null $limit
     *
     * @return array
     */
    private function getContactsByUniqueFields($uniqueFieldsWithData, $leadId = null, $limit = null)
    {
        $q = $this->entityManager->getConnection()->createQueryBuilder()
            ->select('l.*')
            ->from(MAUTIC_TABLE_PREFIX.'leads', 'l');
        // loop through the fields and
        foreach ($uniqueFieldsWithData as $col => $val) {
            $q->andWhere(
                $q->expr()->andX(
                    $q->expr()->eq('l.'.$col, ':'.$col),
                    $q->expr()->neq('l.'.$col, $q->expr()->literal('')),
                    $q->expr()->isNotNull('l.'.$col)
                )
            )->setParameter($col, $val);
        }

        // if we have a lead ID lets use it
        if (!empty($leadId)) {
            // make sure that its not the id we already have
            $q->andWhere('l.id != :leadId')
                ->setParameter('leadId', $leadId);
        }

        foreach ($this->fields->getFieldsToSkip() as $col) {
            $q->andWhere(
                $q->expr()->orX(
                    $q->expr()->eq('l.'.$col, $q->expr()->literal('')),
                    $q->expr()->isNull('l.'.$col)
                )
            );
        }

        if ($limit) {
            $q->setMaxResults($limit);
        }
        $results = $q->execute()->fetchAll();
        // Collect the IDs
        $leads = [];
        foreach ($results as $r) {
            $leads[$r['id']] = $r;
        }
        // Get entities
        $q = $this->entityManager->createQueryBuilder()
            ->select('l')
            ->from('MauticLeadBundle:Lead', 'l');

        $q->where(
            $q->expr()->in('l.id', ':ids')
        )
            ->setParameter('ids', array_keys($leads))
            ->orderBy('l.dateAdded', 'DESC')
            ->addOrderBy('l.id', 'DESC');
        $entities = $q->getQuery()
            ->getResult();

        return $entities;
    }

    /**
     * @param array $queryFields
     *
     * @return array
     */
    private function getCustomUniqueData(array $queryFields)
    {
        $uniqueLeadFields = $this->fields->getCustomUniqueFields();
        $uniqueLeadFieldData = [];
        foreach ($queryFields as $k => $v) {
            if (in_array($k, $uniqueLeadFields)) {
                $uniqueLeadFieldData[$k] = $v;
            }
        }
        return $uniqueLeadFieldData;
    }
}
