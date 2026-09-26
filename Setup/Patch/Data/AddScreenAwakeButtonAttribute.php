<?php
/**
 * Copyright (C) 2026 Benjamin Rosenberger <bensch.rosenberger@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @copyright 2026 Benjamin Rosenberger
 * @author bensch.rosenberger@gmail.com
 * @license MIT
 * @link https://brocode.at
 */
declare(strict_types=1);

namespace BroCode\ScreenAwake\Setup\Patch\Data;

use BroCode\EntityServices\Model\EntityServiceFactory;
use BroCode\ScreenAwake\ViewModel\ScreenAwake;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;

/**
 * Yes/No product attribute that switches the button on per product (store view scope).
 */
class AddScreenAwakeButtonAttribute implements DataPatchInterface, PatchRevertableInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EntityServiceFactory
     */
    private $entityServiceFactory;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EntityServiceFactory $entityServiceFactory,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->entityServiceFactory = $entityServiceFactory;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function apply(): self
    {
        $this->moduleDataSetup->startSetup();
        $this->entityServiceFactory
            ->createAttributeBuilder($this->moduleDataSetup)
            ->withProductAttribute(ScreenAwake::PRODUCT_ATTRIBUTE)
            ->withTypeInt()
            ->withInputBoolean()
            ->withSource(Boolean::class)
            ->withLabel("Show 'Keep Screen On' Button")
            ->withDefault('0')
            ->withStoreScope()
            ->inGroup('Content')
            ->withSortOrder(90)
            ->asRequired(false)
            ->asUserDefined(true)
            ->asVisibleOnFront(false)
            ->asUsedInProductListing(false)
            ->asSearchable(false)
            ->asFilterable(false)
            ->asComparable(false)
            ->build();
        $this->moduleDataSetup->endSetup();

        return $this;
    }

    /**
     * EntityServices builds attributes but has no removal, so the revert uses EavSetup.
     */
    public function revert(): void
    {
        $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup])
            ->removeAttribute(Product::ENTITY, ScreenAwake::PRODUCT_ATTRIBUTE);
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
