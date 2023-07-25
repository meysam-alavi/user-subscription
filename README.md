# user-subscription

### setup project an use it by run following command.
- systemctl start docker

### in root of web app
- ./vendor/bin/sail up -d
- ./vendor/bin/sail root-shell
- chmod -R 0777 bootstrap/
- chmod -R 0777 storage/
- php artisan cache:clear
- php artisan schedule:list
- php artisan schedule:work
- php artisan app:alert-subscribe-expiration-to-user
- php artisan test

### outbound of containers
- ./vendor/bin/sail test

- phpmyadmin -> http://localhost:8080
- laravel mail pit dashboard -> http://localhost:8025/
