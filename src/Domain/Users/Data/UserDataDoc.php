<?php

namespace App\Domain\Users\Data;

use Selective\ArrayReader\ArrayReader;

/**
 * Data Model.
 */
final class UserDataDoc
{
    public ?int $user_id = null;

    public ?string $f_name = null;

    public ?string $l_name = null;

    public ?string $email = null;

    public ?string $passwd = null;

    public ?int $user_role = null;

    public ?string $reg_date = null;

    public ?string $locale = null;

    public ?int $account_status = null;
    public $password;

    /**
     * The constructor.
     *
     * @param array $data The data
     */
    public function __construct(array $data = [])
    {
        $reader = new ArrayReader($data);

        $this->user_id = $reader->findInt('user_id');
        $this->f_name = $reader->findString('first_name');
        $this->l_name = $reader->findString('last_name');
        $this->email = $reader->findString('email');
        $this->passwd = $reader->findString('password');
        $this->user_role = $reader->findInt('user_type');
        $this->reg_date = $reader->findString('reg_date');
        $this->locale = $reader->findString('locale');
        $this->account_status = $reader->findInt('account_status');
    }
}