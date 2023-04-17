<?php

namespace App\Action\Pages;

use App\Moebius\Definition;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\PhpRenderer;

final class HomeAdminPageAction
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
            /*
            $this->renderer->addAttribute('css', [
                '/assets/js/plugins/datatables-bs5/dataTables.bootstrap5.css',
                '/assets/js/plugins/datatables-buttons-bs5/buttons.bootstrap5.min.css',
                '/assets/js/plugins/sweetalert2/sweetalert2.css'
            ]);*/

            $this->renderer->addAttribute('js', [
                '../assets/js/pages/home.js'
            ]);

            return $this->renderer->render($response, 'home/home.php');

    }
}
