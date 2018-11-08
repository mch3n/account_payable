<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Ref number</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Credit</th>
                        <th class="text-right">Balance</th>
                    </tr>
                </thead>
                <?php
                if ($detail->num_rows()!=0){
                    echo '<tbody>';
                    $no = 1;                    
                    $balance = 0;
                    $ppcode = $this->asik_model->category_configuration.$this->asik_model->config_01;
                    foreach ($detail->result() as $value) { 
                        if ($value->debit == 0){
                            $balance = $balance - $value->credit;
                        } else {
                            $balance = $balance + $value->debit;
                        }
                        $number = '-';
                        $status = '-';
                        if ($value->pp_id != 0){
                            $enc_id = $this->general_model->encrypt_value($value->pp_id);
                            $linkdetail = 'ppdetail/go/' . $ppcode.'/'.$enc_id.'/4/'; 
                            $number = '<a href="'. site_url($linkdetail).'" target="_blank">'.$arr_number[$value->pp_id].'</a>';
                        }
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$this->general_model->get_string_date_ver2($value->balance_date).'</td>';
                        echo '<td>'.$number.'</td>';
                        echo '<td class="text-right">'.number_format($value->debit).'</td>';
                        echo '<td class="text-right">'.number_format($value->credit).'</td>';
                        echo '<td class="text-right">'.number_format($balance).'</td>';
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
                        <th>Ref number</th>
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