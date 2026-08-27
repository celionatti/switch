<?php

declare(strict_types=1);

use Switch\Diagram\Diagram;

if (!function_exists('diagram')) {
    /**
     * Get the Diagram manager instance.
     */
    function diagram(): Diagram
    {
        return Diagram::getInstance();
    }
}
