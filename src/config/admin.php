<?php

use App\Libraries\Helpers;
return [
    'super_danger_zone' => Helpers::getEnvWithFallback('I_KNOW_WHAT_I_AM_DOING_ENABLE_SUPER_DANGER_ZONE', false),

];