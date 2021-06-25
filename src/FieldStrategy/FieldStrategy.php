<?php

declare(strict_types=1);

namespace Remind\RmndSearchFilter\FieldStrategy;

use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Describes a strategy that can be used to process a search filter field.
 */
interface FieldStrategy
{
    /**
     * Process the given filter value and return an array of generated
     * query constraints.
     *
     * @return ConstraintInterface|null
     */
    public function process(): ?ConstraintInterface;

    /**
     * Set the filter value that will be processed.
     *
     * @param mixed $filterValue
     * @return void
     */
    public function setFilterValue($filterValue): void;

    /**
     * Set the object manager instance.
     *
     * @param ObjectManager $objectManager
     * @return void
     */
    public function setObjectManager(ObjectManager $objectManager): void;

    /**
     * Set the query for which the constraints are generated and applied.
     *
     * @param QueryInterface $query
     * @return void
     */
    public function setQuery(QueryInterface $query): void;
}
