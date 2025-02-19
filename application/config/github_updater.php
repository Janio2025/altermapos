<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * The user name of the git hub user who owns the repo
 */
$config['github_user'] = 'Janio2025';

/**
 * The repo on GitHub we will be updating from
 */
$config['github_repo'] = 'altermapos';

/**
 * The branch to update from
 */
$config['github_branch'] = 'main';

/**
 * The current commit the files are on.
 *
 * NOTE: You should only need to set this initially it will be
 * automatically set by the library after subsequent updates.
 */
$config['current_commit'] = '2d0ca543aae10a5e05867c4644fd6d8601ab27cc';

/**
 * A list of files or folders to never perform an update on.
 * Not specifying a relative path from the webroot will apply
 * the ignore to any files with a matching segment.
 *
 * I.E. Specifying 'admin' as an ignore will ignore
 * 'application/controllers/admin.php'
 * 'application/views/admin/test.php'
 * and any other path with the term 'admin' in it.
 */
$config['ignored_files'] = [];

/**
 * Flag to indicate if the downloaded and extracted update files
 * should be removed
 */
$config['clean_update_files'] = true;