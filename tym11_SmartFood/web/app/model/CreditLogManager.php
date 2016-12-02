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

	public function logSomething($description, $total, $userId)
	{
		$data[self::COLUMN_DATE] = new \DateTime();
		$data[self::COLUMN_DESCRIPTION] = $description;
		$data[self::COLUMN_TOTAL] = $total;
		$data[self::COLUMN_USER_ID] = $userId;
		$this->database->table(self::TABLE_NAME)
			->insert($data);
	}
}