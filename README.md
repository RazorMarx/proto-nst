## Nephele Storage Prototype 
![alt text](public/favicon.png)

![alt text](nst.gif)
### Goal

This project is a prototype for a file storage application.
It's build with Laravel as Backend and Vue3 as Frontend
### Requirement

- PHP >= 8.2
- Composer
- Nodejs >= 16
- A database (MySQL >= 5.7 ...)

### Init

Clone or Download this repo and open a terminal in the root folder and run

```bash
composer install  #install php dependencies [/vendors]
npm init #install js dependencies [/node_modules]
cp .env.example .env  #Create a config file .env to be configure with DB creds

php artisan key:generate #Generate the APP_KEY in the .env file
php artisan migrate --seed #create the database and create the first user test@test.fr/password

npm run dev #Start the vite server for developpement
npm run build #Build prod file in /public/build folder
```

### Warning

During uploading, if the file sizes are greater than 5MB (/resources/js/Components/FileUploaderMultiple.vue 'chunksize'), you must modify the "upload_max_filesize" and "post_max_size" values in php.ini.