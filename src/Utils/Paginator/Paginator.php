<?php

namespace App\Utils\Paginator;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Custom implementation for the paginator.
 *
 * @author Fondative <devteam@fondative.com>
 */
class Paginator
{
    /**
     * @var PaginatorInterface
     */
    private $knpPaginator;

    /**
     * Paginator constructor.
     * @param PaginatorInterface $knpPaginator
     */
    public function __construct(PaginatorInterface $knpPaginator)
    {
        $this->knpPaginator = $knpPaginator;
    }


    /**
     * @param $target
     * @param PaginationFilterInterface $filter
     * @param array $options
     * @return Pagination
     */
    public function paginate($target, PaginationFilterInterface $filter, array $options = array())
    {
        // TODO check parameters

        if ($target instanceof PersistentCollection || $target instanceof ArrayCollection) {
            $target = $target->toArray();
        }

        $knpPagination = $this->knpPaginator->paginate($target, $filter->getPage(), $filter->getLimit(), $options);

        $pagination = (new Pagination())
            ->setPage($filter->getPage())
            ->setLimit($filter->getLimit())
            ->setTotalCount($knpPagination->getTotalItemCount())
            ->setTotalPages($knpPagination->getPageCount())
            ->setItemsLabel($filter->getItemsLabel())
            ->setItems($knpPagination->getItems());

        return $pagination;
    }
}