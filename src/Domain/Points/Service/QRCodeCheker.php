<?php

namespace App\Domain\Points\Service;

use App\Domain\Points\Repository;
use App\Moebius\Definition;
use App\Moebius\Token;

/**
 * Service.
 */
final class QRCodeCheker
{
    private $repository;
    private $token;
    /**
     * The constructor.
     *
     * @param Repository\PointsRepository $repository The repository
     */
    public function __construct(
        Repository\PointsRepository $repository,
        Token $token
    ) {
        $this->repository = $repository;
        $this->token = $token;
    }

    /**
     * Check if the code is valid or not
     * @param $token Token received from app
     * @return bool
     */
    public function checkCode(string $token)
    {
        // Check token validity
        if (!$this->token->verify_token($token)) {
            return false;
        }

        if (!$this->repository->checkToken($token)) {
            return false;
        }

        return true;
    }
}
