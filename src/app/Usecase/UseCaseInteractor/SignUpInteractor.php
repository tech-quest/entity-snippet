<?php
namespace App\Usecase\UseCaseInteractor;

use App\Usecase\UseCaseInput\SignUpInput;
use App\Usecase\UseCaseOutput\SignUpOutput;
use App\Infrastructure\Dao\UserDao;

final class SignUpInteractor
{
    /**
     * メールアドレスがすでに存在している場合のエラーメッセージ
     */
    const ALLREADY_EXISTS_MESSAGE = "すでに登録済みのメールアドレスです";

    /**
    * ユーザー登録成功時のメッセージ
    */
    const COMPLETED_MESSAGE = "登録が完了しました";

    /**
    * @var UserDao
    */
    private $userDao;

    /**
    * @var SignUpInput
    */
    private $useCaseInput;

    /**
    * コンストラクタ
    *
    * @param SignUpInput $input
    */
    public function __construct(SignUpInput $useCaseInput)
    {
        $this->userDao = new UserDao();
        $this->useCaseInput = $useCaseInput;
    }

    /**
    * ユーザー登録処理
    * すでに存在するメールアドレスの場合はエラーとする
    *
    * @return SignUpOutput
    */
    public function handler(): SignUpOutput
    {
        $userMapper = $this->findUser();

        if ($this->existsUser($userMapper)) {
        return new SignUpOutput(false, self::ALLREADY_EXISTS_MESSAGE);
        }

        $this->signup();
        return new SignUpOutput(true, self::COMPLETED_MESSAGE);
    }

    /**
     * ユーザーを入力されたメールアドレスで検索する
     *
     * @return array
     */
    private function findUser(): ?array
    {
        return $this->userDao->findByEmail($this->useCaseInput->email()->value());
    }

    /**
     * ユーザーが存在するかどうか
     *
     * @param array|null $user
     * @return boolean
     */
    private function existsUser(?array $user): bool
    {
        return !is_null($user);
    }

    /**
     * ユーザーを登録する
     *
     * @return void
     */
    private function signup(): void
    {
        $this->userDao->create($this->useCaseInput->name()->value(), $this->useCaseInput->email()->value(), $this->useCaseInput->password()->value());
    }
}
