<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php echo form_open('', array('role'=>'form')); ?>

    <?php // hidden id ?>
    <?php if (isset($id_restaurant)) : ?>
        <?php echo form_hidden('id_restaurant', $id_restaurant); ?>
    <?php endif; ?>
    <?php if (isset($id_job)) : ?>
        <?php echo form_hidden('id', $id_job); ?>
    <?php endif; ?>

    <div class="row">
        <?php // name ?>
        <div class="form-group col-sm-6<?php echo form_error('name') ? ' has-error' : ''; ?>">
            <?php echo form_label('Name', 'name', array('class'=>'control-label')); ?>
            <span class="required">*</span>
            <?php echo form_input(array('name'=>'name', 'value'=>set_value('name', (isset($job['name']) ? $job['name'] : '')), 'class'=>'form-control')); ?>
        </div>
    </div>

    <div class="row">
        <?php // address ?>
        <div class="form-group col-sm-6<?php echo form_error('time_limit') ? ' has-error' : ''; ?>">
            <?php echo form_label('Hour limit', 'time_limit', array('class'=>'control-label')); ?>
            <span class="required">*</span>
            <?php echo form_input(array('name'=>'time_limit', 'value'=>set_value('time_limit', (isset($job['time_limit']) ? $job['time_limit'] : '')), 'class'=>'form-control')); ?>
        </div>
    </div>

    <div class="row">
        <?php // phone ?>
        <div class="form-group col-sm-6<?php echo form_error('shift_limit') ? ' has-error' : ''; ?>">
            <?php echo form_label('Shift limit', 'shift_limit', array('class'=>'control-label')); ?>
            <span class="required">*</span>
            <?php echo form_input(array('name'=>'shift_limit', 'value'=>set_value('shift_limit', (isset($job['shift_limit']) ? $job['shift_limit'] : '')), 'class'=>'form-control')); ?>
        </div>
    </div>

    

    <?php // buttons ?>
    <div class="row pull-right">
        <a class="btn btn-link" href="<?php echo $cancel_url; ?>"><?php echo lang('core button cancel'); ?></a>
        <button type="submit" name="submit" class="btn btn-success"><span class="glyphicon glyphicon-save"></span> <?php echo lang('core button save'); ?></button>
    </div>

<?php echo form_close(); ?>
