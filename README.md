# Kakadu - The free learn portal

Kakadu is a PHP website, that focuses on learning. The user can create learngroups, courses, catalogs and questions and 
share them with other users. This project includes also an learnalgorithm, that tries to select the best questions for 
the user so his learn success is as high as possible.

Kakadu is bsed on the following open source project:
- Laravel - A clean and classy framework for PHP web development
- PHPExcel - A pure PHP library for reading and writing spreadsheet files
- jQuery - A multi-browser JavaScript library designed to simplify the client-side scripting of HTML
- Bootstrap -  A free collection of tools for creating websites and web applications
- Backbone - A JavaScript library with a RESTful JSON interface

## Requirements
- Apache web server with PHP 5.3
- MySQL Database
- [Laravel Requirements](http://laravel.com/docs/install#requirements)


## Installation
- Download Kakadu
- Upload the content to your web server
- Verify that the following directories and files are writeable:
  - storage/view
  - application/config/kakadu
  - application/config/application.php
- Point your Apache VirtualHost configuration to the public folder

<pre><code>
    &lt;VirtualHost *:80&gt;
        DocumentRoot /Users/Kakadu/Sites/kakadu/public
        ServerName yourwebsite.com
    &lt;/VirtualHost&gt;
    
</code></pre>

- Open the installation with our browser and fill out all settings: http://yourwebsite.com/install
- Remove the installation routes form the file kakadu/application/routes.php



## License
Kakadu is open-sourced software licensed under the MIT License.
