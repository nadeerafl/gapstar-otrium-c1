# Instructions

## Setup
* `cp .env.example .env` to create .env file, and replace the DB connection settings
* `composer install` to install all dependencies

## Generate Repsr CSV
* `composer generate-report` to  generate file

## Run Unit Test

### Run on windows terminal
* `composer test-windows` This wil run all test cases 

**Note:** in case test of error use direct command `.\vendor\bin\phpunit --testdox tests`
{: .note}

### Run on UNIX terminals
* `composer test-unix` This wil run all test cases 

**Note:** in case test of error use direct command `vendor/bin/phpunit --testdox tests`
{: .note}

