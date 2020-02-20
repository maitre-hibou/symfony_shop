<?php

declare(strict_types=1);

namespace App\Validator\Constraint;

use App\Form\Dto\DataTransferObjectInterface;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class DtoUniqueEntityValidator extends ConstraintValidator
{
    private $managerRegistry;

    /**
     * @var DtoUniqueEntity
     */
    private $constraint;

    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var DataTransferObjectInterface
     */
    private $validationObject;

    /**
     * @var ClassMetadata
     */
    private $entityMeta;

    /**
     * @var ObjectRepository
     */
    private $repository;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function validate($object, Constraint $constraint)
    {
        if (!$constraint instanceof DtoUniqueEntity) {
            throw new UnexpectedTypeException($this->constraint, DtoUniqueEntity::class);
        }

        $this->constraint = $constraint;
        $this->validationObject = $object;

        $this->entityMeta = $this->getEntityManager()->getClassMetadata($this->constraint->entityClass);
        $criteria = $this->getCriteria();

        if (empty($criteria)) {
            return;
        }

        $result = $this->checkConstraint($criteria);

        if (!$result || (1 === \count($result) && current($result) === $this->entityMeta)) {
            return;
        }

        $objectFields = array_keys($this->constraint->fieldMapping);
        $errorPath = null !== $this->constraint->errorPath ?
            $this->constraint->errorPath :
            $objectFields[0];

        $invalidValue = isset($criteria[$this->constraint->fieldMapping[$errorPath]]) ?
            $criteria[$this->constraint->fieldMapping[$errorPath]] :
            $criteria[$this->constraint->fieldMapping[0]];

        $this->context->buildViolation($this->constraint->message)
            ->atPath($errorPath)
            ->setParameter('{{ value }}', $this->formatWithIdentifiers($invalidValue))
            ->setInvalidValue($invalidValue)
            ->setCode(DtoUniqueEntity::NOT_UNIQUE_ERROR)
            ->setCause($result)
            ->addViolation();
    }

    private function checkTypes()
    {
        if (!$this->validationObject instanceof DataTransferObjectInterface) {
            throw new UnexpectedTypeException($this->validationObject, DataTransferObjectInterface::class);
        }

        if (null === $this->constraint->entityClass || !class_exists($this->constraint->entityClass)) {
            throw new UnexpectedTypeException($this->constraint->entityClass, Entity::class);
        }

        if (!is_array($this->constraint->fieldMapping) || 0 === count($this->constraint->fieldMapping)) {
            throw new UnexpectedTypeException($this->constraint->fieldMapping, '[objectProperty => entityProperty]');
        }

        if (null !== $this->constraint->errorPath || !is_string($this->constraint->errorPath)) {
            throw new UnexpectedTypeException($this->constraint->errorPath, 'string or null');
        }
    }

    private function getEntityManager(): ObjectManager
    {
        if (null !== $this->em) {
            return $this->em;
        }

        if ($this->constraint->em) {
            $this->em = $this->managerRegistry->getManager($this->constraint->em);

            if (null === $this->em) {
                throw new ConstraintDefinitionException(sprintf('Object manager "%s" does not exist.', $this->constraint->em));
            }
        } else {
            $this->em = $this->managerRegistry->getManagerForClass($this->constraint->entityClass);

            if (null === $this->em) {
                throw new ConstraintDefinitionException(sprintf('Unable to find the object manager associated with entity of class "%s"', $this->constraint->entityClass));
            }
        }

        return $this->em;
    }

    private function getCriteria(): array
    {
        $validationClass = new \ReflectionClass($this->validationObject);

        $criteria = [];
        foreach ($this->constraint->fieldMapping as $objectField => $entityField) {
            if (!$validationClass->hasProperty($objectField)) {
                throw new ConstraintDefinitionException(sprintf('Property for fieldMapping "%s" does not exists on this object.', $objectField));
            }

            if (!property_exists($this->constraint->entityClass, $entityField)) {
                throw new ConstraintDefinitionException(sprintf('Property for fieldMapping "%s" does not exists in given entityClass.', $objectField));
            }

            if (!$this->entityMeta->hasField($entityField) && !$this->entityMeta->hasAssociation($entityField)) {
                throw new ConstraintDefinitionException(sprintf('The field "%s" is nott mapped by Doctrine, so it cannot be validated for uniqueness.', $entityField));
            }

            $fieldValue = $validationClass->getProperty($objectField)->getValue($this->validationObject);

            if (null === $fieldValue && !$this->constraint->ignoreNull) {
                throw new ConstraintDefinitionException('Unique value cannot be NULL.');
            }

            $criteria[$entityField] = $fieldValue;

            if (null !== $criteria[$entityField] && $this->entityMeta->hasAssociation($entityField)) {
                $this->getEntityManager()->initializeObject($criteria[$entityField]);
            }
        }

        return $criteria;
    }

    private function checkConstraint(array $criteria)
    {
        $result = $this->getRepository()->{$this->constraint->repositoryMethod}($criteria);

        if ($result instanceof \IteratorAggregate) {
            return $result->getIterator();
        }

        if ($result instanceof \Iterator) {
            $result->rewind();
            if ($result instanceof \Countable && 1 < \count($result)) {
                $result = [$result->current(), $result->current()];
            } else {
                $result = $result->current();
                $result = null === $result ? [] : [$result];
            }
        } elseif (\is_array($result)) {
            reset($result);
        } else {
            $result = null === $result ? [] : [$result];
        }

        return $result;
    }

    private function getRepository(): ObjectRepository
    {
        if (null === $this->repository) {
            $this->repository = $this->getEntityManager()->getRepository($this->constraint->entityClass);
        }

        return $this->repository;
    }

    private function formatWithIdentifiers($value)
    {
        if (!is_object($value) || $value instanceof \DateTimeInterface) {
            return $this->formatValue($value, self::PRETTY_DATE);
        }

        if ($this->entityMeta->getName() !== $idClass = get_class($value)) {
            if ($this->getEntityManager()->getMetadataFactory()->hasMetadataFor($idClass)) {
                $identifiers = $this->getEntityManager()->getClassMetadata($idClass)->getIdentifierValues($value);
            } else {
                $identifiers = [];
            }
        } else {
            $identifiers = $this->entityMeta->getIdentifierValues($value);
        }

        if (!$identifiers) {
            return sprintf('object("%s")', $idClass);
        }

        array_walk($identifiers, function (&$id, $field) {
            if (!is_object($id) || $id instanceof \DateTimeInterface) {
                $idAsString = $this->formatValue($id, self::PRETTY_DATE);
            } else {
                $idAsString = sprintf('object("%s")', get_class($id));
            }

            $id = sprintf('%s => %s', $field, $idAsString);
        });

        return sprintf('object("%s") identtified by (%s)', $idClass, implode(', ', $identifiers));
    }
}
