<?php
namespace App\Model;

use Nette\Database\Context;
use Nette\Neon\Exception;
use Nette\Object;


abstract class BaseManager extends Object
{
	const MSG_REQ = "Toto pole je povinné";

	/** @var  Context Instance třídy pro práci s databází */
	protected $database;

	/** @var  ImageStorage Instance třídy pro práci s obrázky */
	protected $imageStorage;

	/**
	 * BaseManager constructor - Dependency Injection přes konstruktor
	 * @param Context $database
	 * @param ImageStorage $imageStorage
	 */
	public function __construct(Context $database, ImageStorage $imageStorage)
	{
		$this->database = $database;
		$this->imageStorage = $imageStorage;
	}
}
