<?php
namespace App\Model;
/**
 * Created by PhpStorm.
 * User: Michael Kufner
 * Date: 02.12.2016
 * Time: 18:08
 */

class AutomatManager extends BaseManager
{
	const
		TABLE_NAME = "automats",
		COLUMN_ID = "id",
		COLUMN_NAME = "name",
		COLUMN_LOCALITY = "locality";

	public function getAll()
	{
		return $this->database->table(self::TABLE_NAME)
			->fetchAll();
	}

	public function addAutomat($values)
	{
		$data[self::COLUMN_NAME] = $values->name;
		$data[self::COLUMN_LOCALITY] = $values->locality;

		$this->database->table(self::TABLE_NAME)
			->insert($data);
	}

	public function updateAutomat($values)
	{
		$data[self::COLUMN_LOCALITY] = $values->locality;
		$data[self::COLUMN_NAME] = $values->name;

		$this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_ID, $values->id)
			->update($data);
	}

	public function getAutomatById($id)
	{
		return $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_ID, $id)
			->fetch();
	}
}