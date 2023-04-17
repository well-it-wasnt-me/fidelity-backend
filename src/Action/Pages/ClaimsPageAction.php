<?php

namespace App\Action\Pages;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\PhpRenderer;

final class ClaimsPageAction
{
    /**
     * @var PhpRenderer */
    private $renderer;

    public function __construct(PhpRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

            $this->renderer->setLayout('layout/layout.php');
            $this->renderer->addAttribute('css', [
                'https://cdn.datatables.net/v/dt/dt-1.13.4/b-2.3.6/sl-1.6.2/datatables.min.css'
            ]);
            $this->renderer->addAttribute('js', [
                'https://cdn.datatables.net/v/dt/dt-1.13.4/b-2.3.6/sl-1.6.2/datatables.min.js',
                'https://luca-vercelli.github.io/DataTable-AltEditor/src/dataTables.altEditor.free.js',
                '../assets/js/pages/claims.js'
            ]);

            return $this->renderer->render($response, 'claims/home.php');
    }
}
