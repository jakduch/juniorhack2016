<?php
namespace App\Model;

class CorePagesManager extends BaseManager
{
	const
		TABLE_NAME = "pages",
		COLUMN_ID = "id",
		COLUMN_PRESENTER = "presenter",
		COLUMN_ACTION = "action",
		COLUMN_SORT = "sort",
		COLUMN_TEXT = "text";

	/**
	 * @return array - Dvourozměrné pole s left side navigací
	 * Vytváří leftSide navigaci na základě oprávnění (zadání čerpá z DB)
	 */
	public function createMenu()
	{
		$headings = $this->database->table(self::TABLE_NAME)
			->where(self::COLUMN_ACTION, '')
			->order(self::COLUMN_SORT . " ASC")
			->fetchAll();

		$categories = $this->database->table(self::TABLE_NAME)
			->where("NOT " . self::COLUMN_ACTION, '')
			->order(self::COLUMN_SORT . " ASC");

		foreach($headings as $heading)
		{
			$section = clone $categories;
			foreach($section->where(self::COLUMN_PRESENTER, $heading->{self::COLUMN_PRESENTER}) as $category)
			{
			}
		}
		$toReturn = array("headings" => $headings, "categories" => $categories);
		return $toReturn;
	}
}