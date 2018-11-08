<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Ref Number</th>
                        <th>Outlet</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Credit</th>
                        <th class="text-right">Balance</th>
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
                    $linkdetail = '';
                    $outlet = '';
                    foreach ($detail->result() as $value) {  
                        $number = '';
                        if ($value->debit != 0){
                            $balance += $value->debit;
                        } else {
                            $balance -= $value->credit;
                        }
                        if ($value->pp_id != 0){
                            $number = $arr_pp_number[$value->pp_id];
                            $enc_id = $this->general_model->encrypt_value($value->pp_id);
                            $linkdetail = 'ppdetail/go/' . $ppcode.'/'.$enc_id.'/3/';
                            $outlet = $arr_pp_branch[$value->pp_id];
                        } 
                        if ($value->receive_bank_id != 0){
                            if (isset($arr_rb_number[$value->receive_bank_id])){
                                $number = $arr_rb_number[$value->receive_bank_id];
                            }
                            
                            $enc_id = $this->general_model->encrypt_value($value->receive_bank_id);
                            $linkdetail = 'receiveinbank/detail/20191121214305/'.$enc_id;
                            if (isset($arr_rb_branch[$value->receive_bank_id])){
                                $outlet = $arr_rb_branch[$value->receive_bank_id];
                            }                            
                        }
                        echo '<tr>
                            <td>'.$no.'</td>
                            <td>'.$this->general_model->get_string_date_ver2($value->balance_date).'</td>
                            <td><a href="'. site_url($linkdetail).'" target="_blank">'.$number.'</a></td>
                            <td>'.$outlet.'</td>
                            <td class="text-right">'. number_format($value->debit).'</td>
                            <td class="text-right">'. number_format($value->credit).'</td>
                            <td class="text-right">'. number_format($balance).'</td>
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
                        <th>Ref Number</th>
                        <th>Outlet</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Credit</th>
                        <th class="text-right">Balance</th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
</div>