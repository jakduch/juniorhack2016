<?php
namespace App\Model;
use Nette\Database\Context;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\Permission;

class UserAuthenticator extends BaseManager implements IAuthenticator
{
	const
		TABLE_NAME = "users",
		COLUMN_ID = "id",
		COLUMN_EMAIL = "email",
		COLUMN_PASSWORD = "password",
		COLUMN_SALT = "salt",
		COLUMN_GROUP_ID = "group_id";

	/**
	 * @var Instance databáze
	 */
	protected $database;

	/**
	 * @var Instance UserManager
	 */
	protected $userManager;

	public function __construct(Context $database, UserManager $userManager)
	{
		$this->database = $database;
		$this->userManager = $userManager;
	}

	/**
	 * @param array $credentials - údaje užvatele
	 * @return Identity - Identita uživatele
	 * @throws AuthenticationException - Při špatném jméně nebo hesle
	 */
	public function authenticate(array $credentials)
	{
		list($email, $password) = $credentials;
		$row = $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_EMAIL, $email)->fetch();
		if(!$row)
			throw new AuthenticationException("Uživatel neexistuje");
		if($row->{self::COLUMN_PASSWORD} != hash('sha512', $password . $row->{self::COLUMN_SALT}))
			throw new AuthenticationException("Špatné údaje");

		$array = $row->toArray();
		unset($array['password']);
		$array['group_name'] = $row->ref(self::COLUMN_GROUP_ID)->name;


		return new Identity($row->{self::COLUMN_ID}, $row->ref(self::COLUMN_GROUP_ID)->name, $array );
	}

	public function refreshLogin($user)
	{
		$user->getIdentity()->setRoles(array($this->userManager->getUserRole($user->id)));
	}

}