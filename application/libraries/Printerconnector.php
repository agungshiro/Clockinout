<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;

Class Printerconnector {

    protected $CI;
    protected $printers_name;
    protected $text = '';

    function __construct() {
        
        $this->CI =& get_instance();

    }

    function set_printers_name($p_name) {
        $this->printers_name = $p_name;
    }

    function set_text($t) {
        $this->text = $text;
    }

    function clear_text() {
        $this->text = '';
    }

    function append_text($t) {
        $this->text = $this->text.$t."\n";
    }

    function print_out() {
        require('escpos-php-development/vendor/autoload.php');
        $tmpfname = tempnam(sys_get_temp_dir(), 'print-');
        $connector = new FilePrintConnector($tmpfname);

        // Generate output
        $printer = new Printer($connector);
        $printer -> setFont(Printer::FONT_B);
        $printer -> text($this->text);
        $printer -> cut();
        $printer -> close();

        // Print it out
        $cmd = sprintf("lpr -o raw -H localhost -P {$this->printers_name} %s 2>&1",
            escapeshellarg($tmpfname));
        exec($cmd, $retArr, $retVal);
        unlink($tmpfname);
        if($retVal != 0) {
            throw new Exception("$cmd: " . implode($retArr));
        }
    }

}

?>