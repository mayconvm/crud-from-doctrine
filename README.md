Crud-from-doctrine
==================

The CrudFromDoctrine is a utility generate code for Zend Framework 2.

## Features

* Create new Controller
* Create new Controlle APIRest
* Create new Form based in Doctrine entity
* Create new Validate for Form based in Doctrine entity

## Requiriments

* Zend Framework 2 or later
* ZFTools
* PHP 5.3 or later
 
## Installation using [Composer](http://getcomposer.org)
 1. Open console (command prompt)
 2. Go to your application's directory.
 3. Run `composer require mayconvm/crud-from-doctrine:dev-master`
 4. Execute the `public/index.php` as reported below
 
## Usage
  
### Create Controller APIRest

  index.php create apirest <name> <module> <path>
  
  <name>    The name of the controller
  <module>  The name of the module
  <path>    The path to folder of the ZF2 application (optional)
