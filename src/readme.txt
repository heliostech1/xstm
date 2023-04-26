

ระบบ xstm 
server framework 
Laravel 8.x
PHP 7.4
Nginx 1.17
MongoDB 4.4

client framework  
jquery , datatable


====================================================================
> UP TO TEST SERVER
host: 203.154.91.122, port: 8089 , User: ctl Password: ctl@heliostech 
cd  /home/ctl/docker-ctl
sudo git pull

Password for 'ctl': ctl@heliostech
Username for 'https://github.com': WeerachartP
Password for 'https://WeerachartP@github.com': .....

> CONNECT DATABASE TEST 
host: 203.154.91.122, port: 27018 , User: ctl Password: ctl@heliostech  

> CONFIG PROJECT FILE
host: 203.154.91.122, port: 22 

cd  /home/ctl/docker-ctl/src
sudo chmod -R 777 storage 
sudo chmod -R 777 bootstrap 



=======================================================================
> JWT (Api authorize)
  https://github.com/tymondesigns/jwt-auth/wiki/Authentication

> DomPdf
  https://github.com/barryvdh/laravel-dompdf
  https://github.com/dompdf/dompdf/wiki/Usage


============================================================
> SET UP CRONTAB

crontab -e  // แก้ไขไฟล์ crontab ระบุ config

* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
* * * * * cd /home/weerachart/devel/xstm/latest && php artisan schedule:run >> /dev/null 2>&1

sudo /etc/init.d/cron restart

php artisan send_alarm test

====================================================================
> SETUP LIBRARY


sudo apt-get install php7.4-zip
sudo apt-get install php7.4-bcmath
sudo apt-get install php7.4-gd
sqlsrv

sudo service nginx restart


====================================================================
> ADDITIONAL VENDER INSTALL

composer update phpoffice/phpspreadsheet
composer update barryvdh/laravel-dompdf



