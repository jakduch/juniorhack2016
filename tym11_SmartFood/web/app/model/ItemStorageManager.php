<?php
namespace App\Model;
/**
 * Created by PhpStorm.
 * User: Michael Kufner
 * Date: 03.12.2016
 * Time: 6:33
 */

class ItemStorageManager extends BaseManager
{
	const
		TABLE_NAME = "item_storage",
		COLUMN_ID = "id",
		COLUMN_PRODUCT_ID = "product_id",
		COLUMN_AUTOMAT_ID = "automat_id",
		COLUMN_AMOUNT = "amount";

	/**
	 * Updatuje sklad. zásoby
	 * @param $values
	 */
	public function updateStorage($values)
	{
		$automatId = $values->id;

		foreach($values->items_container as $itemId => $amount)
		{
			$result = $this->getAmountByIdAndAutomat($itemId, $automatId);
			if($result == true)
			{
				$this->updateAmount($automatId, $itemId, $amount);
			}
			else
			{
				$this->insertAmount($automatId, $itemId, $amount);
			}
		}

	}

	/**
	 * Získá hodnotu skladové zásoby pro kombinaci automatu a produktu
	 * @param $productId
	 * @param $automatId
	 * @return bool|mixed|\Nette\Database\Row|\Nette\Database\Table\IRow
	 */
	public function getAmountByIdAndAutomat($productId, $automatId)
	{
		return $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_AUTOMAT_ID, $automatId)
			->where(self::COLUMN_PRODUCT_ID, $productId)
			->fetch();
	}

	/**
	 * Získá hodnoty formátované jako kdyby to bylo pro select box [id] => hodnota
	 * @param $automatId
	 * @param $productList - seznam produktů
	 * @return array
	 */
	public function getDefaults($automatId, $productList)
	{
		$return = array();
		foreach($productList as $productId => $product)
		{
			$result = $this->getAmountByIdAndAutomat($productId, $automatId);
			$name = $productId;
			if($result == true)
			{
				$return[$name] = $result->amount;
			}
			else
				$return[$name] = 0;
		}

		return $return;
	}

	/**
	 * Získá zboží uložené v automatu
	 * @param $automatId
	 * @return array
	 */
	public function getAmountOfAutomat($automatId)
	{
		$result = $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_AUTOMAT_ID)
			->fetchAll();

		$return = array();
		foreach($result as $r)
		{
			$return[$r->product_id] = $r->amount;
		}
		return $return;

	}

	/**
	 * Prodání produktu, odčítání položek
	 * @param $items - koupené itemy (id a počet)
	 * @param $automatId
	 */
	public function sellProducts($items, $automatId)
	{
		//získá původní hodnoty
		$original = $this->getAmountOfAutomat($automatId);
		//pro každý zakoupený item
		foreach($items as $itemId => $itemCount)
		{
			//pokud není, pokračuj další iterací
			if(isset($original[$itemId]))
			{
				$value = $original[$itemId];
			}
			else
				continue;
			//nová hodnota - původní kredity mínus cena
			$newValue = $value - $itemCount;
			$data[self::COLUMN_PRODUCT_ID] = $itemId;
			$data[self::COLUMN_AMOUNT] = $newValue;
		}
		$this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_AUTOMAT_ID, $automatId)
			->update($data);
	}

	/**
	 * Vložení množství dle automatu a produktu
	 * @param $automatId
	 * @param $productId
	 * @param $amount - počet zboží
	 */
	private function insertAmount($automatId, $productId, $amount)
	{
		$data[self::COLUMN_AMOUNT] = $amount;
		$data[self::COLUMN_AUTOMAT_ID] = $automatId;
		$data[self::COLUMN_PRODUCT_ID] = $productId;

		$this->database->table(self::TABLE_NAME)
			->insert($data);
	}

	/**
	 * Aktualizuje množství zboží dle automatu a produktu
	 * @param $automatId
	 * @param $productId
	 * @param $amount - počet zboží
	 */
	private function updateAmount($automatId, $productId, $amount)
	{
		$data[self::COLUMN_AMOUNT] = $amount;

		$this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_AUTOMAT_ID, $automatId)
			->where(self::COLUMN_PRODUCT_ID, $productId)
			->update($data);
	}
}