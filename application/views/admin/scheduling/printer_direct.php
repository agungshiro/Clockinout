
<?php
$CI =& get_instance();
$CI->load->library('Printerconnector');
$p_name = $CI->config->item('printers_name');
echo($p_name);

$CI->printerconnector->set_printers_name($p_name);

$CI->printerconnector->append_text($resto_name);
$CI->printerconnector->append_text($resto_address);
$CI->printerconnector->append_text($resto_phone);
$CI->printerconnector->append_text($resto_email);
$CI->printerconnector->append_text('======================================');

$CI->printerconnector->append_text('Name : '.$name);

foreach($dayoffs as $do):
    $CI->printerconnector->append_text('---------------------------------------');
    if ($do['type'] == 'off'):
        $CI->printerconnector->append_text($do['day'].' : '.str_replace('_',' ',$do['type']));
        $CI->printerconnector->append_text('Total Time : '.$do['duration']);
    else:
        $CI->printerconnector->append_text($do['day'].' : '.str_replace('_',' ',$do['type']));
        $CI->printerconnector->append_text('Clock In : '.$do['clockin']);
        $CI->printerconnector->append_text('Clock Out : '.$do['clockout']);
        $CI->printerconnector->append_text('Duration : '.$do['duration']);
    endif;
    
endforeach;
$CI->printerconnector->append_text('---------------------------------------');
$dt = new DateTime("now", new DateTimeZone('America/New_York'));
$CI->printerconnector->append_text('Printed : '.$dt->format('Y-m-d H:i:s'));
$CI->printerconnector->append_text('Signed');
$CI->printerconnector->append_text('');
$CI->printerconnector->append_text('');
$CI->printerconnector->append_text($name);


$CI->printerconnector->print_out();




?>