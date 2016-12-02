<?php
namespace App\CoreModule\Presenters;

use Nette\Application\BadRequestException;
use Nette\Application\UI\Form;

class SettingsPresenter extends BasePresenter
{
	const MSG_REQ = "Toto pole je povinné";
	/** Seznam modelů */
	protected $managerList = array('user');

	/** Instance pro práci s modelem uživatelů */
	protected $userManager;

	/** Instance pro práci s modelem zákazníků*/
	protected $customersManager;

	/** Instance upravovaného uživatele */
	protected $editedUser;

	public function renderUsers()
	{
		$this->template->users = $this->userManager->getAllUsers();
	}


	/**
	 * @return Form - formulář pro vytvoření uživatele
	 */
	protected function createComponentUserCreateForm()
	{
		$form = new Form();
		$form->getElementPrototype()->addAttributes(array('class' => 'form-horizontal'));

		$form->addText('firstname', "Jméno: ");
		$form['firstname']->getControlPrototype()->addAttributes(array('class' => 'form-control'));
		$form['firstname']->getLabelPrototype()->addAttributes(array('class' => 'control-label col-sm-offset-1 col-sm-2 col-xs-12'));

		$form->addText('lastname', "Příjmení: ");
		$form['lastname']->getControlPrototype()->addAttributes(array('class' => 'form-control'));
		$form['lastname']->getLabelPrototype()->addAttributes(array('class' => 'control-label col-sm-offset-1 col-sm-2 col-xs-12'));

		$form->addText('email', 'Email:')->setType('email')->addRule(Form::EMAIL, "Zadání není platná e-mailová adresa")->addRule(Form::MAX_LENGTH, "Maximální délka je %d znaků", 50)->setRequired(self::MSG_REQ);
		$form['email']->getControlPrototype()->addAttributes(array('class' => 'form-control'));
		$form['email']->getLabelPrototype()->addAttributes(array('class' => 'control-label col-sm-3 col-xs-12'));

		$form->addSelect('group_id', "Skupina: ", $this->userManager->getGroupList());
		$form['group_id']->getControlPrototype()->addAttributes(array('class' => 'form-control'));
		$form['group_id']->getLabelPrototype()->addAttributes(array('class' => 'control-label col-sm-offset-1 col-sm-2 col-xs-12'));

		$form->addUpload('image', "Náhledový obrázek (nepovinné): ")->addCondition(Form::FILLED)->addRule(Form::IMAGE, "Soubor může být pouze obrázek");
		$form['image']->getControlPrototype()->addAttributes(array('class' => 'article-upload-button'));
		$form['image']->getLabelPrototype()->addAttributes(array('class' => 'control-label col-sm-3 col-xs-12'));

		$form->addHidden('id');

		$form->onSuccess[] = [$this, 'userCreateFormSucceeded'];
		$form->addSubmit('submit', "Vytvořit uživatele");

		return $form;
	}

	/**
	 * Metoda se volá při úspěšném odeslání formuláře pro vytvoření uživatele
	 * @param $form - formulář
	 * @param $values - hodnoty
	 */
	public function userCreateFormSucceeded($form, $values)
	{
		if($this->userManager->getUserByEmail($values->email) == false)
		{
			$this->userManager->register($values);
			$this->flashMessage("Uživatel byl vytvořen!");
			$this->redirect("Settings:users");
		}
		else
		{
			$this->flashMessage("Duplicita e-mailové adresy!");
		}

	}

	/**
	 * Akce reprezentující editaci uživatele
	 * @param $id - ID uživatele
	 */
	public function actionUserEdit($id)
	{
		try {
			$this->editedUser = $this->userManager->getUserById($id);
		} catch (BadRequestException $e) {
			$this->flashMessage("Tento uživatel neexistuje!");
			$this->redirect("Settings:users");
		}
		$this->template->userImage = $this->editedUser->user_image_name;
		$this->template->id = $this->editedUser->id;
	}

	/**
	 * @return Form - formulář pro editaci uživatele
	 */
	protected function createComponentUserEditForm()
	{
		$editedUser = $this->editedUser;

		$form = new Form();
		$form->getElementPrototype()->addAttributes(array('class' => 'form-horizontal'));

		$form->addText('firstname', "Jméno: ")->setDefaultValue($editedUser->firstname);
		$form['firstname']->getControlPrototype()->addAttributes(array('class' => 'form-control'));
		$form['firstname']->getLabelPrototype()->addAttributes(array('class' => 'control-label col-sm-offset-1 col-sm-2 col-xs-12'));

		$form->addText('lastname', "Příjmení: ")->setDefaultValue($editedUser->lastname);
		$form['lastname']->getControlPrototype()->addAttributes(array('class' => 'form-control'));
		$form['lastname']->getLabelPrototype()->addAttributes(array('class' => 'control-label col-sm-offset-1 col-sm-2 col-xs-12'));

		$form->addSelect('group_id', "Skupina: ", $this->userManager->getGroupList())->setDefaultValue($editedUser->group_id);
		$form['group_id']->getControlPrototype()->addAttributes(array('class' => 'form-control'));
		$form['group_id']->getLabelPrototype()->addAttributes(array('class' => 'control-label col-sm-offset-1 col-sm-2 col-xs-12'));

		$form->addUpload('image', "Náhledový obrázek (nepovinné): ")->addCondition(Form::FILLED)->addRule(Form::IMAGE, "Soubor může být pouze obrázek");
		$form['image']->getControlPrototype()->addAttributes(array('class' => 'article-upload-button'));
		$form['image']->getLabelPrototype()->addAttributes(array('class' => 'control-label col-sm-3'));

		$form->addHidden('id')->setDefaultValue($editedUser->id);

		$form->onSuccess[] = [$this, 'userEditFormSucceeded'];
		$form->addSubmit('submit', "Uložit změny");

		return $form;
	}


	/**
	 * Metoda se volá při úspěšném odeslání formuláře pro editaci uživatele
	 * @param $form - formulář
	 * @param $values - hodnoty
	 */
	public function userEditFormSucceeded($form, $values)
	{

		/*Pokud se mění role související se zákazníky (Reg. vedoucí, OZ, ID), kontroluj ve správných případech jestli
			k ním není přiřazený nějaký zákazník */


		$this->userManager->updateUser($values);
		$this->flashMessage("Změny byly uloženy");
	}


	/**
	 * @return Form - Formulář pro změnu hesla uživatele
	 */
	protected function createComponentChangePasswordForm()
	{
		$form = new Form();
		$form->getElementPrototype()->addAttributes(array('class' => 'form-horizontal'));

		$form->addPassword('oldPassword', "Staré heslo: ")->setRequired(self::MSG_REQ);
		$form['oldPassword']->getControlPrototype()->addAttributes(array('class' => 'form-control'));
		$form['oldPassword']->getLabelPrototype()->addAttributes(array('class' => 'control-label col-sm-offset-1 col-sm-2 col-xs-12'));

		$form->addPassword('newPassword', "Nové heslo: ")->addRule(Form::MIN_LENGTH, "Heslo musí mít alespoň %d znaků", 5)->setRequired(self::MSG_REQ);
		$form['newPassword']->getControlPrototype()->addAttributes(array('class' => 'form-control'));
		$form['newPassword']->getLabelPrototype()->addAttributes(array('class' => 'control-label col-sm-offset-1 col-sm-2 col-xs-12'));

		$form->addPassword('newPassword_again', "Nové heslo (znovu): ")->addRule(Form::EQUAL, "Hesla se neshodují", $form['newPassword'])->setRequired(self::MSG_REQ);
		$form['newPassword_again']->getControlPrototype()->addAttributes(array('class' => 'form-control'));
		$form['newPassword_again']->getLabelPrototype()->addAttributes(array('class' => 'control-label col-sm-offset-1 col-sm-2 col-xs-12'));

		$form->onSuccess[] = [$this, 'changePasswordFormSucceeded'];
		$form->addSubmit('submit', "Změnit heslo");

		$form->onValidate[] = function($form) {
			$userManager = $this->userManager;
			if (!$userManager->checkPassword($form->values->oldPassword, $this->getUser())) {
				$form->addError("Špatné heslo!");
			}
		};

		return $form;
	}


	/**
	 * Metoda se volá při úspěšném odeslání formuláře pro změnu uživatelského hesla
	 * @param $form - formulář
	 * @param $values - hodnoty
	 */
	public function changePasswordFormSucceeded($form, $values)
	{
		$this->userManager->changePassword($values, $this->getUser());
		$this->getuser()->logout();
		$this->flashMessage("Vaše heslo bylo změněno, přihlaste se prosím!");
		$this->redirect(":Homepage:login");
	}

	/**
	 * @return Form - formulář pro změnu avataru
	 */
	protected function createComponentChangeAvatarForm()
	{
		$form = new Form();
		$form->getElementPrototype()->addAttributes(array('class' => 'form-horizontal'));

		$form->addUpload('image', "Obrázek: ")->setRequired(self::MSG_REQ)->addRule(Form::IMAGE, "Soubor musí být obrázek!");
		$form['image']->getControlPrototype()->addAttributes(array('class' => 'form-control'));
		$form['image']->getLabelPrototype()->addAttributes(array('class' => 'control-label col-sm-offset-1 col-sm-2 col-xs-12'));

		$form->onSuccess[] = [$this, 'changeAvatarFormSucceeded'];
		$form->addSubmit('submit', "Změnit avatar");

		return $form;
	}

	/**
	 * Metoda se volá při úspěšném odeslání formuláře pro změnu uživatelského avataru
	 * @param $form - formulář
	 * @param $values - hodnoty
	 */
	public function changeAvatarFormSucceeded($form, $values)
	{
		$this->userManager->changeAvatar($values, $this->getUser()->id);
		$this->flashMessage("Obrázek změněn! Přihlašte se znovu!");
		$this->redirect("signOut!");
	}

	/**
	 * Handle zachycující odstranění uživatele
	 * @param $id - (int) ID uživatele
	 */
	public function handleRemoveUser($id)
	{
		$this->userManager->removeUser($id);
		$this->flashMessage("Uživatel odstraněn!");
		$this->redirect("Settings:users");
	}

	public function handleResetPassword($id)
	{
		if(!$this->user->isAllowed($this->getPresenterName(), "editAll"))
		{
			$this->flashMessage("Chyba oprávnění!");
			$this->redirect("Homepage:default");
		}
		$this->userManager->generateNewPassword($id);
		$this->flashMessage("Heslo bylo vyresetováno, email odeslán!");
	}
}