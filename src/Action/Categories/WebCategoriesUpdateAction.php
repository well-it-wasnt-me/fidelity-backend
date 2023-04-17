<?php
/*
 * Copyright (c) 2022. Moebius Integrated System.
 */

namespace App\Action\Categories;


use App\Domain\Categories\Repository\CategoriesRepository;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class WebCategoriesUpdateAction
{
    private CategoriesRepository $categoriesRepository;

    private Responder $responder;

    /**
     * The constructor.
     *
     * @param CategoriesRepository $categoriesRepository The Repository
     * @param Responder $responder The responder
     */
    public function __construct(CategoriesRepository $categoriesRepository, Responder $responder)
    {
        $this->categoriesRepository = $categoriesRepository;
        $this->responder = $responder;
    }

    /**
     * Action.
     *
     * @param ServerRequestInterface $request The request
     * @param ResponseInterface $response The response
     *
     * @return ResponseInterface The response
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response ): ResponseInterface
    {
        $data = $request->getParsedBody();

        $prods = $this->categoriesRepository->updateCategory($data);

        return $this->responder->withJson($response, $prods);
    }
}
