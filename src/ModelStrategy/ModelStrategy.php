<?php

declare(strict_types=1);

namespace Remind\RmndSearchFilter\ModelStrategy;

use Remind\RmndSearchFilter\FieldStrategy\FieldStrategy;

/**
 * ModelStrategy
 */
interface ModelStrategy extends FieldStrategy
{
    /**
     *
     * @return mixed
     */
    public function getFilterValue();

    /**
     *
     * @param string $fieldName
     * @return void
     */
    public function setFieldName(string $fieldName): void;
}
