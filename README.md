This is a super simple library to interact with curl. 

Install with composer cause that's what all the cool kids use these days.

```php
composer require n0nag0n/easy-curl
```

Basic Usage
```php
<?php
	include('/path/to/vendor/autoload.php');

	use n0nag0n\EasyCurl;

	echo EasyCurl::getRequest('https://www.google.com/');

	$result = EasyCurl::postRequest('https://example.com/v1/endpoint', [ 'is_json_request' => true, 'post_body' => '{"json":"values"}' ]);
```

Check out the file on your own for documenation. It's pretty dang simple.
