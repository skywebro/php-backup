PHP Backup
==========

This set of scripts provide a framework for making backups using the PHP language.  
It was developed and tested in Ubuntu using PHP 5.5.9 but it might work with lower versions. Because it uses `pcntl_fork`, it does not run on Windows platforms.  
It supports out of the box three kind of backups: ftp using `wget`, mysql using `mysqldump` and filesystem using `tar`.

Requirements
------------

* PHP 5.5.9 on *nix platform (it might work with lower PHP versions)
* the tar and bzip2 commands used by the directory and filesystem observers;
* the wget command used by the ftp observer;
* the mysqldump command used by the mysqldump observer;
* the scp and sshpass commands used by the ssh observer;
* the pg_dump command used by the psql observer;

FTP and MySQL backup
--------------------

* make the script executable: `chmod +x php-backup`
* prepare the ini and the csv file
    * `cp ftp_and_mysql.ini.dist ftp_and_mysql.ini`
    * `touch ftp_and_mysql`
    * add the ftp and mysql login information in your ftp_and_mysql file
* run the command: `./php-backup -i ftp_and_mysql.ini`

Filesystem backup
-----------------

This method iterates through a list of paths defined in a CSV file and it creates archives of each children directories (useful when you have all projects in the same directory).

### Usage

* `cp filesystem.ini.dist filesystem.ini` and edit it to suit your needs
* `cp filesystem.dist filesystem`
* edit `filesystem` and add the parent directories, each on a new line
* run the script: `./php-backup -i filesystem.ini`

Directories and MySQL backup
----------------------------

This method reads a CSV file with records made out of a path to a project and the mysql info for that project.

### Usage

* `cp directories_and_mysql.ini.dist directories_and_mysql.ini` and edit it to suit your needs
* `cp directories_and_mysql.dist directories_and_mysql`
* edit `directories_and_mysql` and add the project directory and the mysql info on the same line
* run the script: `./php-backup -i directories_and_mysql.ini`


Directories and PostgreSQL backup
----------------------------

This method reads a CSV file with records made out of a path to a project and the postgresql info for that project.

### Usage

* `cp directories_and_postgresql.ini.dist directories_and_postgresql.ini` and edit it to suit your needs
* `cp directories_and_postgresql.dist directories_and_postgresql`
* edit `directories_and_postgresql` and add the project directory and the postgresql info on the same line
* run the script: `./php-backup -i directories_and_postgresql.ini`

SSH and MySQL backup
----------------------------

This method reads a CSV file with records made out of ssh login info and the project path on the server together with the mysql info for that project.

### Usage

* `cp ssh_and_mysql.ini.dist ssh_and_mysql.ini` and edit it to suit your needs
* `cp ssh_and_mysql.dist ssh_and_mysql`
* edit `ssh_and_mysql` and add the ssh info and the mysql info on the same line
* run the script: `./php-backup -i ssh_and_mysql.ini`

**Note**: You can combine the observers in the ini file as you see fit.

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
* run the command: `./php-backup -i <some_ini_file>.ini`
