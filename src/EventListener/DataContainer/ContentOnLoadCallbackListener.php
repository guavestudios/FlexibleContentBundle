<?php

namespace Guave\FlexibleContentBundle\EventListener\DataContainer;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\System;

#[AsCallback(table: 'tl_content', target: 'config.onload')]
class ContentOnLoadCallbackListener
{
    public function __invoke(): void
    {
        $GLOBALS['TL_CSS']['guaveflexiblecontent'] = System::getContainer()->get('kernel')->isDebug()
            ? 'bundles/guaveflexiblecontent/css/backend.css'
            : 'bundles/guaveflexiblecontent/css/backend.min.css';
    }
}
