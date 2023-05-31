<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php echo form_open('', array('role'=>'form')); ?>

    <?php // hidden id ?>
    <?php if (isset($id_employee)) : ?>
        <?php echo form_hidden('id_employee', $id_employee); ?>
    <?php endif; ?>

    <?php if (isset($id_period)): ?>
        <?php echo form_hidden('id_period', $id_period); ?>
    <?php endif; ?>
    

    <table class="table table-striped">
        <thead>
            <th scope="col">No</th>
            <th scope="col">Date</th>
            <th scope="col" colspan="4">Status</th>
        </thead>
        <?php $x=1; ?>
        <?php foreach($days as $d): ?>
            <tr>
                <td><?php echo $x; ?></td>
                <td><?php echo $d; ?></td>
                <td>
                    <?php echo form_radio($d, 'full_day', TRUE); ?>
                    <label class="form-radio-label" for="<?php echo $d ?>">Full Day</label>
                </td>
                <td>
                    <?php echo form_radio($d, '1st_shift', FALSE); ?>
                    <label class="form-radio-label" for="<?php echo $d ?>">First Shift</label>
                </td>
                <td>
                    <?php echo form_radio($d, '2nd_shift', FALSE); ?>
                    <label class="form-radio-label" for="<?php echo $d ?>">Second Shift</label>
                </td>
                <td>
                    <?php echo form_radio($d, 'off', FALSE); ?>
                    <label class="form-radio-label" for="<?php echo $d ?>">Off</label>
                </td>
            </tr>
        <?php $x++; ?>
        <?php endforeach; ?>

    </table>



    
    
    <?php // buttons ?>
    <div class="row pull-right">
        <a class="btn btn-link" href="<?php echo $cancel_url; ?>"><?php echo lang('core button cancel'); ?></a>
        <button type="submit" name="submit" class="btn btn-success"><span class="glyphicon glyphicon-save"></span> <?php echo lang('core button save'); ?></button>
    </div>

<?php echo form_close(); ?>
