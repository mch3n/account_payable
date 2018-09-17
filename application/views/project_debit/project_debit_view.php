<div class="col-xs-12">
    <div class="box box-primary">
        <form class="form-inline" role="form" name="filterdata" method="post" action="<?php echo site_url('projectdebit/go/'.$pagecode) ?>">
            <div class="box-header with-border">
                <div class="pull-left">
                    <div class="input-group">
                        <div class="btn-group">
                            <a href="<?php echo site_url('projectdebit/go/'.$pagecode.'/1/') ?>" class="btn btn-default">Today</a>
                            <a href="<?php echo site_url('projectdebit/go/'.$pagecode.'/2/') ?>" class="btn btn-default">Yesterday</a>
                            <a href="<?php echo site_url('projectdebit/go/'.$pagecode.'/3/') ?>" class="btn btn-default">This Week</a>
                            <a href="<?php echo site_url('projectdebit/go/'.$pagecode.'/4/') ?>" class="btn btn-default">This Month</a>
                            <a href="<?php echo site_url('projectdebit/go/'.$pagecode.'/5/') ?>" class="btn btn-default">Last Month</a>
                            <a href="<?php echo site_url('projectdebit/go/'.$pagecode.'/6/') ?>" class="btn btn-default">Up to Today</a>
                        </div>
                    </div>
                </div>
                <div class="pull-right">
                <?php
                if ($is_add_val){
                    echo '<a href="#" class="btn btn-success" onclick="add_data()"><i class="glyphicon glyphicon-plus"></i> New Data</a>';
                }
                ?>
                </div>
            </div>
            <div class="box-header with-border">
                <div class="form-group">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="start_date" class="form-control datepicker" value="<?php echo $start_date ?>" placeholder="Date from">
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" name="end_date" class="form-control datepicker" value="<?php echo $end_date ?>" placeholder="Date to">
                    </div>
                </div>
                <div class="form-group">
                    <label class="sr-only">Fields</label>
                    <select class="form-control" name="field_search">
                        <?php
                            foreach ($field_opt as $key => $value) {
                                echo '<option value="'.$key.'">'.$value.'</option>';
                            }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label class="sr-only" >Filter</label>
                    <input type="text" class="form-control" name="keyword" placeholder="Keyword">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Filter</button>
                
            </div> 
        </form>
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Number</th>
                        <th>Title</th>
                        <th>Vendor</th>
                        <th>Outlet</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <?php
                if ($list->num_rows()!=0){
                    $no = 1;    
                    $total_amount = 0;
                    
                    echo '<tbody>';
                    foreach ($list->result() as $value) {
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$this->general_model->get_string_date_ver2($value->project_date).'</td>';
                        echo '<td>'.$value->project_number.'</td>';
                        echo '<td>'.$value->project_title.'</td>';
                        echo '<td>'.$value->vendor_name.'</td>';
                        echo '<td>'.$value->branch_name.'</td>';
                        echo '<td>'.number_format($value->amount).'</td>';
                        echo '<td>'.$this->project_debit_model->arr_status[$value->project_status].'</td>';
                        echo '<td>';
                                if ($value->project_status == 0){
                                    if ($is_checked){
                                        echo '<a  href="'. site_url('projectdebit/checked/'.$value->project_debit_id).'" class="btn btn-sm btn-warning">Check</a>&nbsp;';
                                    }
                                }
                                
                                if ($value->project_status == 1){
                                    if ($is_checked){
                                        echo '<a  href="'. site_url('projectdebit/approved/'.$value->project_debit_id).'" class="btn btn-sm btn-primary">Approve</a>&nbsp;';
                                    }
                                }
                                
                                if ($value->project_status == 0){
                                    if ($is_edit_val){
                                        echo '<a  href="#" class="btn btn-sm btn-success" onclick="edit_data('.$value->project_debit_id.')">Edit</a>&nbsp;';
                                    }
                                }
                                
                                if ($is_delete_val){
                                    echo '<a  href="#" class="btn btn-sm btn-danger" onclick="delete_data('.$value->project_debit_id.')">Delete</a>&nbsp;';
                                }
                        echo '</td>';
                        echo '</tr>';
                        $no++;
                    }
                    echo '</tbody>';
                }
                ?>
                <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Number</th>
                        <th>Title</th>
                        <th>Vendor</th>
                        <th>Outlet</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>