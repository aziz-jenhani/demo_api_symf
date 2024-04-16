<?php

namespace App\Utils\Paginator;

/**
 * Interface for entities pagination.
 *
 * @author Fondative <devteam@fondative.com>
 */
interface PaginationFilterInterface
{
    const LIMIT_DEFAULT = 10;
    const LIMIT_MAX = 100;

    /**
     * @return int
     */
    public function getPage();

    /**
     * @return int
     */
    public function getLimit();

    /**
     * Label used for the list of items.
     *
     * @return string
     */
    public function getItemsLabel();
}