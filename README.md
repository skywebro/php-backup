PHP Backup
==========

This set of scripts provide a framework for making backups using the PHP language.  
It was developed and tested in Ubuntu using PHP 5.5.9 but it might work with lower versions. Because it uses "pcntl_fork", it does not run on Windows platforms.  
It supports out of the box two kind of backups: ftp using wget and mysql using mysqldump.

Requirements
------------

* PHP 5.5.9 on *nix platform (it might work with lower PHP versions)
* the wget command
* the mysqldump command

Usage
-----
* prepare the ini and the hosts file
    * `chmod +x php-backup`
    * `cp backup.ini.dist backup.ini`
    * `touch hosts`
    * add the ftp and mysql login information in your hosts file
* run the command: `./php-backup -i backup.ini`

Extending
---------
* create your namespace: `mkdir -p com/example/backup/observers`
* `cd com/example/backup/observers`
* `vi custom.class.php`
* paste this code:


    ```php
    <?php
    namespace Com\Example\Backup\Observers;

    class Custom implements \SplObserver {
        protected $name = 'Custom Observer';

        public function update(\SplSubject $subject) {
            //do your thing here
        }
    }
    ```
* add the observer class to the "observers" section of the ini file:
    `custom = Com\Example\Backup\Observers\Custom`
* run the command: `./php-backup -i backup.ini`
