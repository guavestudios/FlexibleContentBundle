<?php

namespace Guave\FlexibleContentBundle\Controller\ContentElement;

use Contao\BackendTemplate;
use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\FilesModel;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ContentElement(
 *     "flexibleContent",
 *     category="flexibleContent",
 *     template="ce_flexible_content"
 * )
 */
class FlexibleContentElementController extends AbstractContentElementController
{
    protected string $strTemplate = 'ce_flexible_content';

    protected function getResponse(Template $template, ContentModel $model, Request $request): ?Response
    {
        if (TL_MODE === 'BE') {
            $this->strTemplate = 'be_wildcard';
            $template = new BackendTemplate($this->strTemplate);

            $template->title = $model->flexibleTitle;
            $template->wildcard = $model->flexibleSubtitle;

            return $template->getResponse();
        }

        $this->strTemplate = 'ce_' . $model->flexibleTemplate;

        if ($model->customTpl) {
            $this->strTemplate = $model->customTpl;
        }

        $flexibleImages = self::prepareImages($model, 'orderSRC');

        return $this->render('content-elements/' . $this->strTemplate . '.html.twig', [
            'flexibleTitle' => $model->flexibleTitle,
            'flexibleSubtitle' => $model->flexibleSubtitle,
            'flexibleText' => $model->flexibleText,
            'flexibleImages' => $flexibleImages,
        ]);
    }

    public static function prepareImages(ContentModel $model, string $attribute): array
    {
        $preparedImages = [];
        $images = unserialize($model->$attribute);

        foreach ($images as $image) {
            $file = FilesModel::findByUuid($image);
            $preparedImages[] = static::getImageData($file);
        }

        $model->$attribute = $preparedImages;
        return $preparedImages;
    }

    public static function getImageData(FilesModel $model): array
    {
        if (!is_file(TL_ROOT . '/' . $model->path)) {
            return [];
        }

        $meta = unserialize($model->meta);
        if ($meta) {
            $meta = $meta[$GLOBALS['TL_LANGUAGE']];
        } else {
            $meta['title'] = $model->name;
        }

        return [
            'src' => '/' . $model->path,
            'name' => $model->name,
            'title' => $meta['title'],
        ];
    }
}
