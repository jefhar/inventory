# Inventory System

[Requires](https://github.com/jefhar/inventory/blob/master/composer.json) PHP >= 7.4.

This is the master-release code base for a warehouse inventory application. The application is still in 
active development (February 2020). As releases are updated on the master branch, they will be pushed to 
this master-release repo. A live demo version of this release is at https://inventory.jeffharris.us.


## Table of Contents

- [Installation](#installation)
- [Usage](#usage)

## Installation
To build your awesome package, optionally fork this repository and clone your
fork.

You will need: 
- PHP >= 7.4
- Database server
- Web server
- Optionally a redis server and an SMTP server.

Enter your favorite terminal and run:
```console
$ git clone https://github.com/jefhar/inventory.git
$ cd inventory
$ cp .env.example .env
$ nano .env # Fill in your secrets. See https://laravel.com/docs/6.x/configuration#environment-configuration
$ composer install
$ php artisan key:generate
$ yarn install
$ npm run prod
$ php artisan migrate

# Enter a tinker shell and interact directly with the framework to create your Super Admin user.
$ php artisan tinker
>>> $admin = new User();
>>> $admin->assignRole(UserRoles::SUPER_ADMIN);
>>> $admin->email = 'superAdmin@example.com'
>>> $admin->name = 'Super Admin'
>>> $admin->password = Hash::make('correct-horse-battery-staple'); # Use a better password, though.
# You need to assign the role, update the name and password before saving.
>>> $admin->save();
>>> exit
```
Point your webserver to `inventory/public`, or run `php artisan serve`. Visit
your webserver and login as the user.

At some point, you will probably want to update `./resources/views/welcome.blade.php`
to build your own front page.

## Usage
Once installed, visit your webpage and login with permissions. As the Admin
user, you can create other users with specific permissions. The other types are
`Owner`, `Technician`, `Sales Rep`, and `Employee`. By Default, they can each do
permissions in the table that will be here and updated after each iteration.

Super Admin is designed to create Owners, and Owners are designed to add employees
in one of three categories: Technician, Sales Rep, and Employees.

The Technician users are designed to add incoming inventory to the system. The
technician (usually) knows the difference between a RAID and an HBA controller and
should be adding inventory.

The Sales Rep users are designed to see the inventory, and can make small changes
to existing inventory. They can also create long-term shopping carts for their
customers and clients.

The Employee is simply a gate for future expansion to keep unauthorized people out.
In the future, if a specific vendor needs access, the user could be given an account,
but without the Employee role, and the vendor will only have view access, just as if
the vendor came to the shop and was looking through the gate.
