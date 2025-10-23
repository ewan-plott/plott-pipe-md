# Legacy Sites > Pipeline

*Documentation for moving PLOTT Legacy sites onto the pipeline*

### PULL DOWN
1. Pull latest code and theme files:
   - `git pull` to update repository
   - Download theme files via FTP from live server
   - If local theme differs from live, re-upload after comparing
2. Export database from live server
---
### DDEV
1. Config DDEV:
   - `DDEV config --project-name={$sitename} --project-type=wordpress`
   - **BEDROCK** `DDEV config --project-name={$sitename} --project-type=wordpress --docroot=web`

2. Install DDEV Adminer (database management UI):
   - `ddev add-on get ddev/ddev-adminer`
3. Import the exported database:
   - `ddev import-db --src=backup.sql`
---
### COMPOSER
1. Create composer.json ( or use this [example](https://github.com/ewan-plott/plott-pipe-md/blob/main/example.composer.json) ) -
   - run `composer require johnpbloch/wordpress-core:{$live_version}` and ensure the following exists below the `"require"` in your composer.json
```
 "extra": {
    "wordpress-install-dir": "wordpress"   // use 'wp' for bedrock
 },
```
2. Set required plugins to match live server versions:
   - Review live server active plugins and their versions
   - Update the `"require"` section in composer.json to match each plugin version
   - Example: `"plugin-name/plugin": "1.2.3"`
   
3. Add changelog tracking with `log_changelog.php`:
   - Copy the [log_changelog.php](https://github.com/ewan-plott/plott-pipe-md/blob/main/log_changelog.php) script to your project root
   - This script logs all dependency changes during composer updates
   - Add the following to the `"scripts"` section in your composer.json
   ```
      "post-update-cmd": [
         "php log_changelog.php"
       ],
   ```
   - update `$SITE_ID` & `$SITE_NAME` on lines `22` & `23`
4. Preserve pre-update state with `save_composer_lock.php`:
   - Copy the [save_composer_lock.php](https://github.com/ewan-plott/plott-pipe-md/blob/main/save_composer_lock.php) script to your project root
   - This creates a backup of your current lock file before updates
   - Add the following to the `"scripts"` section in your composer.json:
   ```
   "pre-update-cmd": [
      "php save_composer_lock.php"
   ]
   ```
   - Run `composer update` to update all dependencies while preserving version history

### PLOTT REPMAN

1. Obtain authentication token from the team and configure Composer:
   - `composer config --global --auth http-basic.plottcreative.repo.repman.io token {{REPMAN_TOKEN}}`
   - Replace `{{REPMAN_TOKEN}}` with the actual token provided

2. Configure the Repman repository in Composer:
   - `composer config repositories.plott '{"type": "composer", "url": "https://plottcreative.repo.repman.io"}'`
   - This allows Composer to fetch private Plott plugins

3. Install Plott plugins from the registry:
   - Browse available plugins at [Repman Organization](https://app.repman.io/organization/plottcreative/package)
   - Example: `composer require ashleyarmstrong/plott-gf`

---

### GITHUB WORKFLOWS

1. Set up GitHub Actions workflows:
   - Create `.github/workflows/` directory (see [example templates](https://github.com/ewan-plott/plott-pipe-md/tree/main/github))
   - Copy relevant workflow files to this directory
   - Update **site paths** and **theme paths** to match your project structure
   - Verify the target deployment branch (main or master)

2. Configure secure configuration files:
   - Create a secure [wp-config.php](https://github.com/ewan-plott/plott-pipe-md/blob/main/wp-config.php) with environment variables
   - Create `.env` file with environment-specific settings (do not commit to repository)
   - For legacy sites, remove vulnerable wp-config versions: [remove-wp-config-across-branches](https://github.com/ewan-plott/remove-wp-config-across-branches)

3. Add GitHub repository secrets:
   - Set deployment credentials, API keys, and WordPress salts in GitHub Settings > Secrets
   - These will be injected into workflows at runtime

4. Update `.gitignore` to prevent committing sensitive files:
   - Add `.env`, `wp-config.php`, and `composer.lock` (if using dynamic versioning)

## PUSH AND TEST!!
