<?php

namespace App\Search;

abstract class SearchModel
{
    public const OP_AND = 'and';
    public const OP_OR = 'or';

    private string|int $page = 1;

    private string|int $limit = 50;

    /** @var array<string, array<'eq'|'neq'|'like'|'notLike'|'in'|'lt'|'lte'|'get'|'gte', int|float|string|array<int|float|string>>> */
    private array $filter = [];

    /** @var array<string, 'asc'|'desc'> */
    private array $sortBy = [];

    private string $op = self::OP_AND;

    /**
     * @return int|string
     */
    public function getPage(): int|string
    {
        return (int) $this->page;
    }

    /**
     * @param int|string $page
     */
    public function setPage(int|string $page): void
    {
        $this->page = $page;
    }

    /**
     * @return int|string
     */
    public function getLimit(): int|string
    {
        return (int) $this->limit;
    }

    /**
     * @param int|string $limit
     */
    public function setLimit(int|string $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * phpcs:ignore
     * @return array<string, array<'eq'|'neq'|'like'|'notLike'|'in'|'lt'|'lte'|'get'|'gte', int|float|string|array<int|float|string>>>
     */
    public function getFilter(): array
    {
        return $this->filter;
    }

    /**
     * phpcs:ignore
     * @param array<string, array<'eq'|'neq'|'like'|'notLike'|'in'|'lt'|'lte'|'get'|'gte', int|float|string|array<int|float|string>>> $filter
     */
    public function setFilter(array $filter): void
    {
        $this->filter = $filter;
    }

    /**
     * @return array<string, 'asc'|'desc'>
     */
    public function getSortBy(): array
    {
        return $this->sortBy;
    }

    /**
     * @param array<string, 'asc'|'desc'> $sortBy
     */
    public function setSortBy(array $sortBy): void
    {
        $this->sortBy = $sortBy;
    }

    public function getOp(): string
    {
        return in_array($this->op, [self::OP_AND, self::OP_OR]) ? $this->op : self::OP_AND;
    }

    public function setOp(string $op): void
    {
        $this->op = $op;
    }
}
