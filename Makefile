all: init doc

init: install-composer depends-setup

install-composer: composer.phar

depends-setup: install-composer
	php composer.phar install

depends-update: install-composer
	php composer.phar self-update
	php composer.phar update

doc: depends-setup
	vendor/bin/apigen generate --source="class" --destination="doc/api" --template-theme="bootstrap" --todo --tree --access-levels="public,protected,private" --internal

clean:
	rm -rf doc vendor composer.phar

composer.phar:
	curl -sS https://getcomposer.org/installer | php
