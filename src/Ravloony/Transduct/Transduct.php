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
	 *
	 * The cache tag so we can flush from the app
	 */
	const CACHE_TAG = 'ravloony-transduct';


	/**
	 * Get a JSON representation of a lang dir (recursive).
	 *
	 * @param string $directory the directory to get the langs from.
	 * @return array recursive array containing all the arrays of all the lang files in $directory.
	 */
    public function get($directory) {
		$directoryKey = self::CACHE_KEY.$directory;
		if (!Cache::tags(self::CACHE_TAG)->has($directoryKey) || Config::get('app.debug')) {
			$this->refreshCache($directory, $directoryKey);
		}
		return Cache::tags(self::CACHE_TAG)->get($directoryKey);
	}

	public function flush() {
		Cache::tags(self::CACHE_TAG)->flush();
	}

	private function refreshCache($directory, $directoryKey) {
		$locale = App::getLocale();
		$langs = $this->buildLangArray( app_path() . '/lang/' . $locale . '/' . $directory );
		$flags = JSON_HEX_QUOT | JSON_HEX_APOS | JSON_HEX_AMP;
		if (Config::get('app.debug')) {
			$flags |= JSON_PRETTY_PRINT;
		}
		$encoded_langs = json_encode($langs, $flags);
		Cache::tags(self::CACHE_TAG)->forever($directoryKey, $encoded_langs);
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
