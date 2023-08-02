# Laravel Heimdall

## About Laravel Heimdall

Laravel Heimdall is a package that allows you to specify a list of exact emails, email domains, and regular expressions for prohibiting emails.

## Installation

```bash
composer require amgrade/laravel-heimdall
```

## Configuration
This packages supports discovery configuration of the service provider. If you prefer manual installation, then add to `config/app.php` into `providers` section next line:

```php
'providers' => [
    AMgrade\Heimdall\ServiceProvider::class,
],
```

You can optionally publish the config file with:

```bash
php artisan vendor:publish --provider="\AMgrade\Heimdall\ServiceProvider"
```

Then open the `config/heimdall.php` and configure a list of emails, regular expressions or domains against which will be checked the email.

## Usage

You have two options to use this package: observer or/and validation rule.

Observer prevents the creating/updating of enumerated models in `heimdall.php` config file.

If you want to use the validation rule, just add `heimdall` validation rule to the field which should be validated, for example `'email' => ['heimdall']`.

Available options in a `heimdall` validation rule: `full`, `domain` and `regexp`, `all` - combination of these three rules.

Validation rule usage: `heimdall:domain`, `heimdall:domain,regexp`.

If no rule type is added, will be applied `all` rule type. For example: `heimdall`, `heimdall:`.
