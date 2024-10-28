# Team Project Group 07

This is the main repository for the team group part 3 project 2023-2024.

## Resources for this Project

> [!WARNING]
> I would highly recommend reading most of these documents before you try and build the project. These are the essentials to getting started at development with Laravel.

-   [Laravel Directory Structure](https://laravel.com/docs/10.x/structure)
-   [Laravel Routing](https://laravel.com/docs/10.x/routing)
-   [Laravel Controllers](https://laravel.com/docs/10.x/controllers)
-   [Laravel Blade Templates](https://laravel.com/docs/10.x/blade)
-   [Laravel Views](https://laravel.com/docs/10.x/views)
-   [Laravel Middleware (more advanced)](https://laravel.com/docs/10.x/middleware)
-   [Laravel Pagination](https://laravel.com/docs/10.x/pagination)

## Installation Instructions

To install the project. You must first clone the repository.

```bash
git clone https://github.com/lborocs/team-projects-part-3-team-07
```

You will need [npm](https://docs.npmjs.com/downloading-and-installing-node-js-and-npm) and [composer](https://getcomposer.org/) installed on you machine to run these.

Then run the following commands to install dependencies.

```bash
npm install
composer install
```

Copy `.env.example` into a new file called `.env`

Please ask for the secret keys and necessary environment variables before you run.

Finally, generate a valid key for the project.

```bash
php artisan key:generate
```

## Execution Instructions

To run the project, you should only need one command

```bash
php artisan serve
```

This should open a server on http://localhost:8000

Navigate to http://localhost:8000

## Running with Laravel Sail

To run the project with Laravel Sail, you will need to have [Docker](https://www.docker.com/products/docker-desktop/) installed on your machine.

Then to install the correct files run

```bash
php artisan sail:install
```

To start the server run

```bash
./vendor/bin/sail up
```

> [!NOTE]
> This will start the server on http://localhost and not http://localhost:8000.

To run the server in the background, you can run

```bash
./vendor/bin/sail up -d
```

You may also need to run the vite development server to compile the frontend assets.
```bash
./vendor/bin/sail npm run dev
```

For Unix based machines (MacOS or Linux), you may come across a permission issue. It will complain that the permission has been denied to write to the log files. To fix this, you need to set the WWWUSER and WWWGROUP environment variables to your user and group id.

At the end of you .env file, add the following lines.

```bash
WWWUSER=1000 # or your user id
WWWGROUP=1000 # or your group id
```
