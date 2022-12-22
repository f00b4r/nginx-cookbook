<?php declare(strict_types = 1);

final class App
{

	public static function init()
	{
	}

	public static function error(string $error, int $code = 400): void
	{
		self::json(['error' => $error], $code);
	}

	public static function json(array $data, int $code = 200): void
	{
		self::header('content-type', 'application/json');
		self::statusCode($code);
		echo json_encode($data);
		exit();
	}

	public static function isPreflight(): bool
	{
		return mb_strtoupper($_SERVER['REQUEST_METHOD']) === 'OPTIONS';
	}

	public static function cors(): void
	{
		self::header('Access-Control-Allow-Origin', '*');
		self::header('Access-Control-Allow-Methods', '*');
		self::header('Access-Control-Allow-Headers', '*');
	}

	public static function header(string $name, string $value): void
	{
		header($name . ': ' . $value, false);
	}

	public static function statusCode(int $code): void
	{
		http_response_code($code);
	}

	public static function terminate(): void
	{
		exit();
	}

	public static function run(): void
	{
		self::json(['date' => date('d.m.Y H:i:s'), 'timestamp' => time()]);
	}
}

App::init();

if (App::isPreflight()) {
	App::cors();
} else {
	App::run();
}

App::terminate();