#TinyChimp for Fuel

Version: 1.0

A lightweight API wrapper for interacting with the [MailChimp API 1.3](http://apidocs.mailchimp.com/1.3/) for the [Fuel PHP](http://fuelphp.com/) framework.

##Requirements

In order to run TinyChimp for Fuel, you'll need:

* the Fuel framework, of course
* the cURL library installed
* a MailChimp account and a valid [API key](http://admin.mailchimp.com/account/api)

##Installation

**Via the Oil utility:**

Type this in your terminal:

`php oil package install tinychimp`

**Without the Oil utility:**

1. Move this folder to fuel/packages/
2. Enable the package in your config:
	`'packages' => array(
    	'fuel-tinychimp',
	)`
3. Copy the package's config file to fuel/app/config/ and add the required data such as API key etc.

##Usage

You can use the API methods [documented here](http://apidocs.mailchimp.com/1.3/) right off your controller like this:

	TinyChimp::lists(array('start' => 0, 'limit' => 50));
	
Or if there are a lot of arguments to pass:

	$params = array(
		'id'			=> 'abcd1234',
		'email_address'	=> 'foo@bar.com',
		'double_optin'	=> true,
		'send_welcome'	=> true
	);
	
	TinyChimp::listSubscribe($params);
	
The order in which the arguments are passed is completely arbitrary.
	
Also, if you don't like CamelCase you can alternatively call any method with underscores instead. So the example above equals the following:

	TinyChimp::list_subscribe($params);
	
##License

(The MIT License)

Copyright (c) 2011 Max Zender

Permission is hereby granted, free of charge, to any person
obtaining a copy of this software and associated documentation
files (the "Software"), to deal in the Software without
restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or
sell copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall
be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.