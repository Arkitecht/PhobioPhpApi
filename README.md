PHP Wrapper for Phobio REST API
================

This is a pure PHP library for the Phobio REST API (http://phobio.com/docs/api)

## Requirements
The CURL extension must be compiled into PHP to use this library see [PHP CURL Docs](https://php.net/manual/en/curl.installation.php)

## Basic Overview
The library is written in actionMethod format. So all methods are named getX, createX, deleteX. 

## Per Page Default
The libray defines a default: **PHOBIO_DEFAULT_PER_PAGE** of 30 (which matches the Phobio default). To override and default to another amount of results per page for all requests, define **PHOBIO_DEFAULT_PER_PAGE** before including the library.

## Sample Code
```php
<?php
define('PHOBIO_DEFAULT_PER_PAGE',100);
$phobio = new Phobio('USERNAME','TOKEN',true);

//Get the list of Manufacturers 
$phobio->getManufacturers();

//Get the products from 1 manufacturer
$phobio->getManufacturerProducts('apple');

//Product Search
$phobio->getProductSearch('iphone');
?>
```

## Convenience Classes
The library also provides the foloowing convenience classes for complex request objects:
 - [QuoteRequest](#QuoteRequest)
 - [InvoiceRequest](#InvoiceRequest)
 - [UserRequest] (#UserRequest)
 - [UpdateUserRequest] (#UpdateUserRequest)
 - [AuthenticatedURLRequest] (#AuthenticatedURLRequest)

- - - 

#### QuoteRequest ####
The QuoteRequest class will allow you to construct a complex Quote, with multiple products and sales, and create the JSON request for the createQuote method.

**Sample Code:**
```php
<?php
$quote = new Phobio_QuoteRequest();
//Add a product with product, condition
$quote->addProduct('apple-iphone-5s-32gb-gsm','working');
$quote->addProduct('apple-iphone-5s-16gb-gsm','working');

//Add a sales sku with sku, description
$quote->addSale('1234567890ABCDEF','iPhoneT1234/White/32GB');
$quote->addSale('2345678901ABCDEF','GalaxyA4321/Black/16GB');

//Add a promo code
$quote->setPromoCode('getmoreback');

print $quote->toJSON();
?>
```

**Output:**
```JSON
{
  "products": [
    {
      "slug": "apple-iphone-5s-32gb-gsm",
      "condition": "working"
    },
    {
      "slug": "apple-iphone-5s-16gb-gsm",
      "condition": "working"
    }
  ],
  "sales": [
    {
      "sku": "1234567890ABCDEF",
      "description": "iPhoneT1234/White/32GB"
    },
    {
      "sku": "2345678901ABCDEF",
      "description": "GalaxyA4321/Black/16GB"
    }
  ],
  "promo_code": "getmoreback"
}
```

The quote object is passed directly to ```createQuote()```

- - - 

#### InvoiceRequest ####
The InvoiceRequest class will allow you to construct a complex Invoice, with multiple products and sales, and create the request parameters for the createInvoice method. 

**Sample Code:**
```php
<?php
//Create a new invoice request, with the quote GUID
$invoice = new Phobio_InvoiceRequest(29312432015);

//Set the customer first name, last name
$invoice->setCustomer('Good','Customer');

//Add the customer identification method
$invoice->setCustomerID('12344234234','drivers_license');

//Add sales sku(s)
$invoice->addSalesSku('1234567890ABCDEF');
$invoice->addSalesSku('2345678901ABCDEF');

//create the invoice
$phobio->createInvoice($invoice);
?>
```

The invoice object is passed directly to ```createInvoice()```

- - - 

#### UserRequest ####
The UserRequest class will allow you to construct a user object for creating a user in Phobio. 

**Sample Code:**
```php
<?php
//Create a new user with email, first name, last name, and company_location_uid
$user = new Phobio_UserRequest('gooduser@test.com','Good','User',8900000000);
//Set the user as a company manager
$user->setIsCompanyManager();

$user->setExternalUID('MYCO-GU-123');
//Either a username or exernal UID are required
//$user->setUsername('goodUser123');

//Set a phone number
$user->setPhone('2125551212');
//Set a password
$user->setPassword('myG00dP4$5');

//create the user
$phobio->createUser($user)
?>
```

The user object is passed directly to ```createUser()```

- - - 

#### UpdateUserRequest ####
The UpdateUserRequest object will allow you to update an existing user. It can be created from a username, or statically from the response of the ```getUser()``` call.

**Sample Code:**
```php
<?php
//Create a new update user from username
$updateUserFromUsername = new Phobio_UpdateUserRequest('guser');

//Get a user from Phobio
$storedUser = $phobio->getUser('guser');
//Create an update user from a Phobio user response
$updateUser = Phobio_UpdateUserRequest::createFromUserObject($storedUser);

//toggle user active state
$updateUser->setIsActive(false);

//save the user update 
$phobio->updateUser($updateUser)
?>
```

The updateuser object is passed directly to ```updateUser()```

- - - 

#### AuthenticatedURLRequest ####
The AuthenticatedURLRequest object will allow you to construct a complex AuthenticatedURL object, providing methods to easily add multiple skus and/or customer ids and properly encode.

**Sample Code:**
```php
<?php
$url = new Phobio_AuthenticatedURLRequest();
$url->addSku('sku1');
$url->addSku('sku2');

$url->setCustomerName('Good','Customer');

//reques the authenticated URL
$phobio->getAuthenticatedURL('embedded_trade_flow',$url);
?>
```
The authenticatedurl object is passed directly to ```getAuthenticatedURL()```
