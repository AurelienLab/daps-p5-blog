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

function dateTimeAgo(DateTimeInterface $dateTime): string
{
    $carbon = new \Carbon\Carbon($dateTime);
    return $carbon->locale('fr')->diffForHumans();
}
