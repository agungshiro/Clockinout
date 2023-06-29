<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-6 text-left">
                <h3 class="panel-title">Name : <?php echo $employee['name'] ?> , Restaurant : <?php echo $employee['restaurant'] ?></h3>
            </div>
            <div class="col-md-6 text-right">
                <a class="btn btn-success tooltips" href="<?php echo base_url('admin/scheduling/add/'.$employee['id']); ?>" title="Add new scheduling" data-toggle="tooltip"><span class="glyphicon glyphicon-plus-sign"></span> Add new period</a>
            </div>
        </div>
    </div>

    <table class="table table-striped table-hover-warning">
        <thead>

            <?php // sortable headers ?>
            <tr>
                <td>
                    <a href="<?php echo current_url(); ?>?sort=id&dir=<?php echo (($dir == 'asc' ) ? 'desc' : 'asc'); ?>&limit=<?php echo $limit; ?>&offset=<?php echo $offset; ?><?php echo $filter; ?>">ID</a>
                    <?php if ($sort == 'id') : ?><span class="glyphicon glyphicon-arrow-<?php echo (($dir == 'asc') ? 'up' : 'down'); ?>"></span><?php endif; ?>
                </td>
                <td>
                    <a href="<?php echo current_url(); ?>?sort=start_period&dir=<?php echo (($dir == 'asc' ) ? 'desc' : 'asc'); ?>&limit=<?php echo $limit; ?>&offset=<?php echo $offset; ?><?php echo $filter; ?>">Start Period</a>
                    <?php if ($sort == 'start_period') : ?><span class="glyphicon glyphicon-arrow-<?php echo (($dir == 'asc') ? 'up' : 'down'); ?>"></span><?php endif; ?>
                </td>
                <td>
                    <a href="<?php echo current_url(); ?>?sort=end_period&dir=<?php echo (($dir == 'asc' ) ? 'desc' : 'asc'); ?>&limit=<?php echo $limit; ?>&offset=<?php echo $offset; ?><?php echo $filter; ?>">End Period</a>
                    <?php if ($sort == 'end_period') : ?><span class="glyphicon glyphicon-arrow-<?php echo (($dir == 'asc') ? 'up' : 'down'); ?>"></span><?php endif; ?>
                </td>
                <td class="pull-right">
                    <?php echo lang('admin col actions'); ?>
                </td>
            </tr>

            <?php // search filters ?>
            <tr>
                <?php echo form_open("{$this_url}?sort={$sort}&dir={$dir}&limit={$limit}&offset=0{$filter}", array('role'=>'form', 'id'=>"filters")); ?>
                    <th>
                    </th>
                    <th<?php echo ((isset($filters['start_period'])) ? ' class="has-success"' : ''); ?>>
                        <?php echo form_input(array('name'=>'start_period', 'id'=>'start_period', 'class'=>'form-control input-sm', 'placeholder'=>'Start Period', 'value'=>set_value('start_period', ((isset($filters['start_period'])) ? $filters['start_period'] : '')))); ?>
                    </th>
                    <th colspan="3">
                        <div class="text-right">
                            <a href="<?php echo $this_url; ?>" class="btn btn-danger btn-sm tooltips" data-toggle="tooltip" title="<?php echo lang('admin tooltip filter_reset'); ?>"><span class="glyphicon glyphicon-refresh"></span> <?php echo lang('core button reset'); ?></a>
                            <button type="submit" name="submit" value="<?php echo lang('core button filter'); ?>" class="btn btn-success btn-sm tooltips" data-toggle="tooltip" title="<?php echo lang('admin tooltip filter'); ?>"><span class="glyphicon glyphicon-filter"></span> <?php echo lang('core button filter'); ?></button>
                        </div>
                    </th>
                <?php echo form_close(); ?>
            </tr>

        </thead>
        <tbody>

            <?php // data rows ?>
            <?php if ($total) : ?>
                <?php foreach ($scheduling as $schedule) : ?>
                    <tr>
                        <td<?php echo (($sort == 'id') ? ' class="sorted"' : ''); ?>>
                            <?php echo $schedule['id']; ?>
                        </td>
                        <td<?php echo (($sort == 'start_period') ? ' class="sorted"' : ''); ?>>
                            <?php echo $schedule['start_period']; ?>
                        </td>
                        <td<?php echo (($sort == 'end_period') ? ' class="sorted"' : ''); ?>>
                            <?php echo $schedule['end_period']; ?>
                        </td>
                        
                        <td>
                            <div class="text-right">
                                <div class="btn-group">
                                    <a href="#modal-<?php echo $schedule['id']; ?>" data-toggle="modal" class="btn btn-danger btn-xs" title="<?php echo lang('admin button delete'); ?>"><span class="glyphicon glyphicon-trash"></span></a>
                                    <?php if($schedule['day']): ?>
                                        <a href="#" class="btn btn-secondary btn-xs" title="<?php echo lang('admin button edit'); ?>" disabled><span class="glyphicon glyphicon-list-alt"></span></a>
                                        <a href="<?php echo $this_url; ?>/print/<?php echo $schedule['id']; ?>" target="_blank" class="btn btn-success btn-xs" title="<?php echo('Print'); ?>"><span class="glyphicon glyphicon-print"></span></a>
                                        <a href="<?php echo $this_url; ?>/print_direct/<?php echo $schedule['id']; ?>" class="btn btn-warning btn-xs" title="<?php echo('Print'); ?>"><span class="glyphicon glyphicon-print"></span></a>
                                    <?php else: ?>
                                        <a href="<?php echo $this_url; ?>/add_day_off/<?php echo $schedule['id']; ?>" class="btn btn-info btn-xs" title="<?php echo lang('admin button edit'); ?>" aria-disabled = "true" role="link"><span class="glyphicon glyphicon-list-alt"></span></a>
                                        <a href="#" class="btn btn-secondary btn-xs" title="<?php echo('Print'); ?>" disabled><span class="glyphicon glyphicon-print"></span></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="7">
                        <?php echo lang('core error no_results'); ?>
                    </td>
                </tr>
            <?php endif; ?>

        </tbody>
    </table>

    <?php // list tools ?>
    <div class="panel-footer">
        <div class="row">
            <div class="col-md-2 text-left">
                <label><?php echo sprintf(lang('admin label rows'), $total); ?></label>
            </div>
            <div class="col-md-2 text-left">
                <?php if ($total > 10) : ?>
                    <select id="limit" class="form-control">
                        <option value="10"<?php echo ($limit == 10 OR ($limit != 10 && $limit != 25 && $limit != 50 && $limit != 75 && $limit != 100)) ? ' selected' : ''; ?>>10 <?php echo lang('admin input items_per_page'); ?></option>
                        <option value="25"<?php echo ($limit == 25) ? ' selected' : ''; ?>>25 <?php echo lang('admin input items_per_page'); ?></option>
                        <option value="50"<?php echo ($limit == 50) ? ' selected' : ''; ?>>50 <?php echo lang('admin input items_per_page'); ?></option>
                        <option value="75"<?php echo ($limit == 75) ? ' selected' : ''; ?>>75 <?php echo lang('admin input items_per_page'); ?></option>
                        <option value="100"<?php echo ($limit == 100) ? ' selected' : ''; ?>>100 <?php echo lang('admin input items_per_page'); ?></option>
                    </select>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <?php echo $pagination; ?>
            </div>
            <div class="col-md-2 text-right">
                <?php if ($total) : ?>
                    <a href="<?php echo $this_url; ?>/export?sort=<?php echo $sort; ?>&dir=<?php echo $dir; ?><?php echo $filter; ?>" class="btn btn-success btn-sm tooltips" data-toggle="tooltip" title="<?php echo lang('admin tooltip csv_export'); ?>"><span class="glyphicon glyphicon-export"></span> <?php echo lang('admin button csv_export'); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>

<?php // delete modal ?>
<?php if ($total) : ?>
    <?php foreach ($scheduling as $schedule) : ?>
        <div class="modal fade" id="modal-<?php echo $schedule['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="modal-label-<?php echo $schedule['id']; ?>" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 id="modal-label-<?php echo $schedule['id']; ?>"><?php echo lang('users title user_delete');  ?></h4>
                    </div>
                    <div class="modal-body">
                        <p><?php echo sprintf(lang('scheduling msg delete_confirm'), $schedule['start_period']); ?></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang('core button cancel'); ?></button>
                        <button type="button" class="btn btn-primary btn-delete-user" data-id="<?php echo $schedule['id']; ?>"><?php echo lang('admin button delete'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
