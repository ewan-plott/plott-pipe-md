# Legacy Sites > Pipeline

*Documentation for moving PLOTT Legacy sites onto the pipeline*

### PULL DOWN
1. git pull + ftp theme ( re push  if differences )
2. export live db
---
### DDEV
1. Config DDEV:
   - `DDEV config --project-name={$sitename} --project-type=wordpress`
   - **BEDROCK** `DDEV config --project-name={$sitename} --project-type=wordpress --docroot=web`

2. Install DDEV Adminer
   - `DDEV add-on get ddev/ddev-adminer` 
3. Import db
---
### COMPOSER
1. Create composer.json ( or use this [example](https://github.com/ewan-plott/plott-pipe-md/blob/main/example.composer.json) ) -
   - run `composer require johnpbloch/wordpress-core:{$live_version}` and ensure the following exists below the `"require"` in your composer.json
```
 "extra": {
    "wordpress-install-dir": "wordpress"   // use 'wp' for bedrock
 },
```
2. set require plugins to current versions on the live server
   
3. Create [log_changelog](https://github.com/ewan-plott/plott-pipe-md/blob/main/log_changelog.php) php script, and ensure the following exists in the `"scripts"` in your composer.json
   ```
      "post-update-cmd": [
         "php log_changelog.php"
       ],
   ```
   - update `$SITE_ID` & `$SITE_NAME` on lines `22` & `23`
4. Create [save_composer_lock](https://github.com/ewan-plott/plott-pipe-md/blob/main/save_composer_lock.php) php script, and ensure the following exists in the `"scripts"` in your composer.json
    
   ```
   "pre-update-cmd": [
   "php save_composer_lock.php"
   ]
   ```
   Run `composer update` - this will set a .lock with current live version that are now ready to be updated.

### PLOTT REPMAN

Run this and attain the token inhouse  (remove '{' in token )
 - `composer config --global --auth http-basic.plottcreative.repo.repman.io token {{ token }}`

Configure PLOTT repman repo
 - `composer config repositories.plott '{"type": "composer", "url": "https://plottcreative.repo.repman.io"}'`

Now install your plott [plugins](https://app.repman.io/organization/plottcreative/package)
 - e.g. `composer require ashleyarmstrong/plott-gf`

---

### GITHUB WORKFLOWS
1. Create `.github/` folder ( example found [here](https://github.com/ewan-plott/plott-pipe-md/tree/main/github) ) and set-up appropriate config & workflows ( if taken this github folder remember to rename to `.github` ).
   - update *sitepaths* and *themepaths* 
   - check branch it's pushing to *main* or *master*
   
3. Create secure wp-config and .env
  - Secure [wp-config](https://github.com/ewan-plott/plott-pipe-md/blob/main/wp-config.php)
  - If legacy site remove all vulnerable wp-configs [here](https://github.com/ewan-plott/remove-wp-config-across-branches)
2. Set GitHub Repo Secrets and Salts.

4. Update .gitignore

## PUSH AND TEST!!
