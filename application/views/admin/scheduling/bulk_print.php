<style>
    span {
        width: 75px;
        display: inline-block;
    }
</style>
<?php foreach($employees as $em): ?>
<h2><?php echo($resto_name); ?></h2>
<div><?php echo($resto_address); ?></div>
<div><?php echo($resto_phone); ?></div>
<div><?php echo($resto_email); ?></div>
<div>=======================</div><br>
<div>Name : <?php echo($em['name']); ?></div><br>

<?php //print_r($em['dayoffs']); ?>

<?php foreach($em['dayoffs'] as $k => $do): ?>
    <div>---------------------------------------</div>
    <?php if ($do['type'] == 'off'): ?>
        <div><?php echo($do['date'].' : '.str_replace('_',' ',$do['type'])); ?></div>
        <div>Total Time : <?php echo($do['duration']); ?></div>
    <?php else: ?>
        <div><?php echo($do['date'].' : '.str_replace('_',' ',$do['type'])); ?></div>
        <div><span>Clock in</span> :  <?php echo($do['clockin']); ?></div>
        <div><span>Clock out</span> :  <?php echo($do['clockout']); ?></div>
        <div><span>Duration</span> : <?php echo($do['duration']); ?></div>
    <?php endif; ?>
<?php endforeach; ?>
<div>---------------------------------------</div><br>
<?php 
$dt = new DateTime("now", new DateTimeZone('America/New_York'));

?>
<div>Printed : <?php echo($dt->format('Y-m-d H:i:s'));?>
<div>Sign </div>
<br>
<br>
<br>
<div>-------------- Cut Here ---------------</div>
<br>
<br>
<br>
<div><?php echo($name); ?></div>
<?php endforeach; ?>

<?php

$CI =& get_instance();
$CI->load->library('Printerconnector');
$p_name = $CI->config->item('printers_name');
$CI->printerconnector->set_printers_name($p_name);

foreach($employees as $em):
    $CI->printerconnector->append_text($resto_name);
    $CI->printerconnector->append_text($resto_address);
    $CI->printerconnector->append_text($resto_phone);
    $CI->printerconnector->append_text($resto_email);
    $CI->printerconnector->append_text('======================================');
    
    $CI->printerconnector->append_text('Name : '.$em['name']);

    foreach($em['dayoffs'] as $k => $do):

        $CI->printerconnector->append_text('---------------------------------------');
        if ($do['type'] == 'off'):
            $CI->printerconnector->append_text($do['date'].' : '.str_replace('_',' ',$do['type']));
            $CI->printerconnector->append_text('Total Time : '.$do['duration']);
        else:
            $CI->printerconnector->append_text($do['date'].' : '.str_replace('_',' ',$do['type']));
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
    $CI->printerconnector->append_text($em['name']);

    $CI->printerconnector->print_out();

    $CI->printerconnector->clear_text();

endforeach;

?>