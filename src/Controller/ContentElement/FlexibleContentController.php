<?php

declare(strict_types=1);

namespace Guave\FlexibleContentBundle\Controller\ContentElement;

use Contao\BackendTemplate;
use Contao\ContentModel;
use Contao\CoreBundle\Controller\ContentElement\AbstractContentElementController;
use Contao\CoreBundle\DependencyInjection\Attribute\AsContentElement;
use Contao\FilesModel;
use Contao\StringUtil;
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
    /**
     * @param array<string>|null $classes
     */
    public function __invoke(Request $request, ContentModel $model, string $section, array $classes = null): Response
    {
        if (isset($GLOBALS['TL_HOOKS']['flexibleTemplateField']) && \is_array($GLOBALS['TL_HOOKS']['flexibleTemplateField'])) {
            foreach ($GLOBALS['TL_HOOKS']['flexibleTemplateField'] as $callback) {
                $model = System::importStatic($callback[0])->{$callback[1]}($model, $this);
            }
        }

        $type = $this->getType();

        if ($model->customTpl === '') {
            $model->customTpl = 'content_element/'.$model->flexibleTemplate;
        }
        $template = $this->createTemplate($model, 'ce_'.$type);

        $this->addHeadlineToTemplate($template, $model->headline);
        $this->addCssAttributesToTemplate($template, 'ce_'.$type, $model->cssID, $classes);
        $this->addPropertiesToTemplate($template, $request->attributes->get('templateProperties', []));
        $this->addSectionToTemplate($template, $section);
        $this->tagResponse($model);

        $response = $this->getResponse($template, $model, $request);

        if ($response === null) {
            trigger_deprecation('contao/core-bundle', '4.12', 'Returning null in %s::getResponse() is deprecated, return a Response instead.', static::class);
            $response = $template->getResponse();
        }

        return $response;
    }

    /**
     * @return array<string>|array<array<string>>
     */
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

        foreach ($files as $file) {
            $images[] = self::getImageData($file);
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
            $meta = unserialize($model->meta);
            $meta = $meta[$GLOBALS['TL_LANGUAGE']];
        } else {
            $meta['title'] = $model->title;
        }

        return [
            'src' => '/'.$model->path,
            'name' => $model->name,
            'title' => $meta['title'],
        ];
    }

    protected function getResponse(Template $template, ContentModel $model, Request $request): Response|null
    {
        $scope = System::getContainer()->get('contao.routing.scope_matcher');

        if ($scope && $scope->isBackendRequest($request)) {
            $template = new BackendTemplate('be_wildcard');
            $template->title = $model->flexibleTitle;
            $beIcon = $GLOBALS['TL_FLEXIBLE_CONTENT']['iconPath'].'/'.$model->flexibleTemplate.$GLOBALS['TL_FLEXIBLE_CONTENT']['iconExt'];
            $template->wildcard = '<img src="'.$beIcon.'"><p>'.$model->flexibleTemplate.'</p>';

            return $template->getResponse();
        }

        $template->flexibleImages = self::prepareImages($model, 'orderSRC');
        $template->flexibleImagesColumn = self::prepareImages($model, 'orderSRC2');

        return $template->getResponse();
    }
}
