@servers(['web' => 'deploy@c11k.com'])

@setup
    $repository = 'git@gitlab.com:c11k/inventory.git';
    $app_dir = '/var/www/inventory';
    $releases_dir = $app_dir . '/releases';
    $release = date('YmdHis');
    $new_release_dir = $releases_dir . '/' . $release;
@endsetup

@story('deploy')
    clone_repository
    run_composer
    run_npm
    update_storage
    update_cache
    update_env
    migrate
    update_docroot
    optimize
@endstory

@task('clone_repository')
    echo 'Cloning repository'
    [ -d {{ $releases_dir }} ] || mkdir -p {{ $releases_dir }}
    git clone --depth 1 {{ $repository }} {{ $new_release_dir }}
@endtask

@task('run_composer')
    echo "Starting deployment ({{ $release }})"
    cd {{ $new_release_dir }}
    composer install --no-dev -q -o
@endtask

@task('optimize')
    echo "Optimizing Cache"
    cd {{ $new_release_dir }}
    php artisan optimize:clear
    php artisan config:cache
    php artisan route:cache
@endtask

@task('update_storage')
    echo "Linking storage directory."
    rm -rf {{ $new_release_dir }}/storage
    ln -nfs {{ $app_dir }}/storage {{ $new_release_dir }}/storage
    echo "Checking permissions on storage"
    sudo chgrp -R www-data {{ $new_release_dir }}/storage
@endtask

@task('update_cache')
    echo "Adding webserver write permission to bootstrap/cache."
    sudo chgrp -R www-data {{ $new_release_dir }}/bootstrap/cache
@endtask

@task('update_env')
    echo 'Linking .env file.'
    ln -nfs {{ $app_dir }}/.env {{ $new_release_dir }}/.env
@endtask

@task('migrate')
    echo 'Looking for new migrations.'
    cd {{ $new_release_dir }}
    php artisan migrate --force
@endtask

@task('update_docroot')
    echo 'Linking current release.'
    ln -nfs {{ $new_release_dir }} {{ $app_dir }}/current
@endtask

@task('passport_install')
    if [ ! -f {{ $new_release_dir }}/storage/oauth-public.key ]; then
        echo 'Installing Passport.'
        cd {{ $new_release_dir }}
        php artisan passport:install
    else
        echo 'Passport already installed.'
    fi
@endtask

@finished
    @slack($web_hook, 'inventory-log', ':robot_face: New deployment pushed to ' . $release_url)
@endfinished

@task('run_npm')
    echo 'Installing npm modules
    docker run --rm -v "$(pwd):/src" node:12-slim sh -c 'cd /src && yarn install && ls && npm run prod'
@endtask
