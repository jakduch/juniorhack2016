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

	/**
	 * Získá všechny automaty
	 * @return array|\Nette\Database\Table\IRow[]
	 */
	public function getAll()
	{
		return $this->database->table(self::TABLE_NAME)
			->fetchAll();
	}

	/**
	 * Přidá automat
	 * @param $values
	 */
	public function addAutomat($values)
	{
		$data[self::COLUMN_NAME] = $values->name;
		$data[self::COLUMN_LOCALITY] = $values->locality;

		$this->database->table(self::TABLE_NAME)
			->insert($data);
	}

	/**
	 * Edituje automat
	 * @param $values
	 */
	public function updateAutomat($values)
	{
		$data[self::COLUMN_LOCALITY] = $values->locality;
		$data[self::COLUMN_NAME] = $values->name;

		$this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_ID, $values->id)
			->update($data);
	}

	/**
	 * Získá automat dle ID (primary key)
	 * @param $id
	 * @return bool|mixed|\Nette\Database\Row|\Nette\Database\Table\IRow
	 */
	public function getAutomatById($id)
	{
		return $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_ID, $id)
			->fetch();
	}

	/**
	 * Získá automaty ve formátu pro textbox [id] => text
	 * @return array
	 */
	public function getForArray()
	{
		$result = $this->database->table(self::TABLE_NAME)
			->order(self::COLUMN_NAME . " ASC")
			->fetchAll();

		$return = array();
		foreach($result as $r)
		{
			$return[$r->id] = $r->name . " - " . $r->locality;
		}

		return $return;
	}
}