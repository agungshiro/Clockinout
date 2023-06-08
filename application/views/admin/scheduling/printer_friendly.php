<style>
    span {
        width: 75px;
        display: inline-block;
    }
</style>
<h2><?php echo($resto_name); ?></h2>
<div><?php echo($resto_address); ?></div>
<div><?php echo($resto_phone); ?></div>
<div><?php echo($resto_email); ?></div>
<div>=======================</div><br>
<div>Name : <?php echo($name); ?></div><br>

<?php foreach($dayoffs as $do): ?>
    <div>---------------------------------------</div>
    <?php if ($do['type'] == 'off'): ?>
        <div><?php echo($do['day'].' : '.str_replace('_',' ',$do['type'])); ?></div>
        <div>Total Time : <?php echo($do['duration']); ?></div>
    <?php else: ?>
        <div><?php echo($do['day'].' : '.str_replace('_',' ',$do['type'])); ?></div>
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
<div><?php echo($name); ?></div>