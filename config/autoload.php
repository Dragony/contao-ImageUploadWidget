<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @package ImageUploadWidget
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Widgets
	'Contao\ImageUpload' => 'system/modules/ImageUploadWidget/widgets/ImageUpload.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'be_image_widget' => 'system/modules/ImageUploadWidget/templates',
));
