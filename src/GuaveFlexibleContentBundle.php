<?php

namespace Guave\FlexibleContentBundle;

use Guave\FlexibleContentBundle\DependencyInjection\GuaveFlexibleContentExtension;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GuaveFlexibleContentBundle extends Bundle
{
    /**
     * Register extension
     *
     * @return Extension
     */
    public function getContainerExtension(): Extension
    {
        return new GuaveFlexibleContentExtension();
    }
}
