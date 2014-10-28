<?php

namespace Ravloony\LaravelTransduct;

use Cache;
use App;
use File;
use Log;
use Config;

class LaravelTransduct {
	/**
	 * The key used to cache the JSON messages.
	 *
	 * @var string
	 */
	const CACHE_KEY = 'transduct-json-lang-export';


	/**
	 * Get a JSON representation of a lang dir (recursive).
	 *
	 * @param string $directory the directory to get the langs from.
	 * @return array recursive array containing all the arrays of all the lang files in $directory.
	 */
	public function get($directory) {
		if (!Cache::has(self::CACHE_KEY.$directory) || Config::get('app.debug')) {
			$this->refreshCache($directory);
		}
		return Cache::get(self::CACHE_KEY.$directory);
	}

	private function refreshCache($directory) {
		$locale = App::getLocale();
		$langs = $this->buildLangArray( app_path() . '/lang/' . $locale . '/' . $directory );
		$flags = JSON_FORCE_OBJECT;
		if (Config::get('app.debug')) {
			$flags |= JSON_PRETTY_PRINT;
		}
		Cache::forever(self::CACHE_KEY.$directory, json_encode($langs, $flags));
	}

	private function buildLangArray($directory) {
		Log::info('Building array from directory ' . $directory);
		$subDirectories = File::directories($directory);
		Log::info('Found subdirectories', $subDirectories);
		$lang = [];
		foreach($subDirectories as $subDirectory) {
			$slug = basename($subDirectory);
			$lang[$slug] = $this->buildLangArray($subDirectory);
		}
		$files = File::files($directory);
		Log::info('Found files', $files);
		foreach($files as $file) {
			$slug = basename($file, '.php');
			$lang[$slug] = File::getRequire($file);
		}
		Log::info('Completed array:', $lang);
		return $lang;
	}
}
