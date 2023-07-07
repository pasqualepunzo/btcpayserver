#!/bin/bash
echo Updating...

git stash
git pull

### Folder rights
folder="web/assets"

if [ -d "$folder" ]; then
    chgrp www-data $folder
    chmod g+w $folder/
fi

folder="runtime"

if [ -d "$folder" ]; then
    chgrp www-data $folder
    chmod g+w $folder/
fi

# update sw
npm run update-sw

echo Update composer if required!
