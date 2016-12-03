<?php
namespace App\CoreModule\Presenters;
use Nette\Application\UI\Form;
use Nette\Application\UI\Presenter;

/**
 * Created by PhpStorm.
 * User: Michael Kufner
 * Date: 02.12.2016
 * Time: 18:55
 */

class BuyingPresenter extends BasePresenter
{
	const MSG_REQ = "Toto pole je povinné";

	/** Seznam modelů */
	protected $managerList = array('automat', 'product', 'user', 'orders', 'orderItems');

	protected $automatManager;

	protected $productManager;

	protected $userManager;

	protected $ordersManager;

	protected $orderItemsManager;

	/** Seznam automatů */
	protected $automatList;

	/** Seznam produktů */
	protected $productList;

	/** Seznam cen za kus (index = ID produktu) */
	protected $pricesList;

	public function actionBuy()
	{
		$this->automatList = $this->automatManager->getForArray();
		$this->productList = $this->productManager->getForArray();
		$this->pricesList = $this->productManager->getPrices();
		$this->template->prices = $this->pricesList;
	}

	protected function createComponentBuyForm()
	{
		$form = new Form();
		$form->getElementPrototype()->addAttributes(array('class' => 'form-horizontal'));

		$form->addSelect('automat', "Automat: ", $this->automatList);
		$form['automat']->getControlPrototype()->addAttributes(array('class' => 'form-control'));
		$form['automat']->getLabelPrototype()->addAttributes(array('class' => 'control-label col-sm-offset-1 col-sm-2 col-xs-12'));

		$potentialContainer = $form->addContainer('items_container');


		foreach ($this->productList as $id => $product) {
			$name = $id;
			$potentialContainer->addText($name, $product)->setType('number')->setDefaultValue(0);
			$potentialContainer[$name]->getControlPrototype()->addAttributes(array('data-id' => $id));
		}

		$form->onSuccess[] = [$this, 'buyFormSucceeded'];
		$form->addSubmit('submit', "Vytvořit a zaplatit (automaticky bude odečteno)");

		return $form;
	}

	public function buyFormSucceeded($form, $values)
	{
		$credits = $this->userManager->getCreditByUserId($this->user->id);
		$priceList = $this->pricesList;
		$priceTotal = 0;
		foreach($values->items_container as $id => $item_count)
		{
			$priceTotal += $item_count * $priceList[$id];
		}
		if($priceTotal > $credits)
		{
			$this->flashMessage("Nedostatek kreditů!");
		}
		else
		{
			$lastId = $this->ordersManager->insert($this->user->id, $priceTotal, $values->automat);
			foreach($values->items_container as $id => $item_count)
			{
				$this->orderItemsManager->insert($lastId, $id, $item_count, $item_count * $priceList[$id]);
			}
			$this->userManager->removeCredit($this->user->id, $priceTotal);
		}
	}


}
