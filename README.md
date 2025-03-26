# Infinitie Flutterwave

A lightweight custom Flutterwave library for PHP.

## Installation

Use Composer to install the package:

```bash
composer require yourusername/infinitie_flutterwave
```

## Usage

```php
use App\Libraries\Flutterwave;

$flutterwave = new Flutterwave();

// Example usage
$balances = $flutterwave->balances();
print_r($balances);
```

## License

This project is licensed under the MIT License.