<?php
namespace App\CoreModule\Controls;

use App\Model\CategoryManager;
use App\Model\CorePagesManager;
use Nette\Application\UI\Control;
use Nette\Http\Context;
use Nette\Security\User;

final class LeftsideNavigation extends Control
{
	/** @var Instance pro práci s corePagesManager*/
	protected $corePagesManager;

	/** @var Instance aktuálního uživatele*/
	protected $user;

	public function __construct(CorePagesManager $corePagesManager, User $user)
	{
		$this->corePagesManager = $corePagesManager;
		$this->user = $user;
	}

	public function render()
	{
		$menu = $this->corePagesManager->createMenu();
		$this->template->headings = $menu["headings"];
		$this->template->categories = $menu["categories"];
		$this->template->user = $this->user;
		$template = $this->template;
		$template->setFile(__DIR__ . "/leftsideNavigationControl.latte");
		$template->render();
	}
}

interface ILeftsideNavigationFactory
{
	/**
	 * @return LeftsideNavigation
	 */
	function create();
}