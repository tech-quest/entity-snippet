<?php
namespace App\Infrastructure\Dao;

require_once __DIR__ . '/../../../vendor/autoload.php';

use PDO;
use App\Domain\ValueObject\User\NewUser;

/**
 * ユーザー情報を操作するDAO
 */
final class UserDao extends Dao
{
    /**
     * DBのテーブル名
     */
    const TABLE_NAME = 'users';

    /**
     * ユーザーを追加する
     * @param  string $name
     * @param  string $mail
     * @param  string $password
     */
    public function create(NewUser $user): void
    {

        $sql = sprintf(
            'INSERT INTO %s (name, email, password) VALUES (:name, :email, :password)',
            self::TABLE_NAME
        );
        $name = $user->name();
        $email = $user->email();
        $hashedPassword = $user->password()->hash();

        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':name', $name->value(), PDO::PARAM_STR);
        $statement->bindValue(':email', $email->value(), PDO::PARAM_STR);
        $statement->bindValue(':password', $hashedPassword->value(), PDO::PARAM_STR);
        $statement->execute();
    }

    /**
     * ユーザーを検索する
     * @param  string $mail
     * @return array | null
     */
    public function findByEmail(string $email): ?array
    {
        $sql = sprintf(
            'SELECT * FROM %s WHERE email = :email',
            self::TABLE_NAME
        );
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue(':email', $email, PDO::PARAM_STR);
        $statement->execute();
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        return $user ? $user : null;
    }
}
