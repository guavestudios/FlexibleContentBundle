<?php

declare(strict_types=1);

namespace Guave\FlexibleContentBundle\Tests;

use Guave\FlexibleContentBundle\GuaveFlexibleContentBundle;
use PHPUnit\Framework\TestCase;

class GuaveFlexibleContentBundleTest extends TestCase
{
    public function testCanBeInstantiated(): void
    {
        $bundle = new GuaveFlexibleContentBundle();

        $this->assertInstanceOf('Guave\FlexibleContentBundle\GuaveFlexibleContentBundle', $bundle);
    }
}
