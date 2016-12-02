<?php
namespace App\Model;

class ImageStorage extends BaseManager
{
	private $dir;

	public function __construct($dir)
	{
		$this->dir = $dir;
	}

	/**
	 * @param $file - název obrázku př. obrazek.png
	 * @param $contents - adresa k temp obrázku
	 */
	public function save($file, $contents)
	{
		move_uploaded_file($contents, $this->dir . '/' . $file);
	}

	public function remove($file)
	{
		unlink($this->dir . '/' . $file);
	}
}