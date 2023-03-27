<?php

namespace Guave\FlexibleContentBundle\Controller\ContentElement;

use Contao\BackendTemplate;
use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\CoreBundle\ServiceAnnotation\ContentElement;
use Contao\FilesModel;
use Contao\System;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ContentElement("flexibleContent", category="flexibleContent", template="content_element/1col-img")
 */
#[AsContentElement('flexibleContent', category:'flexibleContent', template:'content_element/1col-img')]
class FlexibleContentController extends AbstractContentElementController
{
    protected function getResponse(Template $template, ContentModel $model, Request $request): ?Response
    {
        if (System::getContainer()->get('contao.routing.scope_matcher')->isBackendRequest($request)) {
            $template = new BackendTemplate('be_wildcard');
            $template->title = $model->flexibleTitle;
            $template->wildcard = $model->flexibleSubtitle;
            return $template->getResponse();
        }

        $this->strTemplate = $model->flexibleTemplate;

        if ($model->customTpl) {
            $this->strTemplate = $model->customTpl;
        }

        $model->flexibleImages = self::prepareImages($model, 'orderSRC');
        $model->flexibleImagesColumn = self::prepareImages($model, 'orderSRC2');

        return $this->render('@Contao/content_element/' . $this->strTemplate . '.html.twig', $model->row());
    }

    public static function prepareImages(ContentModel $model, string $attribute): array
    {
        $preparedImages = [];
        $images = unserialize($model->$attribute);

        if (empty($images)) {
            return $preparedImages;
        }

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
