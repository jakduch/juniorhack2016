<?php
namespace App\Model;
/**
 * Created by PhpStorm.
 * User: Michael Kufner
 * Date: 02.12.2016
 * Time: 17:30
 */

class ProductManager extends BaseManager
{
	const
		TABLE_NAME = "products",
		COLUMN_ID = "id",
		COLUMN_NAME = "name",
		COLUMN_PRICE = "price";

	public function getAll()
	{
		return $this->database->table(self::TABLE_NAME)
			->fetchAll();
	}

	public function addProduct($values)
	{
		$data[self::COLUMN_NAME] = $values->name;
		$data[self::COLUMN_PRICE] = $values->price;

		$this->database->table(self::TABLE_NAME)
			->insert($data);
	}

	public function updateProduct($values)
	{
		$data[self::COLUMN_PRICE] = $values->price;
		$data[self::COLUMN_NAME] = $values->name;

		$this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_ID, $values->id)
			->update($data);
	}

	public function getProductById($id)
	{
		return $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_ID, $id)
			->fetch();
	}
}