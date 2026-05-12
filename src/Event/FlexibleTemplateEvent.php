<?php

namespace Guave\FlexibleContentBundle\Event;

use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Model;
use Symfony\Contracts\EventDispatcher\Event;

class FlexibleTemplateEvent extends Event
{
    public function __construct(private FragmentTemplate $template, private Model $model)
    {
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getTemplate(): FragmentTemplate
    {
        return $this->template;
    }
}
