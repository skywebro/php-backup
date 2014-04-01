<?php
namespace Org\Impavidly\Backup\Observers;

class Psql extends Common {
    protected $name = 'PostgreSQL Observer';

    public function update(\SplSubject $subject) {
        $data = $this->getCsvRecord($subject->data, $subject->config['psql']['csv_fields_indexes']);
        $command = "PGPASSWORD={$data[4]}; {$subject->config['psql']['pg_dump']} -h {$data[0]} -p {$data[1]} -U {$data[3]} -w {$data[2]} > {$subject->outputPath}/{$data[0]}_{$data[2]}.psql";
        $status = $this->execute($subject, $command);

        return $status;
    }
}