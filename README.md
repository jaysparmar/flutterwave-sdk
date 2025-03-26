# Flutterwave SDK for PHP

## 🌟 Introduction

The Flutterwave SDK is a robust, lightweight PHP library designed to simplify interactions with the Flutterwave Payment API. Built with developers in mind, this SDK provides a clean, intuitive interface for handling various financial operations seamlessly.

## 📋 Table of Contents

- [Overview](#-overview)
- [Features](#-features)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Usage Examples](#-usage-examples)
- [Supported Operations](#-supported-operations)
- [Error Handling](#-error-handling)
- [Security](#-security)
- [Contributing](#-contributing)
- [License](#-license)
- [Contact](#-contact)

## 🚀 Overview

Flutterwave SDK offers developers a comprehensive toolkit to integrate Flutterwave's payment and financial services into their PHP applications. Whether you're building a fintech solution, an e-commerce platform, or need advanced payment routing, this library provides the tools you need.

## ✨ Features

### Financial Operations
- Account Balance Retrieval
- Transaction Management
- Virtual Account Creation
- Bill Payments
- Transfer Operations

### Card Services
- Virtual Card Creation
- Card Funding
- Card Transaction Tracking
- Card Block/Unblock
- Card Termination

### Verification Services
- Bank Account Resolution
- BVN (Bank Verification Number) Verification
- OTP (One-Time Password) Sending and Verification

### Additional Capabilities
- Multi-Currency Support
- Webhook Integration
- Comprehensive Error Handling
- Flexible Configuration

## 🔧 Requirements

- PHP 7.3 or higher
- Composer
- Flutterwave API Credentials
- cURL Extension

## 💾 Installation

Install the package via Composer:

```bash
composer require jaysparmar/flutterwave-sdk
```

## 🔐 Configuration

### Obtaining Credentials
1. Log in to your Flutterwave Dashboard
2. Navigate to API Settings
3. Generate/Copy your:
    - Public Key
    - Secret Key
    - Encryption Key

### Initialization

```php
use jaysparmar\flutterwave\Flutterwave;

$flutterwave = new Flutterwave(
    $public_key,        // Your Flutterwave public key
    $secret_key,        // Your Flutterwave secret key
    $encryption_key,    // Your Flutterwave encryption key
    $currency_code,     // Default currency (e.g., 'NGN')
    $card_webhook_url   // Webhook URL for card-related events
);
```

## 🛠 Usage Examples

### 1. Account Balance

```php
// Retrieve balances for all supported currencies
$allBalances = $flutterwave->balances();

// Get balance for a specific currency
$nairaBalance = $flutterwave->balances('NGN');
```

### 2. Virtual Account Creation

```php
$virtualAccount = $flutterwave->create_virtual_account([
    'email' => 'user@example.com',
    'is_permanent' => true,
    'bvn' => '12345678901',
    'phonenumber' => '08109328188',
    'firstname' => 'John',
    'lastname' => 'Doe'
]);
```

### 3. Bill Payments

```php
// List available bill types
$billTypes = $flutterwave->get_available_bills();

// Validate a bill
$validation = $flutterwave->validate_bill(
    $item_code,     // Item code from bill categories
    $biller_code,   // Specific biller code
    $customer_id    // Customer identifier
);

// Pay a bill
$billPayment = $flutterwave->pay_bill(
    'NG',           // Country code
    $customer_id,   // Customer identifier
    $bill_type,     // Bill type (airtime, power, etc.)
    $unique_ref,    // Unique transaction reference
    $amount         // Bill amount
);
```

### 4. Virtual Card Management

```php
// Create a virtual card
$card = $flutterwave->create_virtual_card(
    $unique_ref,    // Unique reference for the card
    $currency,      // Card currency
    $initial_amount,// Initial funding amount
    $billing_name   // Cardholder name
);

// Fund a card
$fundCard = $flutterwave->fund_card(
    $amount,        // Funding amount
    $card_id        // Card identifier
);

// Block/Unblock a card
$blockCard = $flutterwave->block_card($card_id, 'block');
```

## 💱 Supported Currencies

The SDK supports a wide range of currencies:
- NGN (Nigerian Naira)
- KES (Kenyan Shilling)
- GHS (Ghanaian Cedi)
- USD (US Dollar)
- EUR (Euro)
- ZAR (South African Rand)
- And many more...

## 🚨 Error Handling

All methods return a standardized response array:

```php
[
    'error' => bool,       // Operation status
    'message' => string,   // Descriptive message
    'data' => array        // Response payload
]
```

### Example Error Handling

```php
$result = $flutterwave->some_method();

if ($result['error']) {
    // Handle error
    echo "Error: " . $result['message'];
} else {
    // Process successful response
    $data = $result['data'];
}
```

# Flutterwave SDK for PHP

## Other Available Functions

### Transfer and Transaction Related Functions

#### Transfer Rates
```php
// Check transfer rates between currencies
$transferRates = $flutterwave->transfer_rate(
    $amount,        // Amount to transfer
    $source_currency,       // Source currency code
    $destination_currency   // Destination currency code
);
```

#### Create Transfer
```php
// Transfer funds to a bank account
$transfer = $flutterwave->create_transfer(
    $bank_code,     // Recipient bank code
    $account_number,// Recipient account number
    $amount,        // Transfer amount
    $txn_ref,       // Unique transaction reference
    $meta_data      // Additional metadata
);
```

#### Transfer Fees
```php
// Check transfer fees
$transferFees = $flutterwave->transfer_fees(
    $amount,        // Transfer amount
    $currency       // Currency code (default NGN)
);
```

### Bank and Account Related Functions

#### Resolve Account Number
```php
// Validate and retrieve account details
$accountDetails = $flutterwave->resolve_account_number(
    $account_number,    // Account number
    $bank_code          // Bank code
);
```

#### Get All Banks
```php
// Retrieve list of banks (default Nigeria)
$banks = $flutterwave->get_all_banks($country_code);
```

#### Get Bank Branches
```php
// Get branches for a specific bank
$branches = $flutterwave->get_bank_branches($bank_id);
```

### OTP (One-Time Password) Functions

#### Send OTP
```php
// Send OTP to user
$otp = $flutterwave->send_otp(
    $name,          // User's name
    $email,         // User's email
    $phone,         // User's phone number
    $medium         // Delivery methods (email, whatsapp, sms)
);
```

#### Verify OTP
```php
// Verify the OTP
$verification = $flutterwave->verify_otp(
    $otp,           // OTP code
    $reference      // OTP reference
);
```

### Additional Specialized Functions

#### BVN Verification
```php
// Verify Bank Verification Number
$bvnVerification = $flutterwave->verify_bvn($bvn_number);
```

### Card Management Extended Examples

#### Get All Cards
```php
// Retrieve list of virtual cards
$cards = $flutterwave->get_all_cards($page_number);
```

#### Get Specific Card Details
```php
// Get details of a specific card
$cardDetails = $flutterwave->get_card($card_id);
```

#### Card Transactions
```php
// Retrieve card transactions
$cardTransactions = $flutterwave->get_card_transactions(
    $card_id,               // Card identifier
    $from_date,             // Start date
    $to_date,               // End date
    $transactions_per_page, // Transactions per page
    $page_number            // Page number
);
```

#### Withdraw from Card
```php
// Withdraw funds from a virtual card
$withdrawal = $flutterwave->withdraw_from_card(
    $amount,    // Withdrawal amount
    $card_id    // Card identifier
);
```

### Comprehensive Transaction Retrieval

```php
// Retrieve transactions with multiple filters
$transactions = $flutterwave->get_all_transactions([
    'from' => '2023-01-01',
    'to' => '2023-12-31',
    'page' => 1,
    'customer_email' => 'user@example.com',
    'status' => 'successful',
    'currency' => 'NGN'
]);
```

## Additional Notes on Function Usage

1. **Consistent Return Format**: All functions return an array with:
    - `error`: Boolean indicating success/failure
    - `message`: Descriptive message
    - `data`: Returned data or empty array

2. **Error Handling**: Always check the `error` key before processing the response.

3. **Currency Support**: Verify supported currencies before making transactions.

4. **Reference Management**: Generate unique references for transactions.

## Webhook Integration

- All card and transfer operations support webhook callbacks
- Configure `$card_webhook_url` during initialization
- Implement webhook endpoint to receive real-time transaction updates

## Supported Currencies Expanded

- NGN (Nigerian Naira)
- KES (Kenyan Shilling)
- GHS (Ghanaian Cedi)
- USD (US Dollar)
- EUR (Euro)
- ZAR (South African Rand)
- GBP (British Pound)
- And more: TZS, UGX, RWF, ZMW, INR, XOF, MUR, ETB, JPY, MAD, XAF, AUD, CAD, MYR, CNY, BRL, eNGN, MWK

---

**Pro Tip**: Always refer to the Flutterwave API documentation for the most up-to-date information on supported features and any changes in API behavior.

## 🔒 Security Considerations

- Always keep your API credentials confidential
- Use environment variables for sensitive information
- Implement proper error logging
- Validate and sanitize all input data
- Use HTTPS for all API communications

## 🤝 Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Open a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards
- Write comprehensive tests
- Update documentation
- Maintain backward compatibility

## 📄 License

Distributed under the MIT License. See `LICENSE` for more information.

## 📞 Contact & Support

- **Author:** Jay Parmar
- **Email:** bharticloud@gmail.com
- **Project Link:** [GitHub Repository](https://github.com/jaysparmar/flutterwave-sdk)

## 🙏 Acknowledgements

- [Flutterwave](https://flutterwave.com/) for their comprehensive API
- PHP and Composer communities
- All contributors and supporters

---

**Disclaimer:** This SDK is an independent library and is not officially maintained by Flutterwave. Always refer to the official Flutterwave documentation for the most up-to-date information.