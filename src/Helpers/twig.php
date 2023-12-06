<?php

/**
 * Dumps var without returning any data (avoid display as string in twig template)
 *
 * @param $vars
 *
 * @return void
 */
function twigDump($vars): void
{
    dump($vars);
}
