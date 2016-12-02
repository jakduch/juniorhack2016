<?php
namespace App\CoreModule\Presenters;
class HomepagePresenter extends BasePresenter
{
	/** Seznam modelů */
	protected $managerList = array();

	/**
	 * Vykreslení hlavní stránky administrace
	 */
	public function renderDefault()
	{
		$this->template->groupName = $this->user->getRoles()[0];
	}
}