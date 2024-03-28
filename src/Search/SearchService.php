<?php

namespace App\Search;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @template T of object
 */
class SearchService
{
    /** @phpstan-use SearchTrait<T> */
    use SearchTrait;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @param class-string<T> $entityClass
     * @param SearchModel $searchDto
     * @return QueryBuilder
     * @throws MappingException
     */
    public function createQueryBuilder(string $entityClass, SearchModel $searchDto): QueryBuilder
    {
        $this->incrementedAssociation = 1;
        $this->incrementedName        = 1;

        $queryBuilder = $this->entityManager->getRepository($entityClass)->createQueryBuilder('o');

        if ($searchDto->getFilter()) {
            $exprX = $queryBuilder->expr()->{$searchDto->getOp() . 'X'}();
            $queryBuilder->where($exprX);

            foreach ($searchDto->getFilter() as $attr => $filter) {
                [$alias, $field] = $this->addJoinsForProperty($queryBuilder, $entityClass, $attr, false);

                foreach ($filter as $op => $value) {
                    $valueParameter = $this->generateParameterName($field);
                    $exprField    = sprintf('%s.%s', $alias, $field);
                    $exprValParam = sprintf(':%s', $valueParameter);
                    $exprX->add($queryBuilder->expr()->{$op}($exprField, $exprValParam));
                    if (is_scalar($value) && ($op === 'like' || $op === 'notLike')) {
                        $value = sprintf('%2$s%1$s%2$s', $value, '%');
                    } elseif ($op === 'in' && is_string($value)) {
                        $value = explode(',', $value);
                    }
                    $queryBuilder->setParameter($valueParameter, $value);
                }
            }
        }

        if ($searchDto->getSortBy()) {
            foreach ($searchDto->getSortBy() as $attr => $sortDir) {
                [$alias, $field] = $this->addJoinsForProperty($queryBuilder, $entityClass, $attr);
                $queryBuilder->addOrderBy(sprintf('%s.%s', $alias, $field), $sortDir);
            }
        }

        return $queryBuilder;
    }

    /**
     * @param class-string<T> $entityClass
     * @param SearchModel $searchDto
     * @param array<int, string> $associationFields
     * @return Paginator<T>
     * @throws MappingException
     */
    public function getSearchResults(
        string $entityClass,
        SearchModel $searchDto,
        array $associationFields = []
    ): Paginator {
        $queryBuilder = $this->createQueryBuilder($entityClass, $searchDto);
        $searchQueryBuilder = $this->getSearchQueryBuilder($queryBuilder, $entityClass, $associationFields);

        if ($maxResult = $searchDto->getLimit()) {
            $offset = ((int) $searchDto->getPage() - 1) * (int) $searchDto->getLimit();
            $searchQueryBuilder->setMaxResults((int) $maxResult)->setFirstResult($offset);
        }

        /** @var Paginator<T> $paginator */
        $paginator = new Paginator($searchQueryBuilder);

        return $paginator;
    }

    /**
     * @param class-string<T> $entityClass
     * @param SearchModel $searchDto
     * @param array<int, string> $associationFields
     * @return T|null
     * @throws MappingException
     * @throws NonUniqueResultException
     */
    public function getResult(string $entityClass, SearchModel $searchDto, array $associationFields = []): ?object
    {
        $queryBuilder = $this->createQueryBuilder($entityClass, $searchDto);

        /** @var T|null $result */
        $result = $this->getSearchQueryBuilder($queryBuilder, $entityClass, $associationFields)
            ->resetDQLPart('orderBy')
            ->getQuery()
            ->getOneOrNullResult();

        return $result;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param class-string<T> $entityClass
     * @param array<int, string> $associationFields
     * @return QueryBuilder
     * @throws MappingException
     */
    public function getSearchQueryBuilder(
        QueryBuilder $queryBuilder,
        string $entityClass,
        array $associationFields = []
    ): QueryBuilder {
        $searchQueryBuilder = clone $queryBuilder;

        foreach ($associationFields as $assoField) {
            $this->addJoinsForProperty($queryBuilder, $entityClass, $assoField);
        }

        return $searchQueryBuilder;
    }
}
