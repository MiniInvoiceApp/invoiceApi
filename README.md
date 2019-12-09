[![Generic badge](https://img.shields.io/badge/PHP-5.6-<COLOR>.svg)](https://badgen.net/badge/PHP/5.6/green)
[![MIT license](https://img.shields.io/badge/License-MIT-blue.svg)](https://lbesson.mit-license.org/)

## Installation

- For Windows:
    - Clone the project into your \xampp\htdocs folder.
- For Linux:
    - Clone the project into your /var/www/html folder.
    
- Go into invoiceApi/config folder and create a database.php file.
- Copy the content from database_sample.php file into database.php.
- Change the defines from database.php to point to your database.
    - DB_HOST: your database host (ex. localhost)
    - DB_USER: the user that has access into that database (ex. root)
    - DB_PASS: your database password (ex. myAwesomePass123)
    - DB_NAME: the name of your database (ex. users)
    - DB_PORT: the database port (ex. 3306)

## License
[MIT](https://choosealicense.com/licenses/mit/)