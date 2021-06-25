<?php

declare(strict_types=1);

namespace Remind\RmndSearchFilter\FieldStrategy;

use Remind\RmndSearchFilter\FieldStrategy\FieldStrategy;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Description of AbstractFieldStrategy
 */
abstract class AbstractFieldStrategy implements FieldStrategy
{
    /**
     * The filter value to be processed.
     *
     * @var mixed
     */
    protected $filterValue = null;

    /**
     * The object manager instance.
     *
     * @var ObjectManager
     */
    protected ?ObjectManager $objectManager = null;

    /**
     * The query instance on which the filters will be applied.
     *
     * @var QueryInterface
     */
    protected ?QueryInterface $query = null;

    /**
     * Creates a new instance for a strategy with default values.
     */
    public function __construct()
    {
        $this->objectManager = null;
        $this->query = null;
        $this->filterValue = null;
    }

    /**
     * Implementing classes must define this function.
     *
     * @return ConstraintInterface|null
     */
    abstract public function process(): ?ConstraintInterface;

    /**
     * Set the filter value that will be processed.
     *
     * @param mixed $filterValue
     * @return void
     */
    public function setFilterValue($filterValue): void
    {
        $this->filterValue = $filterValue;
    }

    /**
     * Set the object manager instance.
     *
     * @param ObjectManager $objectManager
     * @return void
     */
    public function setObjectManager(ObjectManager $objectManager): void
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Set the query for which the constraints are generated and applied.
     *
     * @param QueryInterface $query
     * @return void
     */
    public function setQuery(QueryInterface $query): void
    {
        $this->query = $query;
    }
}
