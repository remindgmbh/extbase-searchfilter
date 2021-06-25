<?php

declare(strict_types=1);

namespace Remind\RmndSearchFilter;

use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Interface that describes a strategy for filtering search requests.
 */
interface SearchFilterStrategy
{
    /**
     * Parses the given filter and returns a db query result.
     *
     * @return QueryInterface
     */
    public function parse(): QueryInterface;

    /**
     * Sets the object manager instance.
     *
     * @param ObjectManager $objectManager
     * @return void
     */
    public function setObjectManager(ObjectManager $objectManager): void;

    /**
     * Returns the filter value.
     *
     * @return array The filter value
     */
    public function getFilter(): array;

    /**
     * Sets the filter data that should be processed.
     *
     * @param array $filter
     * @return void
     */
    public function setFilter(array $filter): void;
}
