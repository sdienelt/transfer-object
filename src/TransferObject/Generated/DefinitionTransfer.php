<?php declare(strict_types = 1);

namespace Picamator\TransferObject\Generated;

use Picamator\TransferObject\Transfer\AbstractTransfer;
use Picamator\TransferObject\Transfer\Attribute\PropertyTypeAttribute;

/**
 * Specification:
 * - Class is generated based on definition.
 * - In order to modify file please change definition and run generator.
 *
 * Generated on 2024-12-10 21:10:30
 */
final class DefinitionTransfer extends AbstractTransfer
{
    protected const int META_DATA_SIZE = 2;

    protected const array META_DATA = [
        self::CONTENT => self::CONTENT_DATA_NAME,
        self::VALIDATOR => self::VALIDATOR_DATA_NAME,
    ];

    // template
    #[PropertyTypeAttribute(DefinitionContentTransfer::class)]
    public const string CONTENT = 'content';
    protected const string CONTENT_DATA_NAME = 'CONTENT';
    protected const int CONTENT_DATA_INDEX = 0;

    public ?DefinitionContentTransfer $content {
        get => $this->data[self::CONTENT_DATA_INDEX];
        set => $this->data[self::CONTENT_DATA_INDEX] = $value;
    }

    // validator
    #[PropertyTypeAttribute(DefinitionValidatorTransfer::class)]
    public const string VALIDATOR = 'validator';
    protected const string VALIDATOR_DATA_NAME = 'VALIDATOR';
    protected const int VALIDATOR_DATA_INDEX = 1;

    public ?DefinitionValidatorTransfer $validator {
        get => $this->data[self::VALIDATOR_DATA_INDEX];
        set => $this->data[self::VALIDATOR_DATA_INDEX] = $value;
    }
}
