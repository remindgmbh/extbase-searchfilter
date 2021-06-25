<?php

declare(strict_types=1);

namespace Remind\RmndSearchFilter;

use Remind\RmndSearchFilter\FieldStrategy\FieldStrategy;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Abstract implementation of a search filter strategy that sets most of the
 * default procedures for the interface methods.
 */
abstract class AbstractSearchFilterStrategy implements SearchFilterStrategy
{
    /**
     * Defines the version of filter data this strategy can parse.
     * @var string
     */
    public const FILTER_VERSION = '1.0.0';

    /**
     * The filter data that will be processed.
     *
     * @var array
     */
    protected array $filter = [];

    /**
     * The namespace for the search field strategy.
     *
     * @var string
     */
    protected string $searchFieldNamespace = '';

    /**
     * Specify a fallback namespace just in case the filter is not found
     * in the main namespace.
     *
     * @var string
     */
    protected string $fallbackNamespace = '';

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
     * Key of the currently processed filtergroup entry.
     *
     * @var string
     */
    protected string $currentKey = '';

    /**
     * Value of the currently processed filtergroup entry.
     *
     * @var mixed
     */
    protected $currentValue = [];

    /**
     * Collection of constraints that were created by the given filter.
     *
     * @var array
     */
    protected array $constraints = [];

    /**
     * Creates a new instance for a strategy with default values.
     */
    public function __construct()
    {
        $this->filter = [];
        $this->searchFieldNamespace = '';
        $this->fallbackNamespace = '';
        $this->objectManager = null;
        $this->query = null;
        $this->currentKey = '';
        $this->currentValue = [];
        $this->constraints = [];
    }

    /**
     * Set the query used for this search filter strategy.
     */
    abstract public function setQuery(): void;

    /**
     * This methods implementation should apply group based processing,
     * or if no special group handling is necessary, it should call
     * processFieldGroup() and return the result.
     * @return void
     */
    abstract protected function applyGroupRules(): void;

    /**
     * Processes a group of filtering data and applies the resulting constraints
     * to the query instance of this class.
     *
     * @return void
     */
    protected function parseFilterGroup(): void
    {
        /* Check if some special rules apply for this group */
        $constraint = $this->applyGroupRules();

        /* If a constraint was created */
        if ($constraint !== null) {
            /* Add the constraint to the list of constraints */
            $this->constraints[] = $constraint;
        }
    }

    /**
     *
     * @return void
     */
    protected function processFieldGroup(): void
    {
        $constraints = [];

        /* Iterate the filter group entries by name and value */
        foreach ($this->currentValue as $name => $value) {

            /* If the filter has no value whatsoever */
            if (empty($value)) {
                continue; // Just ignore the filter as if it weren't set
            }

            /* Create a classname for the current field stategy */
            $fqcn = $this->searchFieldNamespace . ucfirst($name) . 'FieldStrategy';

            /* The class was not found or is not loaded */
            if (!class_exists($fqcn)) {
                $fqcn = $this->fallbackNamespace . ucfirst($name) . 'FieldStrategy';

                if (!class_exists($fqcn)) {
                    // @todo FIELD DOES NOT COMPUTE
                    continue;
                }
            }

            /* @var $strategy FieldStrategy */
            $strategy = new $fqcn();

            /* Apply filter and other data to strategy */
            $strategy->setFilterValue($value);
            $strategy->setObjectManager($this->objectManager);
            $strategy->setQuery($this->query);

            /* Process the filter field */
            $constraint = $strategy->process();

            /* If a constraint was created */
            if ($constraint !== null) {
                /* Add the constraint to the list of constraints */
                $constraints[] = $constraint;
            }
        }

        /* If not a single constraint was made */
        if (!empty($constraints)) {
            $this->constraints[] = $this->query->logicalAnd($constraints);
        }
    }

    /**
     * Applies constraints to this strategys query instance.
     *
     * @param array $constraints An array of query constraints
     * @return QueryInterface|null
     */
    protected function applyConstraints(): ?QueryInterface
    {
        /* Nothing to concat for query */
        if (empty($this->constraints)) {
            return $this->query;
        }

        /* Apply constraint to query */
        $this->query->matching(
            /* All resulting constraints must be met */
            $this->query->logicalAnd($this->constraints)
        );

        return null;
    }

    /**
     *
     * @param array $filter
     * @return array
     */
    protected function parseFilter(array $filter): array
    {
        return $filter;
    }

    /**
     *
     * @return QueryInterface
     */
    public function parse(): QueryInterface
    {
        /* Set the query object used by this strategy */
        $this->setQuery();

        $filter = $this->parseFilter($this->filter);

        if (empty($filter)) {
            return $this->query;
        }

        /* Iterate the given filter by each group */
        foreach ($filter as $this->currentKey => $this->currentValue) {
            /* Process the current filter group */
            $this->parseFilterGroup();
        }

        $this->applyConstraints();

        /* Execute the query and return the result */
        return $this->query;
    }

    /**
     * Returns the object manager instance used by this stratgey.
     *
     * @return ObjectManager
     */
    public function getObjectManager(): ObjectManager
    {
        return $this->objectManager;
    }

    /**
     * Sets the object manager instance.
     *
     * @param ObjectManager $objectManager
     * @return void
     */
    public function setObjectManager(ObjectManager $objectManager): void
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Sets the filter data that should be processed.
     *
     * @return array The filter value.
     */
    public function getFilter(): array
    {
        return $this->filter;
    }

    /**
     * Sets the filter data that should be processed.
     *
     * @param array $filter
     * @return void
     */
    public function setFilter(array $filter): void
    {
        $this->filter = $filter;
    }
}
