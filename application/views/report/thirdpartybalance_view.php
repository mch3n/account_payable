<div class="col-xs-12">
    <div class="box">
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Third Party</th>
                        <th class="text-right">Debit (Total)</th>
                        <th class="text-right">Credit (Total)</th>
                        <th class="text-right">Balance</th>
                    </tr>
                </thead>
                <?php
                if ($list->num_rows()!=0){
                    $no = 1;
                    echo '<tbody>';
                    foreach ($list->result() as $value) {                     
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td><a href="'. site_url('thirdpartybalance/detail/20191341214314/'.$value->third_party_id).'">'.$value->third_party_name.'</a></td>';
                        echo '<td class="text-right">'.number_format($value->sum_debit).'</td>';
                        echo '<td class="text-right">'.number_format($value->sum_credit).'</td>';
                        echo '<td class="text-right">'.number_format($value->sum_debit - $value->sum_credit).'</td>';
                        echo '</tr>';
                        $no++;
                    }
                    echo '</tbody>';
                }
                ?>
                <tfoot>
                    <tr>
                        <th>#</th>
                        <th>Third Party</th>
                        <th class="text-right">Debit (Total)</th>
                        <th class="text-right">Credit (Total)</th>
                        <th class="text-right">Balance</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>