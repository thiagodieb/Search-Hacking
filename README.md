# README #
This tool has many features:


### How do I get set up? ###

* Summary of set up
-- Install "composer" in operation system
-- Inside folder project $php composer install
* Configuration
* How to run tests


### How to include in project ###

composer.json

{
	"repositories": [
	    { 
	        "type": "vcs", 
	        "url": "git@bitbucket.org:aszone/search-hacking.git",
	        "branch": "master",
	        "autoload": {
	            "psr-4": { 
	               "Aszone\\Component\\SearchHacking\\": "src/"     
	            }
	        }
	    }
	],
	"require": {
	    "aszone/search-hacking": "dev-master"
	},
	"minimum-stability": "dev"
}