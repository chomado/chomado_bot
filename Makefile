SHELL := /bin/bash

all: init doc

init: install-composer install

install-composer:
	if [ ! -e composer.phar ]; then curl -sS https://getcomposer.org/installer | php; fi

install: install-composer
	php composer.phar install

update:	install-composer
	php composer.phar self-update
	php composer.phar update

doc: install
	vendor/bin/apigen generate --source="class" --destination="doc/api" --template-theme="bootstrap" --todo --tree --access-levels="public,protected,private" --internal

clean:
	rm -rf doc vendor composer.phar

FORCE:
