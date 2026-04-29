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

docker exec -it laravel_admin_app supervisorctl -s unix:///tmp/supervisor.sock status


root@72.61.174.220

scp docker.zip root@72.61.174.220:/var/jv
#############################
Already Product i Cart 
Order time date

http://localhost/contact-us
http://localhost:5540/
http://localhost:8080/browser/

#####################################
mac mini m4 24-512
--1500 INR
usb hub -- 8000-14000 INR
monitor benq 27" or 32"
webcam --



Logitech MK215

Mac mini m4 24-512
UGREEN Revodok USB-C Hub 6-in-1
BenQ GW2790Q
logitech brio 100 webcam
Keychron K2 Wireless Mechanical Keyboard //Dell KM3322W Wireless Combo 


Logitech MX Master 3S//Logitech MX Master 5S



Item	Cost
Mac mini M4	₹99,900
BenQ 27” Monitor	₹15,000
Keychron K2	₹7,000
Total	₹1,21,900 (~₹1.22L)



3c5e8c6dc8cf21
bed9afac3bc58c passs


Host

sandbox.smtp.mailtrap.io
Port

25, 465, 587 or 2525
Username

3c5e8c6dc8cf21

Auth

PLAIN, LOGIN and CRAM-MD5
TLS

Optional (STARTTLS on all ports)



Acer Nitro VG270U S3
LG 27U631A-B (QHD)
MSI PRO MP275Q



Acer Nitro XV272U V3 (27-inch IPS, 180Hz, QHD)
LG 27U631A-B (27-inch QHD IPS)
ViewSonic VA2708-2K-MHD 27" QHD Monitor
benq2790q


https://pricehistoryapp.com/product/dell-s2725qc-27-68-58-cm-usb-c-monitor-titan-grey-ips-120hz-350-cd-m2-4-ms-contrast-1500-1-tilt-height-pivot-swivel-adjustable-ports-2x-hdm
https://pricehistoryapp.com/product/benq-ew2790u-68-58-cm-27-inch-4k-ultra-hd-led-backlit-ips-panel-with-95-p3-ai-contrast-5wx2-trevolo-speakers-usb-type-c-65w-pd-3xhdmi-usb-hu


benq ew2790u
Dell S2722QC
Dell S2725QC
LG 27UP650N


benq2790q
benq 3290q
benq 3290qt