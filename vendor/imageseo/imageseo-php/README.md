<!-- logo -->
<img src="https://imageseo.io/wp-content/themes/imageseo/static/img/logo-text-color.svg" height="40" />

# ImageSEO SDK PHP

> Official PHP SDK of the ImageSEO API. This allows you to use our API: https://imageseo.io

---

## Requirements

-   PHP version 5.6 and later
-   ImageSEO API Key, starting at [free level](https://imageseo.io/register)

## Installation

You can install the library via [Composer](https://getcomposer.org/). Run the following command:

```bash
composer require imageseo/imageseo-php
```

To use the library, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once __DIR__. '/vendor/autoload.php';
```

## Resources

-   `Projects` :

    -   getOwner()

-   `ImageReports`
    -   generateReportFromUrl($data,$query = null)
    -   generateReportFromFile($data,$query = null)

## About

`imageseo-php` is guided and supported by the ImageSEO Developer Team.

## License

[The MIT License (MIT)](LICENSE.txt)
