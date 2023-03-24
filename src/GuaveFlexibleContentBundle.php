<?php

namespace Guave\FlexibleContentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class GuaveFlexibleContentBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
