<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Ref Number</th>
                        <th>Status</th>
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
                    foreach ($detail->result() as $value) {
                        $number = '-';
                        $status = '-';
                        $enc_id = $this->general_model->encrypt_value($value->pp_id);
                        $linkdetail = 'ppdetail/go/' . $ppcode.'/'.$enc_id.'/4/'; 
                        if ($value->debit != 0){
                            $balance += $value->debit;
                        } else {
                            $balance -= $value->credit;
                        }
                        if ($value->pp_id != 0){
                            $status = $pp_status_arr[$arr_status[$value->pp_id]];
                            $enc_id = $this->general_model->encrypt_value($value->pp_id);
                            $linkdetail = 'ppdetail/go/' . $ppcode.'/'.$enc_id.'/4/'; 
                            $number = '<a href="'. site_url($linkdetail).'" target="_blank">'.$arr_number[$value->pp_id].'</a>';
                        }
                        echo '<tr>
                            <td>'.$no.'</td>
                            <td>'.$this->general_model->get_string_date_ver2($value->balance_date).'</td>
                            <td>'.$number.'</td>
                            <td>'.$status.'</td>
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
                        <th>Status</th>
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