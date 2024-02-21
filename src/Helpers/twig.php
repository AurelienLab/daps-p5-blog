<?php

use App\Core\Utils\DecodeEditorJS;
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
 * @return Markup
 */
function generateCsrfField(string $string): Markup
{
    $tokenManager = new CsrfTokenManager();
    $rawString = '<input type="hidden" name="_csrf" value="'.$tokenManager->getToken($string).'">';

    return new Markup($rawString, 'UTF-8');
}

/**
 * @param string $data
 *
 * @return Markup
 */
function editorJsToHtml(string $data): Markup
{
    return new Markup((new DecodeEditorJS($data))->toHTML(), 'UTF-8');
}

/**
 * Simplify str_starts_with usage in twig by allowing null value
 *
 * @param string|null $haystack
 * @param string $needle
 *
 * @return bool
 */
function twigStartsWith(?string $haystack, string $needle)
{
    if (!empty($haystack)) {
        return str_starts_with($haystack, $needle);
    }

    return false;
}
