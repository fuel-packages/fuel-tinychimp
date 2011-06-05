<?php

/**
 * A lightweight API wrapper for interacting with MailChimp
 *
 * @package		MailChimp
 * @author		Max Zender <maxzender@gmail.com>
 * @link		http://github.com/maxzender/fuel-tinychimp
 * @license		MIT License
 */

Autoloader::add_core_namespace('TinyChimp');

Autoloader::add_classes(array(
	'TinyChimp\\TinyChimp'		=> __DIR__.'/classes/tinychimp.php'
));


/* End of file bootstrap.php */