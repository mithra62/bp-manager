<?php
namespace Base\Model;

interface HashInterface
{
	public function encrypt($string);
	public function decrypt($string);
}