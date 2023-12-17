# Payment Integration Hub
It is an app that connects e-commerce platforms (Cafe24, Shopify,..) with Payment Service Providers

## Installation

The whole application consists of three services: a nginx server, an app and a database mysql.
These services are managed via docker-compose and can be installed and run by the following steps:

```console
# clone the repository
$ git clone git@github.com:dinhphien/payment-integration-hub.git

$ cd payment-integration-hub/

# install dependencies and start the whole application
$ make install
$ make startup
```

## Testing
There is a postman collection in postman folder which can be imported into Postman application,
so you can test this application via Postman.

You can run unit tests and feature tests via make tests target to make sure everything works.

## Architecture
The app service is built using Laravel framework with MVC architectural pattern.
An additional layer between model and controller was added using Command/CommandHandler pattern to reflect use cases of
the application.

## Quality tools
To ensure quality code of this project, Pint and Phpstan are used for code style fixer and static code analysis.
