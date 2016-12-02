<?php
namespace App\CoreModule\Presenters;
use App\Model\UserAuthenticator;
use Nette\Application\UI\Presenter;

class BasePresenter extends Presenter
{
	/**Instance uživatele */
	protected $user;

	/** @var  Továrna na levou navigaci */
	protected $ILeftSideNavigationFactory;


	public function startup()
	{
		parent::startup();
		$this->user = $this->getUser();
		//Kontrola přihlášení
		if(!$this->user->isLoggedIn())
		{
			$this->redirect(":Homepage:login");
		}
		//Automatická kontrola oprávnění
		if(!$this->user->isAllowed($this->getPresenterName(), $this->getAction()))
		{
			$this->flashMessage("Chyba oprávnění");
			$this->redirect("Homepage:default");
		}

		//Automatický DI managerů v presenterech
		foreach($this->managerList as $name)
		{
			$this->{$name . "Manager"} = $this->context->getService($name . "Manager");
		}
		//Vytvoření navigace
		$this->ILeftSideNavigationFactory = $this->context->getService('leftSideNavigationFactory');

	}

	/**
	 * Handle zachycující odhlášení
	 */
	public function handleSignOut()
	{
		$this->getUser()->logout();
		$this->flashMessage("Byl jste úspěšně odhlášen!");
		$this->redirect(':Homepage:login');
	}

	/**
	 * Handle zaktualizuje SESSION (načte nové skupiny, oprávnění apod.)
	 */
	public function handleRefresh()
	{
		$authenticator = new UserAuthenticator($this->context->getService('database.default.context'), $this->context->getService('userManager'));
		$authenticator->refreshLogin($this->getUser());
		$this->flashMessage("Znovunačtení přihlášení proběhlo!");
		$this->redirect("Homepage:default");
	}

	protected function createComponentLeftsideNavigation()
	{
		return $this->ILeftSideNavigationFactory->create();
	}

	public function getPresenterName()
	{
		$pos = strrpos($this->name, ':');
		if (is_int($pos)) {
			return substr($this->name, $pos + 1);
		}

		return $this->name;
	}
}