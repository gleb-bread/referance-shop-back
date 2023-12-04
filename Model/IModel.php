<?php

namespace Model;

interface IModel {
	public static function create(array $data);
	public static function exists($id);
	public static function getAll(array $params=[]);
	public static function get($id);
	public static function getFrom(array $data, bool $check=false);
	public function update(array $data);
}
