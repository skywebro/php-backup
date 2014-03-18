PHP Backup
==========

This set of scripts provide a framework for making backups using the PHP language.  
It was developed and tested in Ubuntu using PHP 5.5.9 but it might work with lower versions. Because it uses `pcntl_fork`, it does not run on Windows platforms.  
It supports out of the box three kind of backups: ftp using `wget`, mysql using `mysqldump` and filesystem using `tar`.

Requirements
------------

* PHP 5.5.9 on *nix platform (it might work with lower PHP versions)
* the wget command
* the mysqldump command

Usage
-----

* prepare the script: `chmod +x php-backup`
* prepare the ini and the csv file
    * `cp backup.ini.dist backup.ini`
    * `touch hosts`
    * add the ftp and mysql login information in your hosts file
* run the command: `./php-backup -i backup.ini`

Filesystem backup
-----------------

This method iterates through a list of paths defined in a CSV file and it creates archives of each children directories (useful when you have all projects in the same directory).

### Usage

* `cp filesystem.ini.dist filesystem.ini` and edit it to suit your needs
* `cp filesystem.dist filesystem`
* edit "filesystem" and add the parent directories, each on a new line
* run the script: `./php-backup -i filesystem.ini`

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

    You might want to extend the `Common` class which already has implemented an `execute` method:

    ```php
    <?php
    namespace Com\Example\Backup\Observers;
    
    use Org\Impavidly\Backup\Observers\Common;

    class Custom extends Common {
        protected $name = 'Custom Observer';

        public function update(\SplSubject $subject) {
            $command = "<some command>";
            $status = $this->execute($subject, $command);
            
            return $status;
        }
    }
    ```    
    
* add the observer class to the "observers" section of the ini file:
    `custom = Com\Example\Backup\Observers\Custom`
* add your configuration in a new ini section, something like

    ```
    [custom]
    name = value
    ```

    accessible in the observer as `$subject->config['custom']['name']`
* run the command: `./php-backup -i backup.ini`
