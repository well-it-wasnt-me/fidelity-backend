<?php
/*
 * Copyright (c) 2022. Moebius Integrated System.
 */

namespace App\Action\Products;


use App\Domain\Products\Repository\ProductsRepository;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class WebProductUpdateAction
{
    private ProductsRepository $productsRepository;

    private Responder $responder;

    /**
     * The constructor.
     *
     * @param ProductsRepository $productsRepository The Repository
     * @param Responder $responder The responder
     */
    public function __construct(ProductsRepository $productsRepository, Responder $responder)
    {
        $this->productsRepository = $productsRepository;
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

        $prods = $this->productsRepository->updateProduct($data);

        return $this->responder->withJson($response, $prods);
    }
}
