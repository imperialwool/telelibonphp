# Telegram Lib On PHP
Simple lib for simple (or not) Telegram bots.

### How to connect that lib to document?
In your script just include that file and start class in some var.
```php
<?php 
include 'tgLib.php';
$bot = new tgBot('MY TOKEN IS HERE!');
?>
```

### Basics.
* request('method', ['parametrs' => 'here']) - requesting methods with params.
* reply($chat, "Text is here!") - replying.
* Another functions you can see in example.
