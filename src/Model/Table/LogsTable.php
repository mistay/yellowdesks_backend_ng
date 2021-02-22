<?php
namespace App\Model\Table;
use Cake\ORM\Table;

class LogsTable extends Table {
    function debug($message) {
        $this -> logdb($message, "debug");
    }

    function info($message) {
        $this -> logdb($message, "info");
    }

    function error($message) {
        $this -> logdb($message, "error");
    }

    // log() darf man in cake nicht überschreiben, sonst bekommt man: Error: Access level to Log::log() must be public (as in class Object)
    // public damit auch DataSource::CouchDB statisch drauf zugreifen kann
    // getLogFilename() muss im AppModel einen validen pfad zurückliefern
    public function lognow($message, $level) {

        $lines = split("\n", $message);

        $firstline = true;
        foreach ($lines as $line) {
            file_put_contents($this -> getLogFilename(), date("Y-m-d H:i:s") . "\t" . $level . "\t" . ($firstline ? "" : "  ") . $line . "\n", FILE_APPEND);
            $firstline = false;
        }

    }

    private function logdb($message, $level) {
        $log = $this -> newEntity([]);
        $log->message = $message;
        $log->level = $level;
        $this -> save($log);
    }
}

?>