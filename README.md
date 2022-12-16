## Simple chat API

- Clone project : git clone https://github.com/salnuraqidah/simple-chat-api.git
- Go to the folder application using cd command on your cmd or terminal
- Run composer install on your cmd or terminal
- Copy .env.example file to .env on the root folder. You can type copy .env.example .env if using command prompt Windows or cp .env.example .env if using terminal, Ubuntu
- Open your .env file and change the database name (DB_DATABASE) to whatever you have, username (DB_USERNAME) and password (DB_PASSWORD) field correspond to your configuration.
- Run php artisan key:generate
- Run php artisan migrate
- Run php artisan db:seed --class=UserSeeder
- Run php artisan db:seed --class=GroupSeeder
- Run php artisan db:seed --class=GroupUserSeeder
- Run php artisan serve
- Open postman for testing
- Read [Documentation](https://drive.google.com/file/d/1wILbsEK0OAP0dJQZNxjZ88y2Cnx7ylZZ/view?usp=sharing)


