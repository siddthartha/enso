<?php declare(strict_types=1);

namespace Enso\System;

class Template
{
    private string $templateFile;

    public function __construct(string $templateFile)
    {
        if (! file_exists($templateFile))
        {
            throw new \Exception('No template found');
        }

        $this->templateFile = $templateFile;
    }

    public function render(array $vars): string
    {
        extract($vars, EXTR_SKIP);
        ob_start();


        require $this->templateFile;

        $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }
}