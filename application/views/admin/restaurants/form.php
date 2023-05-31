<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php echo form_open('', array('role'=>'form')); ?>

    <?php // hidden id ?>
    <?php if (isset($restaurant_id)) : ?>
        <?php echo form_hidden('id', $restaurant_id); ?>
    <?php endif; ?>

    <div class="row">
        <?php // name ?>
        <div class="form-group col-sm-6<?php echo form_error('name') ? ' has-error' : ''; ?>">
            <?php echo form_label(lang('restaurants input name'), 'name', array('class'=>'control-label')); ?>
            <span class="required">*</span>
            <?php echo form_input(array('name'=>'name', 'value'=>set_value('name', (isset($restaurant['name']) ? $restaurant['name'] : '')), 'class'=>'form-control')); ?>
        </div>
    </div>

    <div class="row">
        <?php // address ?>
        <div class="form-group col-sm-6<?php echo form_error('address') ? ' has-error' : ''; ?>">
            <?php echo form_label(lang('restaurants input address'), 'address', array('class'=>'control-label')); ?>
            <span class="required">*</span>
            <?php echo form_input(array('name'=>'address', 'value'=>set_value('address', (isset($restaurant['address']) ? $restaurant['address'] : '')), 'class'=>'form-control')); ?>
        </div>
    </div>

    <div class="row">
        <?php // phone ?>
        <div class="form-group col-sm-6<?php echo form_error('phone') ? ' has-error' : ''; ?>">
            <?php echo form_label(lang('restaurants input phone'), 'phone', array('class'=>'control-label')); ?>
            <span class="required">*</span>
            <?php echo form_input(array('name'=>'phone', 'value'=>set_value('phone', (isset($restaurant['phone']) ? $restaurant['phone'] : '')), 'class'=>'form-control')); ?>
        </div>
    </div>

    <div class="row">
        <?php // email ?>
        <div class="form-group col-sm-6<?php echo form_error('email') ? ' has-error' : ''; ?>">
            <?php echo form_label(lang('restaurants input email'), 'email', array('class'=>'control-label')); ?>
            <span class="required">*</span>
            <?php echo form_input(array('name'=>'email', 'value'=>set_value('email', (isset($restaurant['email']) ? $restaurant['email'] : '')), 'class'=>'form-control')); ?>
        </div>
    </div>

    <div class="row">
        <?php // open hour ?>
        <div class="form-group col-sm-6<?php echo form_error('open_hour') ? ' has-error' : ''; ?>">
            <?php echo form_label(lang('restaurants input open_hour'), 'open_hour', array('class'=>'control-label')); ?>
            <span class="required">*</span>
            <div class="input-group clockpick" data-placement="bottom" data-align="top" data-autoclose="true">

                <?php echo form_input(array('name'=>'open_hour', 'value'=>set_value('open_hour', (isset($restaurant['open_hour']) ? $restaurant['open_hour'] : '')), 'class'=>'form-control', 'id'=>'timepick')); ?>
            </div>
        </div>
    </div>

    <div class="row">
        <?php // open hour ?>
        <div class="form-group col-sm-6<?php echo form_error('open_hour') ? ' has-error' : ''; ?>">
            <?php echo form_label(lang('restaurants input shift_hour'), 'shift_hour', array('class'=>'control-label')); ?>
            <span class="required">*</span>
            <div class="input-group clockpick" data-placement="bottom" data-align="top" data-autoclose="true">

                <?php echo form_input(array('name'=>'shift_hour', 'value'=>set_value('shift_hour', (isset($restaurant['shift_hour']) ? $restaurant['shift_hour'] : '')), 'class'=>'form-control', 'id'=>'timepick')); ?>
            </div>
        </div>
    </div>
    
    <div class="row">
        <?php // close hour ?>
        <div class="form-group col-sm-6<?php echo form_error('close_hour') ? ' has-error' : ''; ?>">
            <?php echo form_label(lang('restaurants input close_hour'), 'close_hour', array('class'=>'control-label')); ?>
            <span class="required">*</span>
            <div class="input-group clockpick" data-placement="bottom" data-align="top" data-autoclose="true">
            <?php echo form_input(array('name'=>'close_hour', 'value'=>set_value('close_hour', (isset($restaurant['close_hour']) ? $restaurant['close_hour'] : '')), 'class'=>'form-control', 'id'=>'timepick')); ?>
            </div>
        </div>
    </div>

    <?php // buttons ?>
    <div class="row pull-right">
        <a class="btn btn-link" href="<?php echo $cancel_url; ?>"><?php echo lang('core button cancel'); ?></a>
        <button type="submit" name="submit" class="btn btn-success"><span class="glyphicon glyphicon-save"></span> <?php echo lang('core button save'); ?></button>
    </div>

<?php echo form_close(); ?>
