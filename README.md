# Timetables

**Timetables** is an application offering filtered views from Google Calendars (teachers, classes, rooms, etc.). Another feature is the capacity to audit the amount of training hours for a teacher during a given module.

In Passerelles numériques, we are using Google Calendar to schedule our classes. **Timetables** allows us to offer a simplified view to our teachers about their weekly schedules.

## Backup

Please note that this application doesn't use a database. It relies on few JSON files as we don't frequently update the information. These files are stored into `storage/db`.

## Deployment

### Prerequisites

You need the following software:
* PHP CLI for Composer and the optional artisan commands.
* Git to clone this repository.
* NodeJS so as to use NPM or yarn package manager.
* Composer so as to install PHP 3rd party libraries

### Instructions

Deployment on a webserver:

Imagine that you clone this repository into /var/www/timetables/
Then, the Frontend location folder will be built and available at /var/www/timetables/public/
This is this folder you must make available by your webserver.

Additional steps are required in order to deploy the application.

1. Go at the root of the deployment folder (e.g. /var/www/timetables/).
2. Execute the command `composer install --no-dev` so as to install PHP dependencies.
3. Execute the command `npm install` so as to install JavaScript dependencies.
4. Execute the command `npm run build` so as to build the User Interface.
5. Copy `.env-example` to `.env` and edit the file in order to change the Google API key and Timezone.


### Webserver

In addition to the default configuration needed to execute the PHP scripts, make sure that the webserver has the following configuration:

 - Module rewrite enabled.
 - Write access granted to `storage/db` folder.

#### Nginx Sample 

    # Redirect HTTP traffic to HTTPS
    #server {
    #    listen 80;
    #    listen [::]:80;
    #    server_name timetables.pnc.passerellesnumeriques.org;
    #    return 301 https://$host$request_uri;
    #}

    server {
        listen 443 ssl http2;
        listen [::]:443 ssl http2;
        server_name timetables.pnc.passerellesnumeriques.org;
        root /var/www/pnc-timetables/public;
        index index.php;

        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }

        # pass PHP scripts to FastCGI server
        location ~ \.php$ {
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/var/run/php/php7.2-fpm.sock;
            fastcgi_split_path_info ^(.+\.php)(/.+)$;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            fastcgi_param HTTP_AUTHORIZATION $http_authorization;
        }

        # deny access to .htaccess files
        location ~ /\.ht {
            deny all;
        }

        #SSL certificates here
    }


### Configuration of backend

Admin users and passwords are defined into `storage/db/admin.json`

## Troubleshooting

cURL error 60: SSL certificate problem: unable to get local issuer certificate (see http://curl.haxx.se/libcurl/c/libcurl-errors.html)

Download the latest certificates file https://curl.haxx.se/docs/caextract.html
Edit php.ini and modify the line below so as to match the location of cacert.pem file:

   openssl.cafile=/etc/ssl/certs/cacert.pem

## TODO

 - [X] Sort the lessons by alphabetic order in Statistics page.
 - [X] Sort the teachers by alphabetic order in Calendar pages.
 - [X] Make use of vue-bootstrap-material; clean up CSS.
 - [ ] Upgrade security. As of today, authenticated queries send password in clear on the wire.
 - [ ] Authentication by Google OAuth2 or SAML (total or only for admin pages).
 - [ ] Switch UI to vue-bootstrap. See: https://bootstrap-vue.js.org/
 - [ ] i18n. See: https://kazupon.github.io/vue-i18n/
 - [X] BUG: Print feature of FullCalendar is missing styles since alpha2, see: https://github.com/fullcalendar/fullcalendar/issues/4437
 - [X] BUG: Print feature of FullCalendar doesn't sort events since alpha2, see: https://github.com/fullcalendar/fullcalendar/issues/4432