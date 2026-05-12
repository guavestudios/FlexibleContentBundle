<?php

declare(strict_types=1);

namespace Guave\FlexibleContentBundle\Controller\ContentElement;

use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\FilesModel;
use Contao\StringUtil;
use Contao\System;
use Guave\FlexibleContentBundle\Event\FlexibleTemplateEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsContentElement('flexibleContent', category: 'flexibleContent', template: 'content_element/flexible-content')]
class FlexibleContentController extends AbstractContentElementController
{
    public function __construct(private readonly EventDispatcherInterface $eventDispatcher)
    {
    }

    public static function prepareImages(ContentModel $model, string $attribute): array
    {
        if ($model->$attribute === null) {
            return [];
        }

        if (!$model->$attribute || !\is_array(StringUtil::deserialize($model->$attribute))) {
            return self::getImageData(FilesModel::findByUuid($model->$attribute));
        }

        $images = [];
        $files = FilesModel::findMultipleByUuids(StringUtil::deserialize($model->$attribute));

        if ($files) {
            foreach ($files as $file) {
                $images[] = self::getImageData($file);
            }
        }

        return $images;
    }

    /**
     * @return array<string>
     */
    public static function getImageData(FilesModel $model): array
    {
        $rootDir = System::getContainer()->getParameter('kernel.project_dir');

        if (!is_file($rootDir.'/'.$model->path)) {
            return [];
        }

        if ($model->meta) {
            $meta = StringUtil::deserialize($model->meta);
            $meta = $meta[$GLOBALS['TL_LANGUAGE']];
        } else {
            $meta['title'] = $model->name;
        }

        return [
            'src' => '/'.$model->path,
            'name' => $model->name,
            'title' => $meta['title'],
        ];
    }

    protected function getResponse(FragmentTemplate $template, ContentModel $model, Request $request): Response
    {
        $template->set('flexibleTemplate', $model->flexibleTemplate);
        $template->set('flexibleTitle', $model->flexibleTitle);
        $template->set('flexibleSubtitle', $model->flexibleSubtitle);
        $template->set('flexibleText', $model->flexibleText);
        $template->set('flexibleTextColumn', $model->flexibleTextColumn);
        $template->set('flexibleImages', self::prepareImages($model, 'flexibleImages'));
        $template->set('flexibleImagesColumn', self::prepareImages($model, 'flexibleImagesColumn'));

        $event = new FlexibleTemplateEvent($template, $model);
        $this->eventDispatcher->dispatch($event, 'guave.flexibleTemplate');

        return $template->getResponse();
    }
}
