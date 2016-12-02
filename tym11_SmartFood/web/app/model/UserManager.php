<?php
namespace App\Model;
use Nette\Application\BadRequestException;
use Nette\Mail\Message;
use Nette\Mail\SendmailMailer;
use Nette\Neon\Exception;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;

class UserManager extends BaseManager
{
	const
		TABLE_NAME = "users",
		TABLE_GROUPS = "groups",
		COLUMN_ID = "id",
		COLUMN_EMAIL = "email",
		COLUMN_PASSWORD = "password",
		COLUMN_SALT = "salt",
		COLUMN_GROUP_NAME = "group_id",
		COLUMN_FIRSTNAME = "firstname",
		COLUMN_LASTNAME = "lastname",
		COLUMN_IMAGE = "user_image_name",
		COLUMN_CREDIT = "credit";

	const DEFAULT_PASSWORD_LENGTH = 5,
		SALT_LENGTH = 16;

	/**
	 * @var Model pro logování
	 */
	protected $creditLogManager;

	public function setInjections(CreditLogManager $creditLogManager)
	{
		$this->creditLogManager = $creditLogManager;
	}

	public function register($values)
	{
		$filename = "default-avatar.png";
		if ($values->image->isOk())
		{
			$isUpload = true;
			$filename = $values->image->getSanitizedName();
			$parts = explode(".", $filename);
			$parts[0] = hash('md5', $parts[0] . $parts[1] . time());
			$filename = $parts[0] . "." . $parts[1];
			$this->imageStorage->save($filename, $values->image->getTemporaryFile());
		}

		$password = $this->generateString(self::DEFAULT_PASSWORD_LENGTH);
		$salt = $this->generateString(self::SALT_LENGTH);
		$hashedPass = $this->hash($password, $salt);

		$this->database->query("INSERT into `" . self::TABLE_NAME . "`", array(
			self::COLUMN_EMAIL => $values->email,
			self::COLUMN_PASSWORD => $hashedPass,
			self::COLUMN_SALT => $salt,
			self::COLUMN_FIRSTNAME => $values->firstname,
			self::COLUMN_LASTNAME => $values->lastname,
			self::COLUMN_GROUP_NAME => $values->group_id,
			self::COLUMN_IMAGE => $filename
		));


		$mail = new Message();
		$mail->setFrom('Automat <noreply@automat.cz>')
			->addTo($values->email)
			->setSubject('Registrace | Automat')
			->setHtmlBody("Dobrý den, <br />byl vám vytvořen účet spolu s vygenerovaným heslem. Přihlaste se na webu (doporučujeme změnit heslo):
			<br /> <br /> <br />
			Email: $values->email <br />
			Heslo: $password");


		$mailer = new SendmailMailer();
		$mailer->send($mail);
	}

	public function generateNewPassword($userId)
	{
		$password = $this->generateString(5);
		$salt = $this->generateString(16);
		$hashedPass = $this->hash($password, $salt);

		$data[self::COLUMN_PASSWORD] = $hashedPass;
		$data[self::COLUMN_SALT] = $salt;

		$this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_ID, $userId)
			->update($data);

		$user = $this->getUserById($userId);

		$mail = new Message();
		$mail->setFrom('Automat <noreply@Automat.com>')
			->addTo($user->email)
			->setSubject('Registrace | Automat')
			->setHtmlBody("Dobrý den, <br />bylo vám vyresetováno heslo. Přihlaste se na webu (doporučujeme heslo změnit):
			<br /> <br /> <br />
			Email: $user->email <br />
			Heslo: $password");


		$mailer = new SendmailMailer();
		$mailer->send($mail);
	}


	private function hash($password, $salt)
	{
		return hash('sha512', $password . $salt);
	}

	private function generateString($length)
	{
		$chars = "abcdefghijklmnopqrstuvwxyz1234567890";
		$chars_length = strlen($chars) - 1;
		$string = "";
		for ($i = 0; $i < $length; $i++) {
			$string .= $chars[mt_rand(0, $chars_length)];
		}

		return $string;

	}


	/**
	 * Kontrola duplicity E-mailu
	 * @return TRUE - existuje záznam | FALSE - záznam je unikátní
	 */
	public function validateEmail($email)
	{
		$row = $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_EMAIL, $email)
			->fetch();
		return $row;
	}

	/**
	 * Kontrola duplicity přezdívky
	 * @return TRUE - existuje záznam | FALSE - záznam je unikátní
	 */

	public function getAllUsers()
	{
		$users = $this->database->table(self::TABLE_NAME);
		return $users;
	}

	/***
	 * @param $id ID uživatele
	 * @return bool|mixed|\Nette\Database\Table\IRow
	 * @throws BadRequestException
	 * Získá uživatele podle ID
	 */
	public function getUserById($id)
	{
		$user = $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_ID, $id)
			->fetch();
		if(!$user)
			throw new UserNoExist;
		return $user;
	}

	public function getUserByEmail($email)
	{
		$user = $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_EMAIL, $email)
			->fetch();
		return $user;
	}

	/**
	 * @param $userId
	 * @return mixed - vrací roly uživatele
	 */
	public function getUserRole($userId)
	{
		$user =  $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_ID, $userId)
			->fetch();
		return $user->ref(self::COLUMN_GROUP_NAME)->name;
	}

	/**
	 * Změní heslo uživatele
	 * @param $values - hodnoty z formuláře
	 * @param $user - uživatel
	 */
	public function changePassword($values, $user)
	{
		$salt = $this->generateString(16);
		$newPassword = $this->hash($values->newPassword, $salt);

		$array = array(self::COLUMN_PASSWORD => $newPassword, self::COLUMN_SALT => $salt);
		$this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_ID, $user->id)
			->update($array);
	}

	public function checkPassword($oldPassword, $user)
	{
		$row = $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_ID, $user->id)
			->fetch();
		if($row->{self::COLUMN_PASSWORD} != hash('sha512', $oldPassword . $row->{self::COLUMN_SALT}))
			return false;

		return true;
	}

	public function getGroupList()
	{
		return $this->database->table(self::TABLE_GROUPS)
			->order(self::COLUMN_ID . " ASC")->fetchPairs('id', 'name');
	}

	/**
	 * @param $values - hodnoty z formuláře
	 * @throws \IDCantBeYourself - V případě totožného ID Inendeence a ID upraveného uživatele
	 */
	public function updateUser($values)
	{
		$isUpload = false;
		if ($values->image->isOk())
		{
			$isUpload = true;
			$filename = $values->image->getSanitizedName();
			$parts = explode(".", $filename);
			$parts[0] = hash('md5', $parts[0] . $parts[1] . time());
			$filename = $parts[0] . "." . $parts[1];
			$this->imageStorage->save($filename, $values->image->getTemporaryFile());
		}

		$data = array(self::COLUMN_FIRSTNAME => $values->firstname,
			self::COLUMN_LASTNAME => $values->lastname,
			self::COLUMN_GROUP_NAME => $values->group_id);
		if($isUpload) {
			$data[self::COLUMN_IMAGE] = $filename;
		}

		$this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_ID, $values->id)
			->update($data);

	}

	/**
	 * Odstranění uživatele
	 * @param $id - ID uživatele
	 */
	public function removeUser($id)
	{
		$this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_ID, $id)
			->delete();
	}

	public function getAllUsersAsArray()
	{
		$result = $this->database->table(self::TABLE_NAME)
			->order(self::COLUMN_LASTNAME . " ASC");
		$users = array();
		foreach($result as $r)
		{
			$users[$r[self::COLUMN_ID]] = $r[self::COLUMN_FIRSTNAME] . " " . $r[self::COLUMN_LASTNAME];
		}
		return $users;
	}

	public function changeAvatar($values, $userId)
	{

		if ($values->image->isOk())
		{
			$isUpload = true;
			$filename = $values->image->getSanitizedName();
			$parts = explode(".", $filename);
			$parts[0] = hash('md5', $parts[0] . $parts[1] . time());
			$filename = $parts[0] . "." . $parts[1];
			$this->imageStorage->save($filename, $values->image->getTemporaryFile());
		}

		if($isUpload)
		{
			$data[self::COLUMN_IMAGE] = $filename;
		}

		$this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_ID, $userId)
			->update($data);
	}

	/**
	 * @param $id - Uživatelské ID
	 * @return mixed - Vrací ID skupiny uživatele
	 */
	public function getGroupByUser($id)
	{
		return $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_ID, $id)
			->fetch()
			->{self::COLUMN_GROUP_NAME};
	}

	public function getCreditByUserId($id)
	{
		$result = $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_ID, $id)
			->fetch();

		return $result->credit;
	}

	public function setCreditByUserId($id, $newValue)
	{
		$this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_ID, $id)
			->update(array(self::COLUMN_CREDIT => $newValue));
	}

	public function addCredit($id, $credit)
	{
		$lastValue = $this->getCreditByUserId($id);
		$newValue = $lastValue + $credit;
		$this->setCreditByUserId($id, $newValue);
		$this->creditLogManager->logSomething("Kredity přidány (+$credit)", $newValue, $id);
	}

}


/**
 * Class UserNoExist
 * Vyjímka ošetřující stav, kdy uživatel neexistuje
 */
class UserNoExist extends Exception
{
	protected $message = "Uživatel neexistuje";
}