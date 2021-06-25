<?php

declare(strict_types=1);

namespace Remind\RmndSearchFilter\ModelStrategy;

use Remind\RmndSearchFilter\SearchFilter\ModelStrategy\ModelStrategy;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Description of AbstractModelStrategy
 */
abstract class AbstractModelStrategy implements ModelStrategy
{
    /**
     *
     * @var ObjectManager
     */
    protected ?ObjectManager $objectManager = null;

    /**
     *
     * @var QueryInterface
     */
    protected ?QueryInterface $query = null;

    /**
     *
     * @var mixed
     */
    protected $filterValue = null;

    /**
     *
     * @var string
     */
    protected string $fieldName = '';

    /**
     *
     */
    public function __construct()
    {
        $this->objectManager = null;
        $this->query = null;
        $this->filterValue = null;
        $this->fieldName = '';
    }

    /**
     *
     * @return mixed
     */
    public function getFilterValue()
    {
        return $this->filterValue;
    }

    /**
     *
     * @param mixed $filterValue
     * @return void
     */
    public function setFilterValue($filterValue): void
    {
        $this->filterValue = $filterValue;
    }

    /**
     *
     * @param ObjectManager $objectManager
     * @return void
     */
    public function setObjectManager(ObjectManager $objectManager): void
    {
        $this->objectManager = $objectManager;
    }

    /**
     *
     * @param QueryInterface $query
     * @return void
     */
    public function setQuery(QueryInterface $query): void
    {
        $this->query = $query;
    }

    /**
     *
     * @param string $fieldName
     * @return void
     */
    public function setFieldName(string $fieldName): void
    {
        $this->fieldName = $fieldName;
    }
}
