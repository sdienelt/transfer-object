<?php declare(strict_types = 1);

namespace Picamator\TransferObject\Definition\Validator;

use Picamator\TransferObject\Definition\Enum\DefinitionTypeEnum;
use Picamator\TransferObject\Generated\DefinitionPropertyTransfer;

readonly class PropertyValidator implements PropertyValidatorInterface
{
    use VariableValidatorTrait;

    private const string UNION_TYPE_SEPARATOR = '|';

    private const string PROPERTY_NAME_ERROR_MESSAGE_TEMPLATE = 'Invalid property "%s" name.';
    private const string PROPERTY_DEFINITION_ERROR_MESSAGE_TEMPLATE = 'Neither "type" nor "typeCollection" is set on "%s" property definition.';
    private const string PROPERTY_TYPE_UNION_ERROR_MESSAGE_TEMPLATE = 'Union property "%s" type "%s" is not supported.';

    public function __construct(
        private ClassNameValidatorInterface $classNameValidator,
    ) {
    }

    public function validate(DefinitionPropertyTransfer $propertyTransfer): ?string
    {
        $validators = [
            $this->validatePropertyName(...),
            $this->validatePropertyDefinition(...),
            $this->validatePropertyUnionType(...),
            $this->validatePropertyType(...),
            $this->validatePropertyCollectionType(...),
        ];

        foreach ($validators as $validator) {
            $message = $validator($propertyTransfer);
            if ($message === null) {
                continue;
            }

            return $message;
        }

        return null;
    }

    private function validatePropertyCollectionType(DefinitionPropertyTransfer $propertyTransfer): ?string
    {
        if ($propertyTransfer->collectionType === null) {
            return null;
        }

        return $this->classNameValidator->validate($propertyTransfer->collectionType);
    }

    private function validatePropertyType(DefinitionPropertyTransfer $propertyTransfer): ?string
    {
        if ($propertyTransfer->type === null) {
            return null;
        }

        if (DefinitionTypeEnum::tryFrom($propertyTransfer->type) !== null) {
            return null;
        }

        return $this->classNameValidator->validate($propertyTransfer->type);
    }

    private function validatePropertyUnionType(DefinitionPropertyTransfer $propertyTransfer): ?string
    {
        $type = $propertyTransfer->type ?: $propertyTransfer->collectionType;
        if (!str_contains($type, static::UNION_TYPE_SEPARATOR)) {
            return null;
        }

        return sprintf(
            static::PROPERTY_TYPE_UNION_ERROR_MESSAGE_TEMPLATE,
            $propertyTransfer->propertyName,
            $type,
        );
    }

    private function validatePropertyDefinition(DefinitionPropertyTransfer $propertyTransfer): ?string
    {
        if ($propertyTransfer->type !== null || $propertyTransfer->collectionType !== null) {
            return null;
        }

        return sprintf(
            static::PROPERTY_DEFINITION_ERROR_MESSAGE_TEMPLATE,
            $propertyTransfer->propertyName,
        );
    }

    private function validatePropertyName(DefinitionPropertyTransfer $propertyTransfer): ?string
    {
        if ($this->isValid($propertyTransfer->propertyName)) {
            return null;
        }

        return sprintf(
            static::PROPERTY_NAME_ERROR_MESSAGE_TEMPLATE,
            $propertyTransfer->propertyName,
        );
    }
}