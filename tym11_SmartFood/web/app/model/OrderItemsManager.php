<?php
namespace App\Model;

/**
 * Created by PhpStorm.
 * User: Michael Kufner
 * Date: 02.12.2016
 * Time: 23:19
 */
class OrdersItemsManager extends BaseManager
{
	const
		TABLE_NAME = "order_items",
		COLUMN_ID = "id",
		COLUMN_ORDER_ID = "order_id",
		COLUMN_PRODUCT_ID = "product_id",
		COLUMN_AMOUNT = "amount",
		COLUMN_PRICE = "price";

	/**
	 * Vložení zboží spojeného s objednávkou (N:N relace)
	 * @param $order_id - ID objednávky
	 * @param $product_id - id produktu
	 * @param $amount - množství
	 * @param $price - cena
	 */
	public function insert($order_id, $product_id, $amount, $price)
	{
		$data[self::COLUMN_ORDER_ID] = $order_id;
		$data[self::COLUMN_PRODUCT_ID] = $product_id;
		$data[self::COLUMN_AMOUNT] = $amount;
		$data[self::COLUMN_PRICE] = $price;

		$this->database->table(self::TABLE_NAME)
			->insert($data);
	}
}