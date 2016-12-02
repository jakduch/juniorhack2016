<?php
namespace App\Model;
use Nette\Database\Context;
use Nette\Security\IAuthorizator;
use Nette\Security\Permission;

class UserAuthorizator extends Permission
{
	public function __construct(Context $database)
	{
		$this->addRole("Uživatel");
		$this->addRole("Administrátor", "Uživatel");

		$this->addResource("Homepage");
		$this->addResource("Buying");
		$this->addResource("Credit");
		$this->addResource("Settings");

		$this->allow("Uživatel", "Homepage", "default");
		$this->allow("Uživatel", "Settings", array("headings", "changeAccount"));

		$this->allow("Administrátor", Permission::ALL, Permission::ALL); //HOTOVO
	}


}