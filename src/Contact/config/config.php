<?php 
return [
	'contact.to'=>\DI\get('mail.to'),
	\App\Contact\Action\ContactAction::class => \DI\object()->constructorParameter('to',\DI\get('contact.to'))
];