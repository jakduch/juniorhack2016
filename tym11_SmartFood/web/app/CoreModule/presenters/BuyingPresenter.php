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
	protected $managerList = array('automat', 'product', 'user', 'orders', 'orderItems', 'itemStorage');

	/**
	 * @var Instance manageru pro práci s AutomatManagerem
	 */
	protected $automatManager;

	/**
	 * @var Instance manageru pro práci s ProductManagerem
	 */
	protected $productManager;

	/**
	 * @var Instance manageru pro práci s UserManagerem
	 */
	protected $userManager;

	/**
	 * @var Instance manageru pro práci s OrdersManagerem
	 */
	protected $ordersManager;

	/**
	 * @var Instance manageru pro práci s managerem pro ukládání sklad. zásob
	 */
	protected $itemStorageManager;

	/** Instance manageru pro práci s produktama z nabídky*/
	protected $orderItemsManager;

	/** Seznam automatů */
	protected $automatList;

	/** Seznam produktů */
	protected $productList;

	/** Seznam cen za kus (index = ID produktu) */
	protected $pricesList;

	public function actionBuy()
	{
		//Načtení všech seznamů
		$this->automatList = $this->automatManager->getForArray();
		$this->productList = $this->productManager->getForArray();
		$this->pricesList = $this->productManager->getPrices();
		$this->template->prices = $this->pricesList;
	}

	protected function createComponentBuyForm()
	{
		$form = new Form();
		$form->getElementPrototype()->addAttributes(array('class' => 'form-horizontal'));

		//Vytvoří list automatů
		$form->addSelect('automat', "Automat: ", $this->automatList);
		$form['automat']->getControlPrototype()->addAttributes(array('class' => 'form-control'));
		$form['automat']->getLabelPrototype()->addAttributes(array('class' => 'control-label col-sm-offset-1 col-sm-2 col-xs-12'));

		//Vytvoření items containeru pro práci s jednotlivými položkami
		$itemsContainer = $form->addContainer('items_container');

		foreach ($this->productList as $id => $product) {
			//Jméno je zároveň ID, Nastaví se html5 na number a jako data-id se uloží aktuální ID (kvůli JS)
			$name = $id;
			$itemsContainer->addText($name, $product)->setType('number')->setDefaultValue(0);
			$itemsContainer[$name]->getControlPrototype()->addAttributes(array('data-id' => $id));
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
		//spočítá celkovou cenu a ověří zda-li na to uživatel má
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
			//Vložení objednávky do databáze, zvlášť obecné info o objednávce a o itemech pro danou objednávku
			$lastId = $this->ordersManager->insert($this->user->id, $priceTotal, $values->automat);
			$this->itemStorageManager->sellProducts($values->items_container, $values->automat);
			foreach($values->items_container as $id => $item_count)
			{
				$this->orderItemsManager->insert($lastId, $id, $item_count, $item_count * $priceList[$id]);
			}
			//Odečtení kreditů
			$this->userManager->removeCredit($this->user->id, $priceTotal);
			$this->flashMessage("Objednávka byla úspěšně vytvořena!");
			$this->redirect("Buying:myBuys");
		}
	}
	
	public function actionMyBuys()
	{
		$orders = $this->ordersManager->getByUser($this->user->id);
		$this->template->orders = $orders;
	}

	/**
	 * Handle který zpracovává AJAX request o aktuální stav skladu
	 * @param $automatId - ID automatu
	 */
	public function handleGetAmounts($automatId)
	{
		//načte info a odesílá prostřednictví PayLoadu
		$values = $this->itemStorageManager->getDefaults($automatId, $this->productList);
		$this->payload->data = $values;
		$this->sendPayload();
	}


}
