<?php

namespace Deployer;

use Deployer\Utility\Httpie;

require 'recipe/common.php';
require 'contrib/slack.php';

/**
 * Variables
 */
set('allow_anonymous_stats', false);

set('application', 'Craft CMS Starter');
set('repository', 'git@github.com:diginov/craft.git');

set('bin/php', '/RunCloud/Packages/php82rc/bin/php');

set('shared_files', [
    '.env',
]);

set('shared_dirs', [
    'storage',
    'web/files',
]);

$datetime = date('Ymd-His');

set('db_file', $datetime.'.sql');
set('db_local', 'db');

set('backup_folder', 'storage/backups/deployer');
set('project_folder', 'project-'.$datetime);

set('slack_title', 'Craft CMS Starter');
set('slack_webhook', '');

/**
 * Hosts
 */
host('staging')
    ->set('hostname', 'my-craft-project.dev')
    ->set('remote_user', 'my-craft-project')
    ->set('deploy_path', '~/webapps/my-craft-project')
    ->set('branch', 'develop')
    ->set('app_folder', 'my-craft-project')
    ->set('db_remote', 'my-craft-project')
    ->set('keep_releases', 2)
    ->set('allow_push', true);

host('production')
    ->set('hostname', 'my-craft-project.com')
    ->set('remote_user', 'my-craft-project')
    ->set('deploy_path', '~/webapps/my-craft-project')
    ->set('branch', 'master')
    ->set('app_folder', 'my-craft-project')
    ->set('db_remote', 'my-craft-project')
    ->set('keep_releases', 10)
    ->set('allow_push', false);

/**
 * MySQL Dump
 *
 * @param string $file
 * @param bool $runLocally
 */
function mysqldump(string $file, bool $runLocally = false): void
{
    set('db_name', $runLocally ? get('db_local') : get('db_remote'));

    $defaultArgs = [
        '--add-drop-table',
        '--comments',
        '--create-options',
        '--dump-date',
        '--no-autocommit',
        '--routines',
        '--default-character-set=utf8',
        '--set-charset',
        '--triggers',
        '--no-tablespaces',
    ];

    $ignoreTableArgs = [
        '--ignore-table={{db_name}}.assetindexdata',
        '--ignore-table={{db_name}}.assettransformindex',
        '--ignore-table={{db_name}}.sessions',
        '--ignore-table={{db_name}}.templatecaches',
        '--ignore-table={{db_name}}.templatecachequeries',
        '--ignore-table={{db_name}}.templatecacheelements',
    ];

    $schemaDump = 'mysqldump' .
        ' ' . implode(' ', $defaultArgs) .
        ' --single-transaction' .
        ' --no-data' .
        ' {{db_name}} > ' . $file;

    $dataDump = 'mysqldump' .
        ' ' . implode(' ', $defaultArgs) .
        ' --no-create-info' .
        ' ' . implode(' ', $ignoreTableArgs) .
        ' {{db_name}} >> ' . $file;

    if ($runLocally) {
        runLocally($schemaDump . ' && ' . $dataDump);
    } else {
        run($schemaDump . ' && ' . $dataDump);
    }
}

/**
 * Slack Notification
 *
 * @param string $message
 * @param string|null $color
 */
function notify(string $message, string $color = null): void
{
    if (!get('slack_webhook', false)) {
        return;
    }

    $attachment = [
        'title' => get('slack_title'),
        'text' => parse($message),
        'color' => $color,
        'mrkdwn_in' => ['text'],
    ];

    Httpie::post(get('slack_webhook'))->jsonBody(['attachments' => [ $attachment ]])->send();
}

/**
 * Database tasks
 */
desc('Backup database');
task('db:backup', function() {
    if (test('[ -d {{deploy_path}}/current ]')) {
        mysqldump('{{current_path}}/backup.sql');
        run('gzip -f {{current_path}}/backup.sql');
    }
})->once();

desc('Rollback database');
task('db:rollback', function() {
    if (test('[ -f {{current_path}}/backup.sql.gz ]')) {
        runLocally('mkdir -p {{backup_folder}}');

        writeln('➤ Download {{alias}} database to {{alias}}-{{db_file}}');
        mysqldump('{{current_path}}/{{alias}}-{{db_file}}');
        download('{{current_path}}/{{alias}}-{{db_file}}', '{{backup_folder}}/{{alias}}-{{db_file}}');
        run('rm -f {{current_path}}/{{alias}}-{{db_file}}');

        writeln('➤ Rollback {{alias}} database from previous release');
        run('gzip -d {{current_path}}/backup.sql.gz');
        run('mysql {{db_remote}} < {{current_path}}/backup.sql');
        run('rm -f {{current_path}}/backup.sql');
    } else {
        writeln('➤ No database backup available in previous release');
    }
})->once();

/**
 * Craft tasks
 */
desc('Apply migrations and project config');
task('craft:up', function() {
    run('{{bin/php}} {{release_path}}/craft up --force --interactive=0');
})->once();

desc('Clear all caches');
task('craft:clear', function() {
    run('{{bin/php}} {{release_path}}/craft invalidate-tags/template --interactive=0');
    run('{{bin/php}} {{release_path}}/craft clear-caches/compiled-templates --interactive=0');
    run('{{bin/php}} {{release_path}}/craft clear-caches/temp-files --interactive=0');
    run('{{bin/php}} {{release_path}}/craft clear-caches/data --interactive=0');
    run('{{bin/php}} {{release_path}}/craft clear-caches/seomate-cache --interactive=0');
})->once();

desc('Restart queue listener');
task('craft:queue:restart', function() {
    run('for pid in $(ps -ef | awk \'/'.get('app_folder').'\/current\/craft queue\/listen/ {print $2}\'); do kill -1 $pid; done');
})->once();

/**
 * Asset tasks
 */
desc('Build assets');
task('build:assets', function() {
    if (!testLocally('[ -d node_modules ]')) {
        runLocally('npm install --no-package-lock');
    }

    runLocally('npm run production');
    upload('web/assets', '{{release_path}}/web/.');
})->once();

/**
 * Pull tasks
 */
desc('Pull project');
task('pull:project', function() {
    if (askConfirmation('Pull config/project folder from {{alias}} server ?')) {
        notify('_{{user}}_ pull `project config` from *{{alias}}*');

        runLocally('mkdir -p {{backup_folder}}/local-{{project_folder}}');
        runLocally('mkdir -p {{backup_folder}}/{{alias}}-{{project_folder}}');

        writeln('➤ Backup local config/project folder to local-{{project_folder}}');
        runLocally('cp -R config/project/* {{backup_folder}}/local-{{project_folder}}');

        writeln('➤ Download {{alias}} config/project folder to {{alias}}-{{project_folder}}');
        download('{{current_path}}/config/project/*', '{{backup_folder}}/{{alias}}-{{project_folder}}');

        if (askConfirmation('Overwrite local config/project folder ?')) {
            writeln('➤ Overwrite local config/project folder with {{alias}} config/project folder');
            runLocally('rm -rf config/project/*');
            runLocally('cp -R {{backup_folder}}/{{alias}}-{{project_folder}}/* config/project');
        }
    }
})->once();

desc('Pull database');
task('pull:db', function() {
    if (askConfirmation('Pull database from {{alias}} server ?')) {
        notify('_{{user}}_ pull `database` from *{{alias}}*');

        runLocally('mkdir -p {{backup_folder}}');

        writeln('➤ Backup local database to local-{{db_file}}');
        mysqldump('{{backup_folder}}/local-{{db_file}}', true);

        writeln('➤ Download {{alias}} database to {{alias}}-{{db_file}}');
        mysqldump('{{current_path}}/{{alias}}-{{db_file}}');
        download('{{current_path}}/{{alias}}-{{db_file}}', '{{backup_folder}}/{{alias}}-{{db_file}}');
        run('rm -f {{current_path}}/{{alias}}-{{db_file}}');

        writeln('➤ Import downloaded {{alias}} database to local database');
        runLocally('mysql {{db_local}} < {{backup_folder}}/{{alias}}-{{db_file}}');
    }
})->once();

desc('Pull files');
task('pull:files', function() {
    if (askConfirmation('Pull files from {{alias}} server ?')) {
        notify('_{{user}}_ pull `files` from *{{alias}}*');

        writeln('➤ Download {{alias}} files to local folder');
        download('{{deploy_path}}/shared/web/files/', 'web/files/', [
            'options' => [
                '--delete',
                '--size-only',
                '--exclude="*/_*"',
                '--exclude=".DS_Store"',
            ],
        ]);
    }
})->once();

desc('Pull database and files');
task('pull', [
    'pull:db',
    'pull:files',
])->once();

/**
 * Push tasks
 */
desc('Push database');
task('push:db', function() {
    if (get('allow_push') === true) {
        if (askConfirmation('Push database to {{alias}} server ?')) {
            notify('_{{user}}_ push `database` to *{{alias}}*');

            runLocally('mkdir -p {{backup_folder}}');

            writeln('➤ Backup {{alias}} database to {{alias}}-{{db_file}}');
            mysqldump('{{current_path}}/{{alias}}-{{db_file}}');
            download('{{current_path}}/{{alias}}-{{db_file}}', '{{backup_folder}}/{{alias}}-{{db_file}}');
            run('rm -f {{current_path}}/{{alias}}-{{db_file}}');

            writeln('➤ Upload local database to local-{{db_file}}');
            mysqldump('{{backup_folder}}/local-{{db_file}}', true);
            upload('{{backup_folder}}/local-{{db_file}}', '{{current_path}}/local-{{db_file}}');
            runLocally('rm -f {{backup_folder}}/local-{{db_file}}');

            writeln('➤ Import uploaded local database to {{alias}} database');
            run('mysql {{db_remote}} < {{current_path}}/local-{{db_file}}');
            run('rm -f {{current_path}}/local-{{db_file}}');
        }
    } else {
        writeln('➤ Push database to {{alias}} is not allowed');
    }
})->once();

desc('Push files');
task('push:files', function() {
    if (get('allow_push') === true) {
        if (askConfirmation('Push files to {{alias}} server ?')) {
            notify('_{{user}}_ push `files` to *{{alias}}*');

            writeln('➤ Upload local files to {{alias}} folder');
            upload('web/files/', '{{deploy_path}}/shared/web/files/', [
                'options' => [
                    '--delete',
                    '--size-only',
                    '--exclude="*/_*"',
                    '--exclude=".DS_Store"',
                ],
            ]);
        }
    } else {
        writeln('➤ Push files to {{alias}} is not allowed');
    }
})->once();

Deployer::get()->tasks->remove('push');

desc('Push database and files');
task('push', [
    'push:db',
    'push:files',
])->once();

/**
 * Deploy tasks
 */
desc('Deploy your project');
task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:publish',
]);

// Before notifications
before('deploy', 'slack:notify');
before('rollback', 'slack:notify');

// Backup database after lock
after('deploy:lock', 'db:backup');                  // Do not run on first deployment or without database

// Craft migrate and caches clear
after('deploy:vendors', 'craft:up');                // Do not run on first deployment or without database
after('deploy:vendors', 'craft:clear');             // Do not run on first deployment or without database

// Compile css and js assets
after('deploy:vendors', 'build:assets');

// Restart queue listener
after('deploy:symlink', 'craft:queue:restart');

// Unlock on failed deployment
after('deploy:failed', 'deploy:unlock');

// Rollback database and symlink
after('rollback', 'db:rollback');

// After notifications
after('deploy:success', 'slack:notify:success');
after('deploy:failed', 'slack:notify:failure');
after('rollback', 'slack:notify:success');
