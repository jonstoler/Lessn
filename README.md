**Uppn in an extremely simple, personal file uploader and shortener written in PHP with MySQL and mod_rewrite.**

**It is a fork of [Lessn](https://github.com/shauninman/Lessn), an excellent URL shortener by [Shaun Inman](https://github.com/shauninman).**

### Installation

1. Open /uppn/-/config.php in a plaintext editor and create an Uppn username and password then enter your database connection details.

2. Upload the entire /uppn/ directory to your server.  For the shortest urls, place it at the root of your site and rename to a single character. Example: http://doma.in/u/

3. Visit http://doma.in/u/-/ to Uppn a new file (the required database table is created automatically the first time you visit Lessn).

**NOTE:**  
If your Uppn'd urls aren't working you probably didn't upload the .htaccess file. Enable "Show invisible files" in your FTP application.

Please look at [Lessn's README](https://github.com/shauninman/Lessn) for update instructions.