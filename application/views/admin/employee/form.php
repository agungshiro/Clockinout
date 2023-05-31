<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php echo form_open('', array('role'=>'form')); ?>

    <?php // hidden id ?>
    <?php if (isset($employee_id)) : ?>
        <?php echo form_hidden('id', $employee_id); ?>
    <?php endif; ?>

    <div class="row">
        <div class="form-group col-sm-6<?php echo form_error('restaurant') ? ' has-error' : ''; ?>">
            <?php echo form_label(lang('employee input restaurant'), 'restaurant', array('class'=>'control-label')); ?>
            <?php echo form_dropdown('id_restourant', $options ,(isset($employee['id_restourant']) ? $employee['id_restourant'] : '')); ?>
        </div>
    </div>

    <div class="row">
        <?php // name ?>
        <div class="form-group col-sm-6<?php echo form_error('name') ? ' has-error' : ''; ?>">
            <?php echo form_label(lang('employee input name'), 'name', array('class'=>'control-label')); ?>
            <span class="required">*</span>
            <?php echo form_input(array('name'=>'name', 'value'=>set_value('name', (isset($employee['name']) ? $employee['name'] : '')), 'class'=>'form-control')); ?>
        </div>
    </div>

    <div class="row">
        <?php // address ?>
        <div class="form-group col-sm-6<?php echo form_error('address') ? ' has-error' : ''; ?>">
            <?php echo form_label(lang('employee input address'), 'address', array('class'=>'control-label')); ?>
            <span class="required">*</span>
            <?php echo form_input(array('name'=>'address', 'value'=>set_value('address', (isset($employee['address']) ? $employee['address'] : '')), 'class'=>'form-control')); ?>
        </div>
    </div>

    <div class="row">
        <?php // phone ?>
        <div class="form-group col-sm-6<?php echo form_error('phone') ? ' has-error' : ''; ?>">
            <?php echo form_label(lang('employee input phone'), 'phone', array('class'=>'control-label')); ?>
            <span class="required">*</span>
            <?php echo form_input(array('name'=>'phone', 'value'=>set_value('phone', (isset($employee['phone']) ? $employee['phone'] : '')), 'class'=>'form-control')); ?>
        </div>
    </div>

    <div class="row">
        <?php // email ?>
        <div class="form-group col-sm-6<?php echo form_error('email') ? ' has-error' : ''; ?>">
            <?php echo form_label(lang('employee input email'), 'email', array('class'=>'control-label')); ?>
            <span class="required">*</span>
            <?php echo form_input(array('name'=>'email', 'value'=>set_value('email', (isset($employee['email']) ? $employee['email'] : '')), 'class'=>'form-control')); ?>
        </div>
    </div>

    <?php // buttons ?>
    <div class="row pull-right">
        <a class="btn btn-link" href="<?php echo $cancel_url; ?>"><?php echo lang('core button cancel'); ?></a>
        <button type="submit" name="submit" class="btn btn-success"><span class="glyphicon glyphicon-save"></span> <?php echo lang('core button save'); ?></button>
    </div>

<?php echo form_close(); ?>
