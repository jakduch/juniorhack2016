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
		COLUMN_AUTOMAT_ID = "automat_id";

	public function insert($customer_id, $total_price, $automat_id)
	{
		$data[self::COLUMN_DATE] = new \DateTime();
		$data[self::COLUMN_CUSTOMER_ID] = $customer_id;
		$data[self::COLUMN_TOTAL_PRICE] = $total_price;
		$data[self::COLUMN_AUTOMAT_ID] = $automat_id;

		$row = $this->database->table(self::TABLE_NAME)
			->insert($data);

		return $row->id;
	}
}