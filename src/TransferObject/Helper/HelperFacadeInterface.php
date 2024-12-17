<?php declare(strict_types = 1);

namespace Picamator\TransferObject\Helper;

use Picamator\TransferObject\Generated\HelperTransfer;
use Picamator\TransferObject\Generated\HelperValidatorTransfer;

interface HelperFacadeInterface
{
    /**
     * Specification:
     * - Generates Definitions by iterable transferData
     * - Saves Definition on the file
     * - Returns `true` in success or `false` otherwise
     *
     * @throws \Picamator\TransferObject\Exception\GeneratorTransferException
     */
    public function generateDefinitions(HelperTransfer $helperTransfer): HelperValidatorTransfer;
}
