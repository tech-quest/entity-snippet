<?php
namespace App\Domain\ValueObject\User;
/**
 * ユーザーIDのValueObject
 */
final class UserId
{
    const MIN_VALUE = 1;
    const INVALID_MESSAGE = '不正な値です';

    /**
     * @var int
     */
    private $value;

    /**
     * コンストラクタ
     * 
     * @param int $value
     */
    public function __construct(int $value)
    {
        if ($this->isInvalid($value)) {
            throw new Exception(self::INVALID_MESSAGE);
        }
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function value(): int
    {
        return $this->value;
    }

    /**
     * 1以上かどうかを判定する
     *
     * @param string $value
     * @return boolean
     */
    private function isInvalid(int $value): bool
    {
        return $value < self::MIN_VALUE;
    }
}