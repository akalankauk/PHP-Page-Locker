# PHP-Page-Locker
Protect Your PHP Page Using Simple Code.! Everything is inside a single file.
----------------
### Features

- No installation & No Database
- Very easy to use !only need 1 line of code.
- MD5 encryption enabled .
- Cookies session enabled .

#### Setup Guide

- The only thing you have to do is to copy the aedev.php in the folder with your website's php files.
```<?php include'aedev.php'; lock("aepass", 1); ?>```

Adding this line of code at the beginning of any PHP page, will lock it. The visitor will need to type
the password "aepass" to pass to the content of the page. (refer example.php file)
#### MD5 Encrypted password

-This code will lock the page with the password aepass, as the 39ef3d72a97f8aaf661e0c81b2233637 is the md5 encryption for aepass

```
<?php
    include'aedev.php';
    $options = array(
        "md5" => true
    );
    lock("39ef3d72a97f8aaf661e0c81b2233637", 1, $options);
?>```
