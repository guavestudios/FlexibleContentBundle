# Flexible Content Bundle

This contao module adds a Content Element that allows you to choose from different content layouts.

## Requirements

- Contao 4.9+ (tested up to 4.13)
- PHP 7.4 or 8.0+

## Install

```BASH
$ composer require guave/flexiblecontent-bundle
```

## Usage

To change which fields are shown or add a new one, add the template name without `ce_` into your `config.php`:

```PHP
$GLOBALS['TL_FLEXIBLE_CONTENT']['templates'] = [
    '2col-text',
];
```

then add a new subpalette for your template in your `tl_content.php`, only with the fields you would like to add as your
content:

```PHP
$GLOBALS['TL_DCA']['tl_content']['subpalettes']['flexibleTemplate_2col-text'] = 'flexibleTitle,flexibleText,flexibleTextColumn';
```

using the name of your template from `$GLOBALS['TL_FLEXIBLE_CONTENT']['templates']` in the
subpalette's `flexibleTemplate_<template-name>` key
