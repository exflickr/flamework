all: clean todo

clean:
	rm -f ./TODO.txt

todo: TODO.txt

TODO.txt:
	@echo "Generating TODO.txt file"
	@echo "# This file was generated automatically by grep-ing for 'TO DO' in the source code." > ./TODO.txt
	@echo "# This file is meant as a pointer to the actual details in the files themselves." >> TODO.txt
	@echo "# This file was created "`date` >> TODO.txt
	@echo "" >> TODO.txt
	@-grep -n -r -e "TO DO" www >> TODO.txt
	@-grep -n -r -e "TO DO" bin >> TODO.txt
	@-grep -n -r -e "TODO" www >> TODO.txt
	@-grep -n -r -e "TODO" bin >> TODO.txt

templates:
	php -q ./bin/compile-templates.php

secret:
	php -q ./bin/generate_secret.php

test:
	prove -v --exec 'php --php-ini ./tests/php.ini' ./tests/*.t

cover:
	rm -f ./tests/coverage.state
	rm -rf ./coverage
	-make test
	php -q ./tests/coverage.php
