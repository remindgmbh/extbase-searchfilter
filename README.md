# REMIND - Searchfilter Library

An effort was made to provide a generalized approach to providing search filters.

![remind-badge](https://img.shields.io/badge/author-REMIND-black.svg?style=flat-square)
![library](https://img.shields.io/badge/type-library-lightgray.svg?style=flat-square)
![typo3-badge](https://img.shields.io/badge/TYPO3-10.4-green.svg?style=flat-square)
![php-badge](https://img.shields.io/badge/PHP-7.4-green.svg?style=flat-square)
![license-badge](https://img.shields.io/badge/license-GPL--3.0-blue.svg?style=flat-square)
![version-badge](https://img.shields.io/badge/version-2.0.1-lightgrey.svg?style=flat-square)

## Build Status

[![build-master](https://jenkins.remind.de/buildStatus/icon?job=rmnd_basemodules%2Fmaster&subject=master)](https://jenkins.remind.de/job/rmnd_basemodules/)
[![build-develop](https://jenkins.remind.de/buildStatus/icon?job=rmnd_basemodules%2Fdevelop&subject=develop)](https://jenkins.remind.de/job/rmnd_basemodules/)

---

## How to use

Start with a n-dimensional array filter representation.

```php
<?php
/* filter.php */

use Remind\SearchImpl\SearchFilter\MyFilterSearchFilterStrategy;

/*
 * The filter is a simple n-dimensional array.
 * The first dimension contains the filter group names.
 * The second dimension contains the field names with their respective values.
 */
$filter = [
    /* The filter group name that is mapped to the strategy class name */
    'myFilter' => [
        /* A record type with a given value */
        'someRecord' => 123
    ],
    'someOtherFilter' => [
        'anotherThing' => [ 'foo', 'bar', 'baz' ]
    ]
];

$strategy = new MyFilterSearchFilterStrategy();
$strategy->setObjectManager($objectManager);
$strategy->setFilter($filter);
$strategy->setQuery();

/*
 * This will return a TYPO3\CMS\Extbase\Persistence\QueryInterface instance
 * that can be used like any other TYPO3 query object or NULL when an error
 * occured.
 */
$query = $strategy->parse();

$result = $query === null ? null : $query->execute();

```

Create implementing class.

```php
<?php
/* MyFilterSearchFilterStrategy.php */

declare(strict_types=1);

namespace Remind\SearchImpl\SearchFilter;

use Remind\RmndSearchFilter\AbstractSearchFilterStrategy;
use Remind\SearchImpl\Domain\Repository\SomeRepository;

/**
 * Implementation of a search filter strategy for contact searches.
 */
class MyFilterSearchFilterStrategy extends AbstractSearchFilterStrategy
{
    /**
     * Namespace used for building the full field strategy class namespaces.
     * @var string
     */
    public const STRATEGY_NS = 'Remind\SearchImpl\SearchFilter\FieldStrategy\\';

    /**
     *
     */
    public function __construct()
    {
        parent::__construct();

        /* Use this namespace to look for field strategy classes */
        $this->searchFieldNamespace = self::STRATEGY_NS;
    }

    /**
     * This methods implementation should apply group based processing,
     * or if no special group handling is necessary, it should call
     * processFieldGroup() and return the result.
     *
     * @return void
     */
    protected function applyGroupRules(): void
    {
        $constraint = $this->processFieldGroup();

        if ($constraint !== null) {
            $this->constraints[] = $constraint;
        }
    }

    /**
     * Implement this method and create a query instance that will be used
     * by this instance.
     *
     * #TODO The methods visibility is likely an artifact from an earlier
     * implementation and is bound to change in a later version.
     *
     * @return void
     */
    public function setQuery(): void
    {
        /* @var $repository SomeRepository */
        $repository = $this->objectManager->get(SomeRepository::class);

        $this->query = $repository->createQuery();
    }
}

```

Create class to process filter values.

```php
<?php
/* SomeRecordFieldStrategy.php */

declare(strict_types=1);

namespace Remind\SearchImpl\SearchFilter\FieldStrategy\MyFilter;

use Remind\RmndSearchFilter\FieldStrategy\AbstractFieldStrategy;
use Remind\SearchImpl\Domain\Model\SomeOtherModel;
use Remind\SearchImpl\Domain\Repository\SomeOtherRepository;
use TYPO3\CMS\Extbase\Persistence\Generic\Qom\ConstraintInterface;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * This class will handle the 'someRecord' filter part.
 *
 * $filter = [
 *     'myFilter' => [
 *         'someRecord' => 123
 *      ]
 * ]
 *
 * It will be used to get the records for the SomeRepository, from which the
 * query originates, by using the uid of another Record from SomeOtherRepository
 * that has some relation to the records of SomeRepository.
 *
 */
class SomeRecordFieldStrategy extends AbstractFieldStrategy
{
    /**
     * The $this->filterValue should be set to the value from the filter
     * object, which is '123' in this case.
     *
     * @return ConstraintInterface|null
     */
    public function process(): ?ConstraintInterface
    {
        /* If no numeric value was given */
        if (!is_numeric($this->filterValue)) {
            return null; // do not process any further
        }

        /* @var $repository SomeOtherRepository */
        $repository = $this->objectManager->get(SomeOtherRepository::class);

        /* Get all records matching the '123' filter value in whatever field */
        $records = $repository->findBySomeField($this->filterValue);

        /* Abort processing */
        if ($records->count() === 0) {
            return null;
        }

        $constraints = [];

        foreach ($records as $record) {
            /* @var $record SomeOtherModel /*

            /* @var $objects ObjectStorage */
            $objects = $record->getRelatedObjects();

            /* Abort processing */
            if ($objects->count() === 0) {
                return null;
            }

            $uids = [];

            /* Get uid values from objects */
            foreach ($objects as $object) {
                $uids[] = $object->getUid();
            }

            /*
             * Match query uid from SomeRepository with values returned
             * from SomeOtherRepository
             */
            return $this->query->in('uid', $uids);
        }

        /* Default return */
        return null;
    }
}

```
