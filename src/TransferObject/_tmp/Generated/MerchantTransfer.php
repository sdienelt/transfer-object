<?php declare(strict_types = 1);

namespace Picamator\TransferObject\_tmp\Generated;

use Picamator\TransferObject\Transfer\AbstractTransfer;

final class MerchantTransfer extends AbstractTransfer
{
    protected const int META_DATA_SIZE = 2;

    protected const array META_DATA = [
        self::MERCHANT_REFERENCE => self::MERCHANT_REFERENCE_DATA_NAME,
        self::IS_ACTIVE => self::IS_ACTIVE_DATA_NAME,
    ];

    public const string MERCHANT_REFERENCE = 'merchantReference';
    protected const string MERCHANT_REFERENCE_DATA_NAME = 'MERCHANT_REFERENCE';
    protected const int MERCHANT_REFERENCE_DATA_INDEX = 0;

    public const string IS_ACTIVE = 'isActive';
    protected const string IS_ACTIVE_DATA_NAME = 'IS_ACTIVE';
    protected const int IS_ACTIVE_DATA_INDEX = 1;

    public ?string $merchantReference {
        get => $this->data[self::MERCHANT_REFERENCE_DATA_INDEX];
        set => $this->data[self::MERCHANT_REFERENCE_DATA_INDEX] = $value;
    }

    public bool $isActive {
        get => $this->data[self::IS_ACTIVE_DATA_INDEX];
        set => $this->data[self::IS_ACTIVE_DATA_INDEX] = $value;
    }
}
