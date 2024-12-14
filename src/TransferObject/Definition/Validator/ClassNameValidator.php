<?php declare(strict_types = 1);

namespace Picamator\TransferObject\Definition\Validator;

readonly class ClassNameValidator implements ClassNameValidatorInterface
{
    use VariableValidatorTrait;

    private const string CLASS_NAME_ERROR_MESSAGE_TEMPLATE = 'Invalid class "%s" name.';

    public function validate(?string $className): ?string
    {
        if ($this->isValidVariable($className)) {
            return null;
        }

        return sprintf(self::CLASS_NAME_ERROR_MESSAGE_TEMPLATE, $className);
    }
}
