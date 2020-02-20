<?php

namespace Deployer;

require 'recipe/laravel.php';

set('application', 'jkwedu.net');
set('test_application', 'test.jkwedu.net');

set('bin/php', function () {
    return '/usr/local/php/bin/php';
});

set('bin/composer', function () {
    if (commandExist('composer')) {
        $composer = locateBinaryPath('composer');
    }
    if (empty($composer)) {
        run("cd {{release_path}} && curl -sS https://getcomposer.org/installer | {{bin/php}}");
        $composer = '{{release_path}}/composer.phar';
    }

    return '{{bin/php}} ' . $composer;
});

// Project repository
set('repository', 'git@code.aliyun.com:jkwedu/jkwedu-index.git');

set('composer_options',
    '{{composer_action}} --verbose --prefer-dist --no-progress --no-interaction --no-dev --optimize-autoloader --ignore-platform-reqs');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', TRUE);
set('writable_mode', 'chown');

set('keep_releases', 2);

// Shared files/dirs between deploys
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server
add('writable_dirs', []);

// Hosts
host('prod')->hostname('jkw.ltd')->user('www')->set('deploy_path', '/data/wwwroot/{{application}}')->stage('prod');
host('test')->hostname('jkw.ltd')->user('www')->set('deploy_path', '/data/wwwroot/{{test_application}}')->stage('staging');

// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new-front release.

before('deploy:symlink', 'artisan:migrate');

/**
 * Main task
 */
desc('Deploy your project');

task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:vendors',
    'deploy:writable',
    'artisan:storage:link',
    'artisan:view:cache',
    'artisan:config:cache',
    'artisan:optimize:clear',
    //'env:upload',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
]);

//task('deploy:upload', function () {
//    upload('build/', '{{release_path}}/public');
//});

desc('Upload .env file');
task('env:upload', function () {
    upload('.env.prod', '{{release_path}}/.env');
});

task('reload:php-fpm', function () {
    run('sudo /etc/init.d/php-fpm restart');
});

task('restart:supervisorctl', function () {
    //run('supervisorctl restart all');
});

desc('Composer dump autoload');
task('composer:dump:autoload', function () {
    run('cd {{release_path}} &&  composer dump-autoload');
});

task('build', [
    //'composer:dump:autoload',
    //'bower:install',
    //'npm:install',
    //'npm:run:production',
    'reload:php-fpm',
]);

after('deploy', 'build');
after('deploy', 'success');


