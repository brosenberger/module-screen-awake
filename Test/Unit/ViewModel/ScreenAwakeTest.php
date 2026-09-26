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

namespace BroCode\ScreenAwake\Test\Unit\ViewModel;

use BroCode\ScreenAwake\ViewModel\ScreenAwake;
use Magento\Catalog\Model\Product;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use PHPUnit\Framework\TestCase;

class ScreenAwakeTest extends TestCase
{
    public function testDisabledStoreHidesTheButtonEvenWhenTheProductAsksForIt(): void
    {
        $viewModel = new ScreenAwake($this->config(false));

        self::assertFalse($viewModel->isEnabled());
        self::assertFalse($viewModel->isEnabledForProduct($this->product('1')));
    }

    public function testEnabledStoreShowsTheButtonOnlyOnProductsThatAskForIt(): void
    {
        $viewModel = new ScreenAwake($this->config(true));

        self::assertTrue($viewModel->isEnabled());
        self::assertTrue($viewModel->isEnabledForProduct($this->product('1')));
        self::assertFalse($viewModel->isEnabledForProduct($this->product('0')));
        self::assertFalse($viewModel->isEnabledForProduct($this->product(null)));
    }

    public function testMissingProductNeverShowsTheButton(): void
    {
        self::assertFalse((new ScreenAwake($this->config(true)))->isEnabledForProduct(null));
    }

    private function config(bool $enabled): ScopeConfigInterface
    {
        $config = $this->createMock(ScopeConfigInterface::class);
        $config->method('isSetFlag')
            ->with(ScreenAwake::XML_PATH_ENABLED, ScopeInterface::SCOPE_STORE)
            ->willReturn($enabled);

        return $config;
    }

    private function product(?string $attributeValue): Product
    {
        $product = $this->createMock(Product::class);
        $product->method('getData')
            ->with(ScreenAwake::PRODUCT_ATTRIBUTE)
            ->willReturn($attributeValue);

        return $product;
    }
}
