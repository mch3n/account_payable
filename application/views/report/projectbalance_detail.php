<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Number</th>
                        <th>Title</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Credit</th>
                        <th class="text-right">Balance</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <?php
                if ($detail->num_rows()!=0){
                    $pp_status_arr = array(
                    '<span class="label label-danger">Draft</span>', 
                    '<span class="label label-warning">Cross Check</span>', 
                    '<span class="label label-info">Checked</span>', 
                    '<span class="label label-primary">Approved</span>',
                    '<span class="label label-success">Closed</span>'
                    );
                    echo '<tbody>';
                    $no = 1;
                    $ppcode = $this->asik_model->category_configuration.$this->asik_model->config_01;
                    $balance = 0;
                    foreach ($detail->result() as $value) {
                        $number = '-';
                        $status = '-';
                        $balance = $value->debit_total - $value->credit_total;
//                        $enc_id = $this->general_model->encrypt_value($value->pp_id);
//                        $linkdetail = 'ppdetail/go/' . $ppcode.'/'.$enc_id.'/4/'; 
//                        if ($value->debit_total != 0){
//                            $balance += $value->debit_total;
//                        } else {
//                            $balance -= $value->credit_total;
//                        }
//                        if ($value->pp_id != 0){
//                            $status = $pp_status_arr[$arr_status[$value->pp_id]];
//                            $enc_id = $this->general_model->encrypt_value($value->pp_id);
//                            $linkdetail = 'ppdetail/go/' . $ppcode.'/'.$enc_id.'/4/'; 
//                            $number = '<a href="'. site_url($linkdetail).'" target="_blank">'.$arr_number[$value->pp_id].'</a>';
//                        }
                        echo '<tr>
                            <td>'.$no.'</td>
                            <td>'.$this->general_model->get_string_date_ver2($value->balance_date).'</td>
                            <td>'.$value->project_number.'</td>
                            <td>'.$value->project_title.'</td>
                            <td class="text-right">'. number_format($value->debit_total).'</td>
                            <td class="text-right">'. number_format($value->credit_total).'</td>
                            <td class="text-right">'. number_format($balance).'</td>
                            <td class="text-right"><a href="'. site_url('projectbalance/mdetail/20191341214313/'.$value->project_debit_id.'/'.$value->vendor_id).'" class="btn btn-sm btn-primary">Detail</a></td>
                        </tr>';
                        
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
                        <th class="text-right">Debit</th>
                        <th class="text-right">Credit</th>
                        <th class="text-right">Balance</th>
                        <th class="text-right">Action</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
</div>

<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3>Daftar PP yang belum link ke Project Debit</h3>
        </div>
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Number</th>
                        <th>Title</th>
                        <th>Status</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <?php
                if ($project_vendor->num_rows()!=0){
                    $pp_status_arr = array(
                    '<span class="label label-danger">Draft</span>', 
                    '<span class="label label-warning">Cross Check</span>', 
                    '<span class="label label-info">Checked</span>', 
                    '<span class="label label-primary">Approved</span>',
                    '<span class="label label-success">Closed</span>'
                    );
                    echo '<tbody>';
                    $no = 1;
                    $ppcode = $this->asik_model->category_configuration.$this->asik_model->config_01;
                    foreach ($project_vendor->result() as $value) {
                        $enc_id = $this->general_model->encrypt_value($value->pp_id);
                        $linkdetail = 'ppdetail/go/' . $ppcode.'/'.$enc_id.'/4/'; 
                        echo '<tr>
                            <td>'.$no.'</td>
                            <td>'.$this->general_model->get_string_date_ver2($value->balance_date).'</td>
                            <td><a target="_blank" href="'. site_url($linkdetail).'">'.$value->pp_number.'</a></td>
                            <td>'.$value->pp_title.'</td>
                            <td>'. $pp_status_arr[$value->pp_status] .'</td>
                            <td class="text-right">'. number_format($value->credit).'</td>
                        </tr>';
                        
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
                        <th>Status</th>
                        <th class="text-right">Total</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
</div>