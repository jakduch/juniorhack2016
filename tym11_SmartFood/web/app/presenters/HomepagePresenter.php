<?php
namespace App\Presenters;
use App\Model\UserAuthenticator;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;
use Nette\Security\AuthenticationException;


class HomepagePresenter extends Presenter
{
	public function renderLogin()
	{
		
	}

	protected function createComponentLoginForm()
	{
		$form = new Form();
		$form->addText("email", "Uživatel: ")
			->getControlPrototype()->addAttributes(array('class' => 'form-control'));;
		$form->addPassword("password", "Heslo: ")
			->getControlPrototype()->addAttributes(array('class' => 'form-control'));;

		$form->addSubmit("submit", "Přihlásit se");
		$form->onSuccess[] = [$this, 'loginFormSucceeded'];
		return $form;
	}

	public function loginFormSucceeded($form, $values)
	{
		$user = $this->getUser();
		$authenticator = new UserAuthenticator($this->context->getService('database.default.context'), $this->context->getService('userManager'));
		$user->setAuthenticator($authenticator);

		try
		{
			$user->login($values->email, $values->password);
		}
		catch(AuthenticationException $e)
		{
			$this->flashMessage("Chyba: " . $e->getMessage(), 'error');
			$this->redirect('Homepage:login');
		}

		$this->redirect(':Core:Homepage:default');
	}
}