<?php

namespace App\Core\Classes;

use App\Core\Router\Router;
use MarcW\Heroicons\Twig\HeroiconsExtension;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\LoaderInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigEnvironment extends Environment
{

    public function __construct(LoaderInterface $loader, $options = [])
    {
        parent::__construct($loader, $options);

        $app = [
            'request' => Request::createFromGlobals(),
            'router' => Router::getInstance(),
        ];

        if (isset($options['user'])) {
            $app['user'] = $options['user'];
        }

        if (isset($options['flashes'])) {
            $app['flashes'] = $options['flashes'];
        }

        $this->addGlobal('app', $app);

        $this->addExtension(new IntlExtension());
        $this->addExtension(new HeroiconsExtension());

        // Get a config value
        $this->addFunction(new TwigFunction('config', 'config'));

        // Enable dump function in twig
        $this->addFunction(new TwigFunction('dump', 'twigDump'));

        // Get Route url from name & args
        $this->addFunction(new TwigFunction('route', 'route'));

        // Generate csrf token
        $this->addFunction(new TwigFunction('csrf', 'generateCsrfField'));

        // Get form error
        if (isset($options['formErrors'])) {
            $this->addFunction(new TwigFunction('error', [$options['formErrors'], 'getError']));
        }

        // EditorJs clean data to html
        $this->addFunction(new TwigFunction('editorjs_to_html', 'editorJsToHtml'));

        $this->addFunction(new TwigFunction('starts_with', 'str_starts_with'));

        // Get datetime value as (XX min ago)
        $this->addFilter(new TwigFilter('diff', 'dateTimeAgo'));
    }
}
