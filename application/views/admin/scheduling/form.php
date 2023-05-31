<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php echo form_open('', array('role'=>'form')); ?>

    <?php // hidden id ?>
    <?php if (isset($id_employee)) : ?>
        <?php echo form_hidden('id_employee', $id_employee); ?>
    <?php endif; ?>

    <div class="row">
        <?php // start_period ?>
        <div class="form-group col-sm-2<?php echo form_error('start_period') ? ' has-error' : ''; ?>">
            <?php echo form_label(lang('scheduling input start_period'), 'start_period', array('class'=>'control-label')); ?>
            <span class="required">*</span>
            <div class="input-group date" data-date="<?php echo date('Y-m-d'); ?>" data-date-format="yyyy-mm-dd">
                <?php echo form_input(array('name'=>'start_period', 'id'=>'start_period', 'class'=>'form-control input-sm', 'readonly'=>'readonly', 'placeholder'=>lang('contact input created'), 'value'=>set_value('created', ((isset($filters['created'])) ? $filters['created'] : '')))); ?>
                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
            </div>
        </div>

        <?php // end_period ?>
        <div class="form-group col-sm-2<?php echo form_error('end_period') ? ' has-error' : ''; ?>">
            <?php echo form_label(lang('scheduling input end_period'), 'end_period', array('class'=>'control-label')); ?>
            <span class="required">*</span>
            <div class="input-group date" data-date="<?php echo date('Y-m-d'); ?>" data-date-format="yyyy-mm-dd">
                <?php echo form_input(array('name'=>'end_period', 'id'=>'end_period', 'class'=>'form-control input-sm', 'readonly'=>'readonly', 'placeholder'=>lang('contact input created'), 'value'=>set_value('created', ((isset($filters['created'])) ? $filters['created'] : '')))); ?>
                <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
            </div>
        </div>
        <div class="form-group col-sm-2<?php echo form_error('total_hours') ? ' has-error' : ''; ?>">
            <?php echo form_label(lang('scheduling input total_hours'), 'total_hours', array('class'=>'control-label')); ?>
            <span class="required">*</span>
            
            <?php echo form_input(array('name'=>'total_hours', 'id'=>'total_hours', 'class'=>'form-control input-sm',  'value'=>set_value('total_hours', ((isset($filters['total_hours'])) ? $filters['total_hours'] : '')))); ?>
        </div>
    </div>

    <div class="row">
        
    </div>

    
    <?php // buttons ?>
    <div class="row pull-right">
        <a class="btn btn-link" href="<?php echo $cancel_url; ?>"><?php echo lang('core button cancel'); ?></a>
        <button type="submit" name="submit" class="btn btn-success"><span class="glyphicon glyphicon-save"></span> <?php echo lang('core button save'); ?></button>
    </div>

<?php echo form_close(); ?>
