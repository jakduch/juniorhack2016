<?php
/**
 * Created by PhpStorm.
 * User: Michael Kufner
 * Date: 02.12.2016
 * Time: 15:57
 */
namespace App\CoreModule\Presenters;

use Nette\Application\UI\Form;

class CreditPresenter extends BasePresenter
{
	/** Seznam modelů */
	protected $managerList = array("user", "creditLog");

	/**
	 * Model pro práci s uživateli
	 */

	protected $userManager;

	protected $creditLogManager;

	public function actionBuyCredit()
	{
		//user je na $this->user


	}

	protected function createComponentCreditAddForm()
	{
		$form = new Form();

		$form->addText('credit_count', "Počet kreditů: ")
			->setType('number');
		$form['credit_count']->getControlPrototype()->addAttributes(array('class' => 'form-control'));
		$form['credit_count']->getLabelPrototype()->addAttributes(array('class' => 'control-label col-sm-offset-1 col-sm-2 col-xs-12'));

		$form->addHidden('id')
		->setValue($this->user->id);

		$form->onSuccess[] = [$this, 'creditAddFormSucceeded'];
		$form->addSubmit('submit', "Koupit kredity");

		return $form;
	}

	public function creditAddFormSucceeded($form, $values)
	{
		$this->userManager->addCredit($values->id, $values->credit_count);
		$this->flashMessage("Kredity byli přidány!");
		$this->redirect("Credit:buyCredit");
	}

	public function actionCreditHistory()
	{
		$this->template->creditHistory = $this->creditLogManager->getLogsOfUser($this->user->id);

	}
}
