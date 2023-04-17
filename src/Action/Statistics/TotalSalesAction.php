<?php
/*
 * Copyright (c) 2022. Moebius Integrated System.
 */

namespace App\Action\Statistics;

use App\Domain\Statistics\Repository\StatisticsRepository;
use App\Responder\Responder;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Action.
 */
final class TotalSalesAction
{
    private StatisticsRepository $statisticsRepository;

    private Responder $responder;

    /**
     * The constructor.
     *
     * @param StatisticsRepository $statisticsRepository The Repository
     * @param Responder $responder The responder
     */
    public function __construct(StatisticsRepository $statisticsRepository, Responder $responder)
    {
        $this->statisticsRepository = $statisticsRepository;
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
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $totalSales = $this->statisticsRepository->totalSales();

        if ($totalSales === false) {
            $totalSales = [];
        }

        return $this->responder->withJson($response, $totalSales[0]);
    }
}
