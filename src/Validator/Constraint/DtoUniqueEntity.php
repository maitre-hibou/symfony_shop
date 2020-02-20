<?php

declare(strict_types=1);

namespace App\Validator\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * @see https://gist.github.com/webbertakken/569409670bfc7c079e276f79260105ed
 *
 * @Annotation
 */
final class DtoUniqueEntity extends Constraint
{
    public const NOT_UNIQUE_ERROR = 'e777db8d-3af0-41f6-8a73-55255375cdca';

    protected static $errorNames = [
        self::NOT_UNIQUE_ERROR => 'NOT_UNIQUE_ERROR',
    ];

    public $em;

    public $entityClass;

    public $errorPath;

    public $fieldMapping = [];

    public $ignoreNull = true;

    public $message = 'This value is already used.';

    public $repositoryMethod = 'findBy';

    public function getDefaultOption(): string
    {
        return 'entityClass';
    }

    public function getRequiredOptions(): array
    {
        return ['entityClass', 'fieldMapping'];
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

    public function validatedBy()
    {
        return sprintf('%sValidator', get_class($this));
    }
}
