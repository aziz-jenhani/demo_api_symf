<?php

namespace App\Utils\Paginator;

/**
 * The pagination model.
 *
 * @author Fondative <devteam@fondative.com>
 */
class Pagination
{
    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $totalCount;

    /**
     * @var int
     */
    private $totalPages;

    /**
     * @var string
     */
    private $itemsLabel;

    /**
     * @var array
     */
    private $items;

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     * @return Pagination
     */
    public function setPage(int $page): Pagination
    {
        $this->page = $page;
        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @param int $limit
     * @return Pagination
     */
    public function setLimit(int $limit): Pagination
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @param int $totalCount
     * @return Pagination
     */
    public function setTotalCount(int $totalCount): Pagination
    {
        $this->totalCount = $totalCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * @param int $totalPages
     * @return Pagination
     */
    public function setTotalPages(int $totalPages): Pagination
    {
        $this->totalPages = $totalPages;
        return $this;
    }

    /**
     * @return string
     */
    public function getItemsLabel(): string
    {
        return $this->itemsLabel;
    }

    /**
     * @param string $itemsLabel
     * @return Pagination
     */
    public function setItemsLabel(string $itemsLabel): Pagination
    {
        $this->itemsLabel = $itemsLabel;
        return $this;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array $items
     * @return Pagination
     */
    public function setItems(array $items): Pagination
    {
        $this->items = $items;
        return $this;
    }

    public function getData()
    {
        return [
            'page' => $this->page,
            'limit' => $this->limit,
            'totalCount' => $this->totalCount,
            'totalPages' => $this->totalPages,
            $this->itemsLabel => $this->items
        ];
    }
}