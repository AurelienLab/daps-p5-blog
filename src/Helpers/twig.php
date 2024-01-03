<?php

use Carbon\Carbon;
use Symfony\Component\Security\Csrf\CsrfTokenManager;
use Twig\Markup;

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


/**
 * Transforms DateTime to "time ago" format
 *
 * @param DateTimeInterface $dateTime
 *
 * @return string
 */
function dateTimeAgo(DateTimeInterface $dateTime): string
{
    $carbon = new Carbon($dateTime);
    return $carbon->locale('fr')->diffForHumans();
}


/**
 * Generate a CSRF token
 *
 * @param string $string
 *
 * @return string
 */
function generateCsrfField(string $string): Markup
{
    $tokenManager = new CsrfTokenManager();
    $rawString = '<input type="hidden" name="_csrf" value="'.$tokenManager->getToken($string).'">';

    return new Markup($rawString, 'UTF-8');
}
