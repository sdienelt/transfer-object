<?php declare(strict_types = 1);

namespace Picamator\TransferObject\Helper;

use Picamator\TransferObject\Generated\HelperTransfer;
use Picamator\TransferObject\Generated\HelperValidatorTransfer;

readonly class HelperFacade implements HelperFacadeInterface
{
    public function generateDefinitions(HelperTransfer $helperTransfer): HelperValidatorTransfer
    {
        return new HelperFactory()
            ->createDefinitionGenerator()
            ->generateDefinitions($helperTransfer);
    }
}
