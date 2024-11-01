# Ada System

> Laravel + Vuejs

[![PHP version](https://badge.fury.io/ph/adenvt%2Flaravel-coreui-vue.svg)](https://badge.fury.io/ph/adenvt%2Flaravel-coreui-vue)
[![Build Status](https://travis-ci.com/adenvt/laravel-coreui-vue.svg?branch=master)](https://travis-ci.com/adenvt/laravel-coreui-vue) [![Greenkeeper badge](https://badges.greenkeeper.io/adenvt/laravel-coreui-vue.svg)](https://greenkeeper.io/)

## What's inside
* [Laravel][laravel] 5.7, A PHP framework for web artisans
* [Core UI][coreui] for Vue, Pro Bootstrap Admin Template
* Usefull library: [Axios][axios], [jQuery][jquery], [Moment.js][moment], [Lodash][lodash]
* [Vue Router][vue-router] and [Vuex][vuex], set out of the box
* PWA ready, powered by [Offline-plugin][offline-plugin] and [Workbox][workbox]
* Notification using [Vue-SweatAlert2][vue-sweatalert2] and [Vue-Notification][vue-notification]
* Loading spinner with [Vue Loading Spinner][vue-loading-spinner]
* Quick deployment with [Docker Compose][docker-compose]

## Requirement
* **PHP** >= 7.1.3
* OpenSSL PHP Extension
* PDO PHP Extension
* Mbstring PHP Extension
* Tokenizer PHP Extension
* XML PHP Extension
* Ctype PHP Extension
* JSON PHP Extension
* **Node** >= 8.9.4
* **NPM** >= 5.6.0
* For Ubuntu, require `apt-get install libpng16-dev`, [see](https://github.com/imagemin/imagemin-mozjpeg/issues/28)

## How to Install
* Create new project directory or clone resource from git then
```bash
cd project_name
```
* Install using composer (doesn't need to clone)
```bash
composer install
```
* Install Dependencies
```bash
npm i -f
```
* Add write permission (Unix)
```bash
chmod -R go+w storage bootstrap/cache
```
* Compile Static Asset
```bash
## for Development
npm run dev

## for Development with auto compile every change
npm run watch

## for Development with HMR (Hot Module Replacement)
npm run hot

## for Production
npm run prod

## for auto Deployment
npm run build
```

## Using Docker Compose

### For Development
* Create and start Container
```bash
docker-compose up -d dev
```

* Enter workspace
```bash
docker-compose exec dev bash
```

* Install Depencies
```
composer install
npm install
```

* Compile Static Asset
```bash
## Single run compile
npm run dev

## or watch and compile every change
npm run watch

## or using Hot Module Replacement
npm run hot

## Generate application key
php artisan key:generate

## Run application PHP server
php artisan serve --port=80
```
* Open browser, goto `http://localhost`

### For Production
* Create and start Container
```
docker-compose up -d prod
```

* Open browser, goto [http://localhost](http://localhost)

## License
This project is licensed for CMS Education of eGroup, all right reserved.

[laravel]: https://laravel.com
[coreui]: https://coreui.io
[axios]: https://github.com/axios/axios
[jquery]: https://jquery.com/
[lodash]: https://lodash.com/
[moment]: https://momentjs.com/
[vue-router]: https://router.vuejs.org/
[vuex]: https://vuex.vuejs.org/
[vue-sweatalert2]: https://github.com/avil13/vue-sweetalert2
[vue-notification]: http://vue-notification.yev.io/
[vue-loading-spinner]: https://nguyenvanduocit.github.io/vue-loading-spinner/
[docker-compose]: https://docs.docker.com/compose/
[offline-plugin]: https://github.com/NekR/offline-plugin
[workbox]: https://developers.google.com/web/tools/workbox/
