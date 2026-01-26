<?php

use App\Libraries\Helpers;
return [

	'disable_custom_css_linking' => Helpers::getEnvWithFallback('APPEAR_DISABLE_CUSTOM_CSS_LINKING', 'false'),
	'disable_admin_appearance_css_settings' => Helpers::getEnvWithFallback('APPEAR_DISABLE_ADMIN_APPEARANCE_CSS_SETTINGS', 'false'),
	
];