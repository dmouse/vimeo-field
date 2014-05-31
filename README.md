Vimeo Field Formatter
=====================
[![Build Status](https://travis-ci.org/dmouse/vimeo-field.svg?branch=master)](https://travis-ci.org/dmouse/vimeo-field)

Vimeo Field Formatter for Drupal 8

### Install
```bash
$ cd path/to/drupal/8/modules
$ git clone git@github.com:dmouse/vimeo_field.git
$ drush en -y vimeo_field # or enable this module via UI
```

### Usage
 
 * In your content type create a new textfield field
 * Go to /admin/structure/types/manage/[Content-Type]/display
 * Change the format field to use Vimeo Media

Resources
---------

You can run the unit tests with the following command:

    $ composer install
    $ vendor/bin/phpunit
