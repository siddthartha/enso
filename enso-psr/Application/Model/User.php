<?php

namespace Application\Model;

use Yiisoft\ActiveRecord\ActiveRecord;
use Yiisoft\Db\Connection\ConnectionInterface;

/**
 * Entity User.
 *
 * Database fields:
 * @property int $id
 * @property string $username
 * @property string $email
 * @property string $auth_token
 **/
final class User extends ActiveRecord
{
    public function __construct(private ConnectionInterface $db)
    {
    }

    public function db(): ConnectionInterface
    {
        return $this->db;
    }

    public function tableName(): string
    {
        return '{{%user}}';
    }
}