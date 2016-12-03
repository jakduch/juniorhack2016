<?php
/**
 * Created by PhpStorm.
 * User: Michael Kufner
 * Date: 02.12.2016
 * Time: 16:45
 */

namespace App\Model;

class CreditLogManager extends BaseManager
{
	const
		TABLE_NAME = "credit_log",
		COLUMN_ID = "id",
		COLUMN_DATE= "date",
		COLUMN_DESCRIPTION = "description",
		COLUMN_TOTAL = "total",
		COLUMN_USER_ID = "user_id";

	/**
	 * Zaloguje pohyb kreditů
	 * @param $description - text (do závorky změna stavu kreditů)
	 * @param $total - celkový počet kreditů po operaci
	 * @param $userId - ID uživatele (primary key)
	 */
	public function logSomething($description, $total, $userId)
	{
		$data[self::COLUMN_DATE] = new \DateTime();
		$data[self::COLUMN_DESCRIPTION] = $description;
		$data[self::COLUMN_TOTAL] = $total;
		$data[self::COLUMN_USER_ID] = $userId;
		$this->database->table(self::TABLE_NAME)
			->insert($data);
	}

	/**
	 * Získá logy uživatelových kreditů seřazených dle času
	 * @param $id - ID uživatele
	 * @return array|\Nette\Database\Table\IRow[]
	 */
	public function getLogsOfUser($id)
	{
		return $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_USER_ID, $id)
			->order(self::COLUMN_DATE . " ASC")
			->fetchAll();
	}
}