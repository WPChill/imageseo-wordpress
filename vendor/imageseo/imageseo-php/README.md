<!-- logo -->
<img src="https://imageseo.io/wp-content/themes/imageseo/static/img/logo-text-color.svg" height="40" />

# ImageSEO SDK PHP

> Official PHP SDK of the ImageSEO API. This allows you to use our API: https://imageseo.io

---

API Documentation : https://docs.imageseo.io/

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

View all ressources : https://docs.imageseo.io/resources

-   `Projects` :

    -   getOwner()

-   `ImageReports`

    -   generateReportFromUrl($data,$query = null)
    -   generateReportFromFile($data,$query = null)

-   `Languages`
    -   getLanguages()

## Example

### Authentication

```php
<?php

use ImageSeo\Client\Client;

$apiKey = "YOUR_API_KEY";
$client =new Client($apiKey);
```

### Resources

Our SDK operates on the principle of resources. You must choose the resource on which you want to make an API call in order to use different methods. See the list of our resources

#### Image Reports

Example : Generate an image report from URL

```php
<?php

use ImageSeo\Client\Client;

$apiKey = "YOUR_API_KEY";
$client =new Client($apiKey);

$data = [
    "url": "https://example.com/image.jpg"
];
$report = $client->getResource('ImageReports')->generateReportFromUrl($data);
```

Example : Generate an image report from file

```php
<?php

use ImageSeo\Client\Client;

$apiKey = "YOUR_API_KEY";
$client =new Client($apiKey);

$data = [
    "filePath": "/path/your/image.jpg"
];
$report = $client->getResource('ImageReports')->generateReportFromFile($data);
```

#### Languages

Get languages available

```php
<?php

use ImageSeo\Client\Client;

$apiKey = "YOUR_API_KEY";
$client =new Client($apiKey);

$report = $client->getResource('Languages')->getLanguages();
```

## About

`imageseo-php` is guided and supported by the ImageSEO Developer Team.

## License

[The MIT License (MIT)](LICENSE.txt)
