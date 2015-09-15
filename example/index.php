<?php

include 'Url.php';

$url = new Url('pages/',
        array(
            'test' => 'test.php',
            'about' => 'about.php',
            'contact' => 'contact.php'
        ),
        '404.php'
    );

?>