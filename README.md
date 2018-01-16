# cmsms-porterstemmer-rus
PHP Implementation of the Porter Stemmer algorithm for Cms made simple. Russian language.

Replace PorterStemmer.class.php in /modules/Search directory.

# Installation
Install this package through Composer. To your composer.json file, add:
```php
{
    "require": {
        "wdwp/cmsms-porterstemmer-rus": "dev-master"
    }
}
```
# Usage
```php
$stemmer = new PorterStemmer();
echo $stemmer->Stem('выражению');
```
