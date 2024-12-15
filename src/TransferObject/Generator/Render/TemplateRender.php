<?php declare(strict_types = 1);

namespace Picamator\TransferObject\Generator\Render;

use Picamator\TransferObject\Config\Container\ConfigInterface;
use Picamator\TransferObject\Exception\GeneratorTransferException;
use Picamator\TransferObject\Generated\DefinitionContentTransfer;
use Picamator\TransferObject\Generated\DefinitionPropertyTransfer;
use Picamator\TransferObject\Generated\TemplateTransfer;
use Picamator\TransferObject\Generator\Enum\ArrayEnum;
use Picamator\TransferObject\Generator\Enum\ArrayObjectEnum;
use Picamator\TransferObject\Generator\Enum\CollectionPropertyTypeEnum;
use Picamator\TransferObject\Generator\Enum\PropertyTypeEnum;
use Picamator\TransferObject\Generator\Enum\TransferEnum;

readonly class TemplateRender implements TemplateRenderInterface
{
    use TemplateRenderTrait;

    private const string TEMPLATE_PATH = __DIR__ . DIRECTORY_SEPARATOR . 'Template.tpl.php';

    public function __construct(
        private ConfigInterface $config,
    ) {
    }

    public function renderTemplate(DefinitionContentTransfer $contentTransfer): string
    {
        $templateTransfer = $this->createTemplateTransfer($contentTransfer);

        ob_start();
        include self::TEMPLATE_PATH;
        $renderedOutput = ob_get_clean();

        $lastError = error_get_last();
        if ($lastError === null) {
            return $renderedOutput;
        }

        throw new GeneratorTransferException(
            sprintf(
                'Template render error "%s", line "%s".',
                $lastError['message'],
                $lastError['line'],
            ),
        );
    }

    private function createTemplateTransfer(DefinitionContentTransfer $contentTransfer): TemplateTransfer
    {
        $templateTransfer = new TemplateTransfer();
        $templateTransfer->classNamespace = $this->config->getTransferNamespace();
        $templateTransfer->className = $this->getTransferName($contentTransfer->className);
        $templateTransfer->imports[] = TransferEnum::ABSTRACT_CLASS_NAME->value;

        foreach ($contentTransfer->properties as $propertyTransfer) {
            $propertyName = $propertyTransfer->propertyName;
            $templateTransfer->metaConstants[$this->getMetaConstant($propertyName)] = $propertyTransfer->propertyName;

            match (true) {
                $this->isTransferCollectionType($propertyTransfer) => $this->expandTransferCollectionType($propertyTransfer, $templateTransfer),
                $this->isTransferType($propertyTransfer) => $this->expandTransferType($propertyTransfer, $templateTransfer),
                default => $this->expandDefaultType($propertyTransfer, $templateTransfer),
            };
        }

        $templateTransfer->propertiesCount = $templateTransfer->properties->count();

        $this->sortAndUnify($templateTransfer);

        return $templateTransfer;
    }

    private function expandDefaultType(DefinitionPropertyTransfer $propertyTransfer, TemplateTransfer $templateTransfer): void
    {
        $propertyName = $propertyTransfer->propertyName;
        $templateTransfer->properties[$propertyName] = $propertyTransfer->type;

        if ($this->isArrayObject($propertyTransfer)) {
            $templateTransfer->imports[] = ArrayObjectEnum::CLASS_NAME->value;
            $templateTransfer->defaultValues[$propertyName] = ArrayObjectEnum::DEFAULT_VALUE_TEMPLATE->value;
            $templateTransfer->dockBlocks[$propertyName] = ArrayObjectEnum::DOCK_BLOCK_TEMPLATE->value;

            return;
        }

        if ($this->isArray($propertyTransfer)) {
            $templateTransfer->defaultValues[$propertyName] = ArrayEnum::TYPE->value;
            $templateTransfer->dockBlocks[$propertyName] = ArrayEnum::DOCK_BLOCK_TEMPLATE->value;
        }
    }

    private function expandTransferType(DefinitionPropertyTransfer $propertyTransfer, TemplateTransfer $templateTransfer): void
    {
        $templateTransfer->imports[] = PropertyTypeEnum::CLASS_NAME->value;

        $transferName = $this->getTransferName($propertyTransfer->type);
        $propertyName = $propertyTransfer->propertyName;

        $templateTransfer->properties[$propertyName] = $transferName;
        $templateTransfer->attributes[$propertyName] = sprintf(PropertyTypeEnum::ATTRIBUTE_TEMPLATE->value, $transferName);
    }

    private function expandTransferCollectionType(DefinitionPropertyTransfer $propertyTransfer, TemplateTransfer $templateTransfer): void
    {
        $templateTransfer->imports[] = ArrayObjectEnum::CLASS_NAME->value;
        $templateTransfer->imports[] = CollectionPropertyTypeEnum::CLASS_NAME->value;

        $transferName = $this->getTransferName($propertyTransfer->collectionType);

        $propertyName = $propertyTransfer->propertyName;
        $templateTransfer->properties[$propertyName] = ArrayObjectEnum::CLASS_NAME->value;
        $templateTransfer->attributes[$propertyName] = sprintf(CollectionPropertyTypeEnum::ATTRIBUTE_TEMPLATE->value, $transferName);
        $templateTransfer->dockBlocks[$propertyName] = sprintf(CollectionPropertyTypeEnum::DOCK_BLOCK_TEMPLATE->value, $transferName);
        $templateTransfer->defaultValues[$propertyName] = ArrayObjectEnum::DEFAULT_VALUE_TEMPLATE->value;
    }
}
