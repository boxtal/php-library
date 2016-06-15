# PHP Emc Library For Envoicoinscher API

This PHP library aims to present the PHP implementation of the EnvoiMoinsCher.com API.


### Installation

To install PHP Emc Library, simply:



### Requirements

PHP Emc Library works with PHP 5.4, 5.5, 5.6, 7.0.

In order to use the API, you need to create a (free) user account on www.envoimoinscher.com, checking the "I would like to install the EnvoiMoinsCher module directly on my E-commerce website."

You will then receive an email with your API keys and be able to start your tests.


### Quick Start and Examples

First, fill in your credentials and working environnement in the config/config.php file.

```php
define("EMC_MODE", "test");
if (EMC_MODE == "prod") {
    define("EMC_USER", "myLogin");
    define("EMC_PASS", "myPassword");
    define("EMC_KEY", "myAPIkeyProd");
} else {
    define("EMC_USER", "myLogin");
    define("EMC_PASS", "myPassword");
    define("EMC_KEY", "myAPIkeyTest");
}
```



























### Contribute
1. Check for open issues or open a new issue to start a discussion around a bug or feature.
1. Fork the repository on GitHub to start making your changes.
1. Write one or more tests for the new feature or that expose the bug.
1. Make code changes to implement the feature or fix the bug.
1. Send a pull request to get your changes merged and published.