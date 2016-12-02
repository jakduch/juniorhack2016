<?php

/**
 * Class PermissionsException
 * Vyjímka reprezentující chybu oprávnění
 */
class PermissionsException extends Exception
{
	protected $message = "Na tuto akci nemáte dostatečná oprávnění";
}