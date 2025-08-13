<?php

$GLOBALS['TL_DCA']['tl_content']['palettes']['__selector__'][] = 'flexibleTemplate';
$GLOBALS['TL_DCA']['tl_content']['palettes']['flexibleContent'] = '{type_legend},type;{config_legend},flexibleTemplate;{template_legend:hide},customTpl;';

$GLOBALS['TL_DCA']['tl_content']['subpalettes']['flexibleTemplate_1col-img'] = 'flexibleTitle,flexibleSubtitle,flexibleImages';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['flexibleTemplate_1col-text'] = 'flexibleTitle,flexibleSubtitle,flexibleText';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['flexibleTemplate_2col-img-img'] = 'flexibleTitle,flexibleSubtitle,flexibleImages,flexibleImagesColumn';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['flexibleTemplate_2col-img-text'] = 'flexibleTitle,flexibleSubtitle,flexibleImages,flexibleTextColumn';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['flexibleTemplate_2col-text'] = 'flexibleTitle,flexibleSubtitle,flexibleText,flexibleTextColumn';
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['flexibleTemplate_2col-text-img'] = 'flexibleTitle,flexibleSubtitle,flexibleText,flexibleImagesColumn';

$GLOBALS['TL_DCA']['tl_content']['fields']['flexibleTemplate'] = [
    'inputType' => 'visualradio',
    'options' => $GLOBALS['TL_FLEXIBLE_CONTENT']['templates'],
    'eval' => [
        'mandatory' => true,
        'submitOnChange' => true,
        'imagePath' => $GLOBALS['TL_FLEXIBLE_CONTENT']['iconPath'],
        'imageExt' => $GLOBALS['TL_FLEXIBLE_CONTENT']['iconExt'],
        'tl_class' => 'w100 clr',
    ],
    'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['flexibleTitle'] = [
    'inputType' => 'text',
    'eval' => ['tl_class' => 'w100 clr'],
    'sql' => ['type' => 'string', 'length' => 255, 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['flexibleSubtitle'] = [
    'inputType' => 'textarea',
    'eval' => ['rows' => 10, 'cols' => 100, 'tl_class' => 'w100 clr'],
    'sql' => ['type' => 'text', 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['flexibleText'] = [
    'inputType' => 'textarea',
    'eval' => ['rows' => 10, 'cols' => 100, 'rte' => 'tinyMCE', 'tl_class' => 'w50'],
    'sql' => ['type' => 'text', 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['flexibleTextColumn'] = [
    'inputType' => 'textarea',
    'eval' => ['rows' => 10, 'cols' => 100, 'rte' => 'tinyMCE', 'tl_class' => 'w50'],
    'sql' => ['type' => 'text', 'notnull' => false],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['flexibleImages'] = [
    'inputType' => 'fileTree',
    'eval' => [
        'multiple' => true,
        'filesOnly' => true,
        'fieldType' => 'checkbox',
        'extensions' => '%contao.image.valid_extensions%',
        'isSortable' => true,
        'isGallery' => true,
        'tl_class' => 'w50',
    ],
    'sql' => ['type' => 'blob', 'notnull' => false],
    'load_callback' => [
        ['tl_content', 'setMultiSrcFlags'],
    ],
];

$GLOBALS['TL_DCA']['tl_content']['fields']['flexibleImagesColumn'] = [
    'inputType' => 'fileTree',
    'eval' => [
        'multiple' => true,
        'filesOnly' => true,
        'fieldType' => 'checkbox',
        'extensions' => '%contao.image.valid_extensions%',
        'isSortable' => true,
        'isGallery' => true,
        'tl_class' => 'w50',
    ],
    'sql' => ['type' => 'blob', 'notnull' => false],
    'load_callback' => [
        ['tl_content', 'setMultiSrcFlags'],
    ],
];
