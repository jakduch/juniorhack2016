<?php
namespace App\Model;

/**
 * Created by PhpStorm.
 * User: Michael Kufner
 * Date: 02.12.2016
 * Time: 23:19
 */
class OrdersManager extends BaseManager
{
	const
		TABLE_NAME = "orders",
		COLUMN_ID = "id",
		COLUMN_DATE = "date",
		COLUMN_CUSTOMER_ID = "customer_id",
		COLUMN_TOTAL_PRICE = "total_price",
		COLUMN_ORDER_NUMBER = "order_number",
		COLUMN_AUTOMAT_ID = "automat_id";

	/**
	 * Vložení nové objednávky
	 * @param $customer_id - id zákazníka
	 * @param $total_price - celková cena objednávky
	 * @param $automat_id - id automatu
	 * @return mixed
	 */
	public function insert($customer_id, $total_price, $automat_id)
	{
		$data[self::COLUMN_DATE] = new \DateTime();
		$data[self::COLUMN_CUSTOMER_ID] = $customer_id;
		$data[self::COLUMN_TOTAL_PRICE] = $total_price;
		$data[self::COLUMN_AUTOMAT_ID] = $automat_id;
		$data[self::COLUMN_ORDER_NUMBER] = $this->generateOrderNumber();

		$row = $this->database->table(self::TABLE_NAME)
			->insert($data);

		return $row->id;
	}

	/**
	 * Získá objednávku dle id uživatele
	 * @param $id
	 * @return array|\Nette\Database\Table\IRow[]
	 */
	public function getByUser($id)
	{
		return $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_CUSTOMER_ID, $id)
			->order(self::COLUMN_DATE . " ASC")
			->fetchAll();
	}

	/**
	 * Získá objednávku dle ORDER NUMBER (pro android)
	 * @param $number
	 * @return bool|mixed|\Nette\Database\Row|\Nette\Database\Table\IRow
	 */
	public function getByOrderNumber($number)
	{
		return $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_ORDER_NUMBER, $number)
			->fetch();
	}

	/**
	 * Generuje číslo (zatím testovací rozsah bytu)
	 * @return int - náhodné číslo v testovacím rozsahu (0-254)
	 */
	private function generateOrderNumber()
	{
		$random = rand(1, 254);
		if($this->getByOrderNumber($random) != true)
		{
			return $random;
		}

		else
			$this->generateOrderNumber();
	}
}