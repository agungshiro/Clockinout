<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php echo form_open_multipart('admin/import/upload/', array('role'=>'form')); ?>
    <div class="row">
        <div class="form-group col-sm-6<?php echo form_error('restaurant') ? ' has-error' : ''; ?>">
            <?php echo form_label('Select restaurant', 'restaurant', array('class'=>'control-label')); ?>
            <?php echo form_dropdown('id_restourant', $options); ?>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-sm-6">
            <label class="control-label" for="customFile">Choose file</label>
            <input type="file" class="custom-file-input" name="csv_file" id="customFile">
        </div>
    </div>
    <br>
    <div class="row">
        <a class="btn btn-link" href="<?php echo $cancel_url; ?>"><?php echo lang('core button cancel'); ?></a>
        <button type="submit" name="submit" class="btn btn-success"><span class="glyphicon glyphicon-upload"></span> <?php echo 'Upload'; ?></button>
    </div>
<?php echo form_close(); ?>
