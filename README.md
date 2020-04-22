# C11K Service and Goods

[![pipeline status](https://gitlab.com/c11k/serviceandgoods/badges/master/pipeline.svg)](https://gitlab.com/c11k/serviceandgoods/-/commits/master)
[![coverage report](https://gitlab.com/c11k/serviceandgoods/badges/master/coverage.svg)](https://gitlab.com/c11k/serviceandgoods/-/commits/master)
This is a bespoke application for the keeping track of inventory from a company that
deals with a Service and Goods. This is for use with php7.4 and uses node:13-slim
to compile the front end assets.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Docker Usage](#docker-usage)
- [Support](#support)
- [Contributing](#contributing)

## Installation
To build your awesome package, fork this repository and clone your fork. You may
alternatively choose to download the files and incorporate all or some of them
into your existing repository.

Files need updating, so let's walk through each set of files. Each file may need
further updating depending on your exact situation.

### Deployment
On your deployment server:
* Give your deployment user passwordless login:
```bash
cat ~/.ssh/id_ed25519.pub >> ~/.ssh/authorized_keys
cat ~/.ssh/id_ed25519
```
You're still not using RSA keys, are you?
[[*]](https://blog.g3rt.nl/upgrade-your-ssh-keys.html)
```bash
ssh-keygen -o -a 100 -t ed25519
```

* Copy your deployment user's *private key* to your clipboard. In your GitLab
project, go to **Settings** > **CI/CD** and expand **Secret variables**. Paste
the ssh_key as an Input variable value, and set the key to `SSH_PRIVATE_KEY` and
save the variables.

  This allows the CD system to open an `ssh` connection to your server and push
  the updated repository.
  
* Copy your deployment user's *public key* to your clipboard. In your Gitlab
project, go to **Settings** > **Repository** and expand **Deploy Keys**. Paste
the public key in the *Key* field, and give it a name. Remember to **Add Key**.

* Give your deployment user `chmod` and `chgrp` permissions. This potentially
opens your server to an attack vector through the `Envoy.blade.php` file.
```
sudo visudo -f /etc/sudoers.d/deployment_user_chmod_chown
###
# Enter this in the editor, substituting <<deploy_user>> with the user's actual
# login name:
###

<<deploy_user>> ALL = (root) NOPASSWD: /bin/chmod
<<deploy_user>> ALL = (root) NOPASSWD: /bin/chgrp

```
If your deployment environment does not require changing directory permissions so
the web-server can write to the file system, you should skip this step.

* Open `Envoy.blade.php` and change the username in the first line, the repository
in the 3rd line, and the app_dir in the 4th line. Change or modify any of the
tasks to fit your deployment steps.
[Envoy documentation](https://laravel.com/docs/5.6/envoy#writing-tasks). 

* Set your webserver's root directory to `$app_dir\current\public`.
* As your deployment user, pull the git repository from GitLab manually. This
will make sure that your public key was copied correctly into the deployment key,
and it will allow you to check the certificate of the GitLab server and add
GitLab.com to the user's `~/.ssh/known_hosts`.


### GitLab CI/CD
+ Open the `.gitlab-ci.yml` file. Add any testing tasks you may need. If you
require a database for testing, add this to the testing section. `services`
must be vertically aligned with `stage`. Update any database variables in this
file to match any changes to your configuration or environment.
```
  services:
    - mysql:5.7
```
Update the `url` key in the file to point to your deployment url. This will
appear in the GitLab deployment screen.
+ If this project does not need a deployment web server, you are free to remove
the entire `deploy_production:` stanza. When you are ready to auto-deploy,
change
```
  except:
    - pushes
```
to
```
  only:
    - master
```
This will auto-deploy when a master branch is pushed and passes tests.

### Web Development Environment
+ Open `phpdocker/php-fpm/Dockerfile` and change the `from:` image to the image
you just pushed to the repository.
+ Build your web server development environment:
```bash
docker-compose build
docker-compose up -d
```
+ Your web browser at `http://localhost:8080` will point to your `public/`
directory (not included).

### Usage
Unless you're me, make sure to change the namespace in each of the files to your
own, and update composer.json with your own namespace and author information.

Either use the c11k class or change the name. Make sure to change the name of
the test to match.

Replace the contents of `README.md`.

Once you're all changed up, run

```bash
composer install
```
## Docker Usage

From the root project directory, run `make docker` to start the docker
containers. Give them a moment to first-run build and start.

Make sure you run `composer install` from the docker container to use the most
up to date available version of PHP (7.2.5 as of this writing). Inside the
docker container, you are root, so composer will squawk about your being root. 

* Shell into the PHP container, `docker-compose exec php-fpm bash`
* Run tests in the PHP container, `docker-compose exec php-fpm vendor/bin/phpunit`
* Open a mysql shell, `docker-compose exec mysql mysql -uroot -prootpw`
* Reset a database using propel, `docker-compose exec php-fpm vendor/bin/propel sql:insert`

Read the Makefile for some pre-defined commands.

Add a `/public/index.php` , and it will be visible through your browser at
http://localhost:8080/

Interact with MySQL server docker with
`mysql -u root -prootpw -h 127.0.0.1 -P 13306` or use a desktop application. 
Note that you must connect to `127.0.0.1` instead of `localhost`.

## PHPStorm
Open your Preferences (⌘,). Under **Languages & Frameworks** > **PHP**, hit the
three dots next to the CLI Interpreter. On the next window, press the + icon,
and select **From Docker, Vagrant, VM, Remote&hellip;** In the next window,
select **Docker** and the image and PHP Interpreter path should autofill.
Press OK and if you want, change the name to reflect your project name, then
press OK twice to exit preferences.

## Support

Please [open an issue](https://gitlab.com/c11k/c11k/issues/new) for support.

## Contributing

Please contribute using
[Gitlab Flow](https://docs.gitlab.com/ee/workflow/gitlab_flow.html). Create a
branch, add commits, and
[open a pull request](https://gitlab.com/c11k/c11k/merge_requests/new).
