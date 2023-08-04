<?php

namespace Guave\FlexibleContentBundle\Controller\ContentElement;

use Contao\BackendTemplate;
use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\FilesModel;
use Contao\System;
use Contao\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @ContentElement("flexibleContent", category="flexibleContent", template="content_element/1col-img")
 */
#[AsContentElement('flexibleContent', category: 'flexibleContent', template: 'content_element/1col-img')]
class FlexibleContentController extends AbstractContentElementController
{
    public function __invoke(Request $request, ContentModel $model, string $section, array $classes = null): Response
    {
        $type = $this->getType();
        if ($model->customTpl === '') {
            $model->customTpl = 'content_element/' . $model->flexibleTemplate;
        }
        $template = $this->createTemplate($model, 'ce_' . $type);

        $this->addHeadlineToTemplate($template, $model->headline);
        $this->addCssAttributesToTemplate($template, 'ce_' . $type, $model->cssID, $classes);
        $this->addPropertiesToTemplate($template, $request->attributes->get('templateProperties', []));
        $this->addSectionToTemplate($template, $section);
        $this->tagResponse($model);

        $response = $this->getResponse($template, $model, $request);

        if (null === $response) {
            trigger_deprecation('contao/core-bundle', '4.12', 'Returning null in %s::getResponse() is deprecated, return a Response instead.', static::class);
            $response = $template->getResponse();
        }

        return $response;
    }

    protected function getResponse(Template $template, ContentModel $model, Request $request): ?Response
    {
        $scope = System::getContainer()->get('contao.routing.scope_matcher');
        if ($scope && $scope->isBackendRequest($request)) {
            $template = new BackendTemplate('be_wildcard');
            $template->title = $model->flexibleTitle;
            $template->wildcard = $model->flexibleSubtitle;
            return $template->getResponse();
        }

        $template->flexibleImages = self::prepareImages($model, 'orderSRC');
        $template->flexibleImagesColumn = self::prepareImages($model, 'orderSRC2');

        return $template->getResponse();
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
            if ($file !== null) {
                $preparedImages[] = static::getImageData($file);
            }
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
