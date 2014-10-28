transduct
=========

Simple package for laravel 4 to export lang folders to JSON.

##Setup & Usage

### Installation

- Add `'Ravloony\Transduct\TransductServiceProvider',` to the end of the `providers` array in `/app/config/app.php`
- Add `'Ravloony\Transduct\Facades\Transduct'` to the end of the `aliases` array in `/app/config.php`

### Usage

There is only one method:

`Transduct::get('folder')`, where `folder` is a fully qualified folder or subfolder in `/app/lang/<locale>/`

Transduct is locale aware and will output the current language.
