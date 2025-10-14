# Legacy Sites > Pipeline
**Key Steps to follow locally with the current PLOTT local env**


1. git pull + ftp theme ( re push  if differences )
2. export live theme
3. Config DDEV:
   - `DDEV config --project-name={$sitename} --project-type=wordpress --docroot=web` 
   - `DDEV add-on get ddev/ddev-adminer`
5. Import db
6. Create composer.json ( or use this [example](https://github.com/ewan-plott/plott-pipe-md/blob/main/example.composer.json) ) -
   - run `composer require johnpbloch/wordpress-core:{$live_version}`:
        and ensure the following exists below the `"require"` in your composer.json
```
 "extra": {
    "wordpress-install-dir": "wordpress"   // use 'wp' for bedrock
 },
```
   - set require plugins to current versions on the live server
   
8. Create [log_changelog](https://github.com/ewan-plott/plott-pipe-md/blob/main/log_changelog.php) php script, and ensure the following exists in the `"scripts"` in your composer.json
   ```
      "post-update-cmd": [
         "php log_changelog.php"
       ],
   ```
9. Create [save_composer_lock](https://github.com/ewan-plott/plott-pipe-md/blob/main/save_composer_lock.php) php script, and ensure the following exists in the `"scripts"` in your composer.json
    
```
"pre-update-cmd": [
   "php save_composer_lock.php"
]
```
10. Create `.github/` folder ( example found [here](https://github.com/ewan-plott/plott-pipe-md/tree/main/.github) ) and set-up appropriate config & workflows.
   - update *sitepaths* and *themepaths* 
   - check branch it's pushing to *main* or *master*
