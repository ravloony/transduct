<?php

namespace Ravloony\Transduct;

use Cache;
use App;
use File;
use Config;

class Transduct {
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
		$directoryKey = self::CACHE_KEY.$directory;
		if (!Cache::has($directoryKey) || Config::get('app.debug')) {
			$this->refreshCache($directory, $directoryKey);
		}
		return Cache::get($directoryKey);
	}

	private function refreshCache($directory, $directoryKey) {
		$locale = App::getLocale();
		$langs = $this->buildLangArray( app_path() . '/lang/' . $locale . '/' . $directory );
		$flags = 0;
		if (Config::get('app.debug')) {
			$flags |= JSON_PRETTY_PRINT;
		}
		Cache::forever($directoryKey, json_encode($langs, $flags));
	}

	private function buildLangArray($directory) {
		$subDirectories = File::directories($directory);
		$lang = [];
		foreach($subDirectories as $subDirectory) {
			$slug = basename($subDirectory);
			$lang[$slug] = $this->buildLangArray($subDirectory);
		}
		$files = File::files($directory);
		foreach($files as $file) {
			$slug = basename($file, '.php');
			$lang[$slug] = File::getRequire($file);
		}
		return $lang;
	}
}
