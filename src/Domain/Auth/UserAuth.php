<?php

namespace App\Domain\Auth;

use App\Factory\QueryFactory;
use App\Moebius\Definition;
use PDO;

class UserAuth
{
    /**
     * @var PDO
     */
    private PDO $connection;
    private QueryFactory $query;

    /**
     * UserAuth constructor.
     * @param PDO $pdo Database Connection
     */
    function __construct(PDO $pdo, QueryFactory $query)
    {
        $this->connection = $pdo;
        $this->query = $query;
    }

    /**
     * @param string $username Email
     * @param string $password Password
     * @return array
     */
    public function authenticate(string $username, string $password, string $role): array
    {

        $password = hash('sha512', $password);

        $q = "SELECT users.* FROM users
         WHERE users.email = :email 
           AND users.password = :password 
           AND users.account_status = ". Definition::ACCOUNT_ACTIVE ."
           AND users.account_role = " . Definition::ADMIN;
        $st = $this->connection->prepare($q);
        $st->bindParam(":email", $username);
        $st->bindParam(":password", $password);

        $st->execute();

        $data = $st->fetchAll();

        if (empty($data)) {
            return [];
        }

        return [
            'f_name'        => $data[0]['f_name'],
            'l_name'        => $data[0]['l_name'],
            'user_id'       => $data[0]['user_id'],
            'role'          => $data[0]['account_role'],
            'locale'        => $data[0]['locale'],
            'email'         => $data[0]['email'],
            'creation_date' => $data[0]['creation_date'],
            'account_status'=> $data[0]['account_status'],
        ];
    }

    public function authenticate_paz(string $username, string $password, string $role): array
    {

        $username = trim(strtolower($username));
        $password = trim($password);
        $password = hash('sha512', $password);

        $q = "SELECT * FROM users
         WHERE users.email = :email 
           AND users.password = :password 
           AND users.account_role = 1";
        $st = $this->connection->prepare($q);
        $st->bindParam(":email", $username);
        $st->bindParam(":password", $password);

        $st->execute();

        $data = $st->fetchAll();

        if (empty($data)) {
            return [];
        }

        return [
            'user_id'       => $data[0]['user_id'],
            'f_name'        => $data[0]['f_name'],
            'l_name'        => $data[0]['l_name'],
            'email'         => $data[0]['email'],
            'account_status'=> $data[0]['account_status'],
            'creation_date' => $data[0]['creation_date'],
            'locale'        => $data[0]['locale'],
            'full_addr'     => $data[0]['full_addr'],
            'phone_number'  => $data[0]['phone_number']
        ];
    }
}
