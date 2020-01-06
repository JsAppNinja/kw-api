# KW-API
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/b1bb2c208e494ec78f52f6238106cf17)](https://www.codacy.com?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=KWRI/kw-api&amp;utm_campaign=Badge_Grade)

[![Build Status](https://travis-ci.com/KWRI/kw-api.svg?token=de1QYsjEGBMPW1LXD7HN&branch=jpc-add-travis-ci)](https://travis-ci.com/KWRI/kw-api)

KW-API is middleware to dispatch information from services / applications to others, using messaging platform.

## Setup

For deploying from git.
1. clone from repository
	
	git clone https://github.com/KWRI/kw-api.git

2. run composer install, to install all reqiured library and dependencies
	
	cd kw-api

	composer install

3. configure .env file

	copy from .env.list to .env change configuration that appropriate with your environment

4. run migrate, to create database

	php artisan migrate:install

	php artisan migrate:refresh

5. run db seed, to create 10 api_users

	php artisan db:seed

6. create failed jobs table

	php artisan queue:failed-table

7. setup node packages, on directory project, run

	npm install

8. run gulp file to prepare ng-admin lib inside laravel

	gulp ng-admin

## Development Workflow

> **Do not work on master branch directly!!!**

1. Create a branch for each ticket in waffle.io
* Create a branch from master branch
* Branch name should start with the ticket number. Let's say, you create a branch for ticket 45. Then you can use the following name "45-coreperson"
2. After implementing new features / fixing issues, create pull request or notify Super Admin (Josh).
3. When the Super Admin approves, you or he would merge the branch with master branch.
4. You can delete the branch after merging.
