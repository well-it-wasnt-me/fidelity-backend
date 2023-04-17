<?php

namespace App\Domain\Users\Service;

use App\Domain\Users\Data\UserDataDoc;
use App\Domain\Users\Repository\UserRepository;
use App\Moebius\Definition;

/**
 * Service.
 */
final class FileCreator
{


    /**
     * The constructor.
     *
     * @param UserRepository $repository The repository
     * @param UserValidator $userValidator The validator
     */
    public function __construct(
        UserRepository $repository,
        UserValidator $userValidator
    ) {

    }

    /**
     * Create a new user.
     *
     * @param int $uid The form data
     *
     * @return string Path to the zip file
     */
    public function createFiles($uid): string
    {
        // assegnazione_farmaci
        // calendar
        // diaries
        // patients
        // invoices
        // trackings

        return "";
    }
}
