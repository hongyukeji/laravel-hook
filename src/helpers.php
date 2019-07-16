<?php

use Hongyukeji\Hook\Hook;

if (!function_exists('hook')) {

    /**
     * @return Hook
     */
    function hook()
    {
        return new Hook();
    }
}
