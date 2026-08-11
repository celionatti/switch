<?php

declare(strict_types=1);

use Switch\Head\Head;
use Switch\Head\HeadManager;

if (!function_exists('head')) {
    /**
     * Access the HeadManager instance or render all head tags.
     */
    function head(): HeadManager
    {
        return Head::getInstance();
    }
}
