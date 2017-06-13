#!/bin/bash

# A script that downloads an SQL dump of the database and the files, for a complete backup.

#Where do you want to save the data?
dir=/home/colin/MaVoix
current_dir=${pwd}

echo -n ZIP password:
read -s password
echo

date=`date +%Y-%m-%d:%H:%M:%S`

cd $dir

if [ ! -f dataGL.zip ]; then
mkdir sql data
else
unzip -P $password dataGL.zip
fi

#soyou-r is the SSH host alias I use for the server, you must change it and use yours.

ssh soyou-r 'mysqldump -u root -p groupes-locaux > dump.sql'
scp soyou-r:dump.sql sql/$date.sql
ssh soyou-r 'shred -v -n5 dump.sql'
rsync -arvz soyou:groupes-locaux/web/data .
zip -P $password -r dataGL.zip data sql
rm -rfv data sql

cd $current_dir
