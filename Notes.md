docker-compose down -v
docker-compose up -d --build

docker exec -it laravel_admin_app php artisan migrate:fresh --seed
docker exec -it laravel_admin_app composer update 
docker exec -it laravel_admin_app chown -R www-data:www-data /var/www/html/database
docker exec -it laravel_admin_app chown -R www-data:www-data /var/www/html/storage

docker exec laravel_admin_app php artisan migrate:fresh --seed
docker exec laravel_admin_app php artisan optimize:clear
docker exec laravel_admin_node npm run build
docker exec laravel_admin_node npm update


find . -type f -name "*:Zone.Identifier" -delete



sudo chown -R $USER:$USER storage
sudo chown -R $USER:$USER database
sudo chown -R $USER:$USER bootstrap/cache


sudo chmod -R 775 storage
sudo chmod -R 775 database
sudo chmod -R 775 bootstrap/cache


docker exec -it laravel_admin_app ls -l /var/www/html

docker exec laravel_admin_app chown -R www-data:www-data /var/www/html/database
docker exec laravel_admin_app chmod -R 775 /var/www/html/database


docker exec laravel_admin_app chown -R www-data:www-data /var/www/html/storage
docker exec laravel_admin_app chmod -R 775 /var/www/html/storage

docker exec laravel_admin_app chown -R www-data:www-data /var/www/html/bootstrap/cache
docker exec laravel_admin_app chmod -R 775 /var/www/html/bootstrap/cache


sudo chown -R $(whoami):$(whoami) .
sudo chmod -R 777 myapp/storage myapp/bootstrap/cache myapp/database                                                     


| Service     | URL                                            |
| ----------- | ---------------------------------------------- |
| Laravel     | [http://localhost:8000](http://localhost:8000) |
| Vite (Node) | [http://localhost:5173](http://localhost:5173) |
| Redis UI    | [http://localhost:8081](http://localhost:8081) |
| Mongo UI    | [http://localhost:8082](http://localhost:8082) |
| pgAdmin     | [http://localhost:8083](http://localhost:8083) |
| phpMyAdmin  | [http://localhost:8084](http://localhost:8084) |
