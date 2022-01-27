<?php

namespace Application\Model;

use Yiisoft\ActiveRecord\ActiveRecord;

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
    public function tableName(): string
    {
        return '{{%user}}';
    }
}