<?php

namespace Guave\FlexibleContentBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Guave\FlexibleContentBundle\GuaveFlexibleContentBundle;
use Guave\VisualRadioBundle\GuaveVisualRadioBundle;

class Plugin implements BundlePluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(GuaveFlexibleContentBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class, GuaveVisualRadioBundle::class])
                ->setReplace(['flexibleelement']),
        ];
    }
}
