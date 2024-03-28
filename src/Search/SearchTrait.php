<?php

namespace App\Search;

use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\Expr\Select;
use Doctrine\ORM\QueryBuilder;

/**
 * @template T of object
 */
trait SearchTrait
{
    /** @var int */
    private int $incrementedAssociation;

    /** @var int */
    private int $incrementedName;

    /**
     * * Splits the given property into parts.
     *
     * Returns an array with the following keys:
     *   - associations: array of associations according to nesting order
     *   - field: string holding the actual field (leaf node)
     *
     * @param class-string<T> $entityClass
     * @param string $property
     * @return array{associations: array<string>, field: string}
     * @throws MappingException
     */
    private function splitPropertyParts(string $entityClass, string $property): array
    {
        $parts = explode('.', $property);

        $metadata = $this->entityManager->getClassMetadata($entityClass);
        $slice    = 0;

        foreach ($parts as $part) {
            if ($metadata->hasAssociation($part)) {
                $metadata = $this->entityManager->getClassMetadata($metadata->getAssociationTargetClass($part));
                ++$slice;
            }
        }

        if (count($parts) === $slice && ! $metadata->isIdentifierComposite) {
            $parts[] = $metadata->getSingleIdentifierFieldName();
        } elseif (count($parts) === $slice) {
            --$slice;
        }

        return [
            'associations' => array_slice($parts, 0, $slice),
            'field'        => implode('.', array_slice($parts, $slice)),
        ];
    }

    /**
     * Adds the necessary joins for a property.
     *
     * @param QueryBuilder $queryBuilder
     * @param class-string<T> $entityClass
     * @param string $property
     * @param bool $joinOnce
     * @return array{0: string, 1: string} An array where the first element is the join $alias of the leaf entity,
     *               the second element is the $field name
     * @throws MappingException
     */
    private function addJoinsForProperty(
        QueryBuilder $queryBuilder,
        string $entityClass,
        string $property,
        bool $joinOnce = true
    ): array {
        $propertyParts = $this->splitPropertyParts($entityClass, $property);
        $parentAlias   = $queryBuilder->getRootAliases()[0];

        foreach ($propertyParts['associations'] as $association) {
            $alias = $this->addJoin($queryBuilder, $parentAlias, $association, $joinOnce);
            $this->addSelect($queryBuilder, $alias);
            $parentAlias = $alias;
        }

        return [
            $parentAlias,
            $propertyParts['field'],
        ];
    }

    /**
     * Add unique select alias
     *
     * @param QueryBuilder $queryBuilder
     * @param string $alias
     */
    public function addSelect(QueryBuilder $queryBuilder, string $alias): void
    {
        /** @var Select[] $selects */
        $selects = $queryBuilder->getDQLPart('select');

        foreach ($selects as $select) {
            if (in_array($alias, $select->getParts(), true)) {
                return;
            }
        }
        $queryBuilder->addSelect($alias);
    }

    /**
     * Adds a join to the QueryBuilder if none exists.
     *
     * @param QueryBuilder $queryBuilder
     * @param string $alias
     * @param string $association
     * @param bool $joinOnce
     * @return string
     */
    public function addJoin(
        QueryBuilder $queryBuilder,
        string $alias,
        string $association,
        bool $joinOnce = true
    ): string {
        $join = $this->getExistingJoin($queryBuilder, $alias, $association);

        if ($join?->getAlias() && $joinOnce) {
            /** @var string $alias */
            $alias = $join->getAlias();
            return $alias;
        }

        $associationAlias = $this->generateJoinAlias($association);
        $query            = "$alias.$association";

        $queryBuilder->leftJoin($query, $associationAlias);

        return $associationAlias;
    }

    /**
     * Gets the existing join from QueryBuilder DQL parts.
     *
     * @param QueryBuilder $queryBuilder
     * @param string $alias
     * @param string $association
     * @return Join|null
     */
    private function getExistingJoin(QueryBuilder $queryBuilder, string $alias, string $association): ?Join
    {
        /** @var array<string, array<Join>> $parts */
        $parts     = $queryBuilder->getDQLPart('join');
        $rootAlias = $queryBuilder->getRootAliases()[0];

        if (! isset($parts[$rootAlias])) {
            return null;
        }

        foreach ($parts[$rootAlias] as $join) {
            if (sprintf('%s.%s', $alias, $association) === $join->getJoin()) {
                return $join;
            }
        }

        return null;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @return string
     * @throws MappingException
     */
    public static function getRootIdentifier(QueryBuilder $queryBuilder)
    {
        /** @var class-string<T> $rootEntity */
        $rootEntity = current($queryBuilder->getRootEntities());
        $metadata   = $queryBuilder->getEntityManager()->getClassMetadata($rootEntity);
        return current($queryBuilder->getRootAliases()) . '.' . $metadata->getSingleIdentifierFieldName();
    }

    /**
     * Generates a cacheable alias for DQL join.
     *
     * @param string $association
     * @return string
     */
    public function generateJoinAlias(string $association): string
    {
        return sprintf('%s_a%d', $association, $this->incrementedAssociation++);
    }

    /**
     * @param string $name
     * @return string
     */
    public function generateParameterName(string $name): string
    {
        return sprintf('%s_p%d', str_replace('.', '_', $name), $this->incrementedName++);
    }
}
