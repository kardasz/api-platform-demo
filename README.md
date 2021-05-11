# Api Platform Demo Project

* API Platform 

## Documentation

* https://api-platform.com/docs
* https://symfony.com/doc/current/index.html

## Requirements

* PHP 7.4
* MySQL 5.7+
* Docker

## Installation

### Setup database

- `bin/console doctrine:database:create --if-not-exists` - create database
- `bin/console doctrine:migrations:migrate -n` - migrate database
- `bin/console hautelook:fixtures:load -n` - load fixtures

### Create user

### Oauth2 setup

- https://github.com/trikoder/oauth2-bundle/blob/v3.x/docs/basic-setup.md

### Create keys

https://oauth2.thephpleague.com/installation/#generating-public-and-private-keys

- `openssl genrsa -out private.key 2048` - create private key
- `base64 -i private.key` - base64 encode private key
- `OAUTH2_PRIVATE_KEY_KEY=...` - add base64 encoded private key env
- `openssl rsa -in private.key -pubout -out public.key` - create public key
- `base64 -i public.key` - base64 encode public key
- `OAUTH2_PUBLIC_KEY=...` - add base64 encoded public key env

### Create client id and secret

- `bin/console trikoder:oauth2:create-client --grant-type client_credentials --grant-type password -- frontend`

### Provide Oauth2 client id and secret env variables

Client id and client secret will be used as default when none provided with the request `POST /api/v1/oauth2/token`

- `OAUTH2_CLIENT_ID=`
- `OAUTH2_CLIENT_SECRET=`

### Test client credentials

```sh
curl -i -X "POST" "http://127.0.0.1:8000/api/v1/oauth2/token" \
	-H "Content-Type: application/x-www-form-urlencoded" \
	-H "Accept: 1.0" \
	--data-urlencode "grant_type=client_credentials" \
	--data-urlencode "client_id=frontend" \
	--data-urlencode "client_secret=6255841cecc4189ca80f7c2a911bd465c76131069e88a2269140628bbc5b11e6d13e66ff041f4175052939292b20e6bb0acdb3b04203fcfdb6cf2beb147baecf"
```

### Test password credentials

```sh
curl -i -X "POST" "http://127.0.0.1:8000/api/v1/oauth2/token" \
	-H "Content-Type: application/x-www-form-urlencoded" \
	-H "Accept: 1.0" \
	--data-urlencode "grant_type=password" \
	--data-urlencode "client_id=frontend" \
	--data-urlencode "client_secret=6255841cecc4189ca80f7c2a911bd465c76131069e88a2269140628bbc5b11e6d13e66ff041f4175052939292b20e6bb0acdb3b04203fcfdb6cf2beb147baecf" \
	--data-urlencode "username=user@example.com" \
	--data-urlencode "password=mySe.ret123!"
```

## Testing

* `composer run-script phpunit` run test local
* Bitbucket pipelines used for running automated tests
* https://github.com/lchrusciel/ApiTestCase - helpful library for unit testing
* https://github.com/coduo/php-matcher - json assertions
* https://github.com/nelmio/alice - fixtures
* https://github.com/fzaninotto/Faker - fake data

## Notes

### How to create ApiPlatform project?

* Create symfony project `symfony new ApiPlatformDemo`
* Go into dir `cd ApiPlatformDemo`
* Install ApiPlatform `composer req api`
