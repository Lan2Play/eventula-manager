<?php

use App\Libraries\Helpers;
return [

	'url' => Helpers::getEnvWithFallback('EVENTULA_URL', 'https://eventula.com'),
	
];