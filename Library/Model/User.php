<?php
/**
 * KK Forum
 * A simple bulletin board system
 * Author: kookxiang <r18@ikk.me>
 */
namespace Model;

use Core\Database;
use Helper\Encrypt;

class User
{
    const ENCRYPT_TYPE_DEFAULT = 0;
    const ENCRYPT_TYPE_ENHANCE = 1;

    public $id;
    public $email;
    public $nickname;
    private $password = 'FAKE-PASSWORD';
    private static $instance;

    /**
     * Get current user object
     * @return User
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $cookie = Encrypt::decode($_COOKIE['auth'], COOKIE_KEY);
        if ($cookie) {
            list($this->id, $this->email, $this->nickname) = explode("\t", $cookie);
        }
    }

    /**
     * Get a user by email
     * @param $email string Email address
     * @return User
     */
    public static function GetUserByEmail($email)
    {
        $statement = Database::prepare("SELECT * FROM member WHERE email=?");
        $statement->bindValue(1, $email);
        $statement->execute();
        $statement->setFetchMode(\PDO::FETCH_CLASS, '\\Model\\User');
        return $statement->fetch(\PDO::FETCH_CLASS);
    }

    /**
     * Get a user by UserId
     * @param $userId int UserID
     * @return User
     */
    public static function GetUserByUserId($userId)
    {
        $statement = Database::prepare("SELECT * FROM member WHERE id=?");
        $statement->bindValue(1, $userId, \PDO::PARAM_INT);
        $statement->execute();
        $statement->setFetchMode(\PDO::FETCH_CLASS, '\\Model\\User');
        return $statement->fetch(\PDO::FETCH_CLASS);
    }

    /**
     * Insert current user into database
     * @return int Auto-generated UserID for this user
     */
    public function insertToDB()
    {
        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }
        $statement = Database::prepare("INSERT INTO member SET email=:email, `password`=:pwd, nickname=:nickname");
        $statement->bindValue(':email', $this->email, \PDO::PARAM_STR);
        $statement->bindValue(':pwd', $this->password, \PDO::PARAM_STR);
        $statement->bindValue(':nickname', $this->nickname, \PDO::PARAM_STR);
        $statement->execute();
        $this->id = Database::lastInsertId();
        if (!$inTransaction) {
            Database::commit();
        }
        return $this->id;
    }

    /**
     * Verify whether the given password is correct
     * @param string $password Password needs to verify
     * @return bool Whether the password is correct
     */
    public function verifyPassword($password)
    {
        list($hashedPassword, $encryptType) = explode('T', $this->password);
        if ($encryptType == self::ENCRYPT_TYPE_DEFAULT) {
            return $hashedPassword == md5(ENCRYPT_KEY . md5($password) . ENCRYPT_KEY);
        } elseif ($encryptType == self::ENCRYPT_TYPE_ENHANCE) {
            $salt = substr(md5($this->id . $this->email . ENCRYPT_KEY), 8, 16);
            return $hashedPassword == substr(md5(md5($password) . $salt), 0, 30);
        }
        return false;
    }

    /**
     * Save new password
     * @param string $password New password
     */
    public function savePassword($password)
    {
        $salt = substr(md5($this->id . $this->email . ENCRYPT_KEY), 8, 16);
        $this->password = substr(md5(md5($password) . $salt), 0, 30) . 'T' . self::ENCRYPT_TYPE_ENHANCE;
        $inTransaction = Database::inTransaction();
        if (!$inTransaction) {
            Database::beginTransaction();
        }
        $statement = Database::prepare("UPDATE member SET `password`=:pwd WHERE id=:userId");
        $statement->bindValue(':pwd', $this->password, \PDO::PARAM_STR);
        $statement->bindValue(':userId', $this->id, \PDO::PARAM_INT);
        $statement->execute();
        if (!$inTransaction) {
            Database::commit();
        }
    }
}
