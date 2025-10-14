# Legacy Sites > Pipeline
**Key Steps to follow locally with the current PLOTT local env**


1. git pull + ftp theme ( re push  if differences )
2. export live theme
3. Config DDEV:
   - `DDEV config --project-name={$sitename} --project-type=wordpress --docroot=web` 
   - `DDEV add-on get ddev/ddev-adminer`
5. Import db
6. Create composer.json ( or use this [example](https://github.com/ewan-plott/plott-pipe-md/blob/main/example.composer.json) ) -
   	*set require plugins to current versions on the live server*
7. Create [log_changelog](https://github.com/ewan-plott/plott-pipe-md/blob/main/log_changelog.php) php script, and ensure the following exist in the `"scripts"` in your composer.json
   ```
      "post-update-cmd": [
         "php log_changelog.php"
       ],
   ```
8. Create [save_composer_lock](https://github.com/ewan-plott/plott-pipe-md/blob/main/save_composer_lock.php) php script
