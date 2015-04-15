all: init doc

init: install-composer depends-setup

install-composer: composer.phar

depends-setup: install-composer
	php composer.phar install

depends-update: install-composer
	php composer.phar self-update
	php composer.phar update

docker:
	sudo docker build -t chomado/bot:latest .

doc: depends-setup
	vendor/bin/apigen generate --source="class" --destination="doc/api" --template-theme="bootstrap" --todo --tree --access-levels="public,protected,private" --internal

test:
	vendor/bin/phpunit

check-style:
	vendor/bin/phpmd class text codesize,controversial,design,naming,unusedcode
	vendor/bin/phpcs --standard=PSR2 class reply.php tweet_date.php tweet_weather.php

fix-style:
	vendor/bin/phpcbf --standard=PSR2 class reply.php tweet_date.php tweet_weather.php test

clean:
	rm -rf doc vendor composer.phar

composer.phar:
	curl -sS https://getcomposer.org/installer | php

.PHONY: all init install-composer depends-setup depends-update doc test style clean
