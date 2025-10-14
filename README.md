# Legacy Sites > Pipeline
**Key Steps to follow locally with the current PLOTT local env**


1. git pull + ftp theme ( re push  if differences )
2. export live theme
3. Config DDEV:
   - `DDEV config --project-name={$sitename} --project-type=wordpress --docroot=web` 
   - `DDEV add-on get ddev/ddev-adminer`
5. Import db
6. Establish a composer.josn ( take the most recent project and remove all requires )
