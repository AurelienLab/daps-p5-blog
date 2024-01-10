<?php

namespace App\Core\Utils;

use App\Core\Classes\TwigEnvironment;
use EditorJS\EditorJS;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class DecodeEditorJS
{

    /**
     * @var Environment
     */
    private $twig;
    private $blocks = [];

    public function __construct($json_content)
    {
        // Initialize twig
        $loader = new FilesystemLoader(ROOT.'/templates');
        $this->twig = new TwigEnvironment($loader);

        try {
            $editor = new EditorJS($json_content, json_encode(config('editor')));
            $this->blocks = $editor->getBlocks();
        } catch (\Exception $e) {
            throw new \Exception('Error while parsing editorJS content : '.$e->getMessage());
        }
    }

    public function toHTML()
    {
        $html = '';

        foreach ($this->blocks as $block) {
            $html .= $this->twig->render('partials/blocks/'.$block['type'].'.html.twig', $block['data']);
        }

        return $html;
    }
}
