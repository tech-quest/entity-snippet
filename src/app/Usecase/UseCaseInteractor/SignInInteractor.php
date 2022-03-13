<?php
namespace App\Usecase\UseCaseInteractor;

use App\Domain\Entity\User;
use App\Domain\ValueObject\User\UserId;
use App\Domain\ValueObject\User\UserName;
use App\Domain\ValueObject\Email;
use App\Domain\ValueObject\HashedPassword;
use App\Usecase\UseCaseInput\SignInInput;
use App\Usecase\UseCaseOutput\SignInOutput;
use App\Infrastructure\Dao\UserDao;

final class SignInInteractor
{
    /**
     * ログイン失敗時のエラーメッセージ
     */
    const FAILED_MESSAGE = 'メールアドレスまたは<br />パスワードが間違っています';

    /**
     * ログイン成功時のメッセージ
     */
    const SUCCESS_MESSAGE = 'ログインしました';

     /**
     * @var UserDao
     */
    private $userDao;

    /**
     * @var SignInInput
     */
    private $input;

    public function __construct(SignInInput $input)
    {
        $this->userDao = new UserDao();
        $this->input = $input;
    }

    /**
     * ログイン処理
     * セッションへのユーザー情報の保存も行う
     * 
     * @return SignInOutput
     */
    public function handler(): SignInOutput
    {
        $userMapper = $this->findUser();

        if ($this->notExistsUser($userMapper)) {
            return new SignInOutput(false, self::FAILED_MESSAGE);
        }

        $user = $this->buildUserEntity($userMapper);

        if ($this->isInvalidPassword($user->password()->value())) {
            return new SignInOutput(false, self::FAILED_MESSAGE);
        }

        $this->saveSession($user);

        return new SignInOutput(true, self::SUCCESS_MESSAGE);
    }

    /**
     * ユーザーを入力されたメールアドレスで検索する
     * 
     * @return array | null
     */
    private function findUser(): ?array
    {
        return $this->userDao->findByEmail($this->input->email());
    }

    /**
     * ユーザーが存在しない場合
     *
     * @param array|null $user
     * @return boolean
     */
    private function notExistsUser(?array $user): bool
    {
        return is_null($user);
    }

    private function buildUserEntity(array $user): User
    {
        return new User(
            new UserId($user['id']), 
            new UserName($user['name']), 
            new Email($user['email']), 
            new HashedPassword($user['password']));
    }

    /**
     * パスワードが正しいかどうか
     *
     * @param HashedPassword $hashedPassword
     * @return boolean
     */
    private function isInvalidPassword(string $password): bool
    {
        return !password_verify($this->input->password(), $password);
    }

    /**
     * セッションの保存処理
     *
     * @param User $user
     * @return void
     */
    private function saveSession(User $user): void
    {
        $_SESSION['user']['id'] = $user->id();
        $_SESSION['user']['name'] = $user->name();
    }
}
