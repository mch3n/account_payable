<div class="col-xs-12">
    <div class="box box-primary">
        <div class="box-body table-responsive">
            <table id="datatable2" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Credit</th>
                        <th class="text-right">Balance</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <?php
                $trans_id = 0;
                if ($detail->num_rows()!=0){
                    echo '<tbody>';
                    $no = 1;                    
                    $balance = 0;
                    foreach ($detail->result() as $value) {  
                        $trans_id = $value->trans_id;
                        if ($no == 1){
                            $balance = $value->debit;
                        } else {
                            if ($value->debit == 0){
                                $balance = $balance - $value->credit;
                            } else {
                                $balance = $balance + $value->debit;
                            }
                        }
                        echo '<tr>';
                        echo '<td>'.$no.'</td>';
                        echo '<td>'.$this->general_model->get_string_date_ver2($value->balance_date).'</td>';
                        echo '<td class="text-right">'.number_format($value->debit).'</td>';
                        echo '<td class="text-right">'.number_format($value->credit).'</td>';
                        echo '<td class="text-right">'.number_format($balance).'</td>';
                        echo '<td class="text-right">';
                        if ($adjustment_priv){
                            echo '<a href="#" onclick="adjustment('.$value->cash_request_balance_id.')" class="btn btn-sm btn-primary">Adjustment</a>';
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
                        <th class="text-right">Debit</th>
                        <th class="text-right">Credit</th>
                        <th class="text-right">Balance</th>
                        <th class="text-right">Action</th>
                    </tr>
                </tfoot>
            </table>
            <button class="btn btn-sm btn-primary" onclick="add_adjustment(<?php echo $trans_id ?>)">Add Adjustment</button>
        </div>
        <!-- /.box-body -->
    </div>
    <!-- /.box -->
</div>

<script src="<?php echo base_url(); ?>assets/vendor/bootstrap-filestyle/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
<script type="text/javascript">
    var save_method; //for save method string
    var table;
    
    function add_adjustment(trans_id){
        save_method = 'add';
        $('#form')[0].reset(); // reset form on modals
        $('[name="cash_request_balance_id"]').val("0");
        $('[name="transaction_id"]').val(trans_id);
        $('#modal_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('Add Adjustment'); // Set Title to Bootstrap modal title
    }
    
    function adjustment(id)
    {
        save_method = 'update';
        $('#form')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url: "<?php echo site_url('crbalance/ajax_adjustment/') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function (data)
            {
                $('[name="cash_request_balance_id"]').val(data.cash_request_balance_id);
                $('[name="debit"]').val(data.debit);
                $('[name="credit"]').val(data.credit);

                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Adjustment'); // Set title to Bootstrap modal title

            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error get data from ajax');
            }
        });
    }
    
    function save_adjustment()
    {
        var url = "<?php echo site_url('crbalance/save_adjustment') ?>";

        // ajax adding data to database
        $.ajax({
            url: url,
            type: "POST",
            data: $('#form').serialize(),
            dataType: "JSON",
            success: function (data)
            {
                //if success close modal and reload ajax table
                $('#modal_form').modal('hide');
                location.reload();// for reload a page
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                alert('Error adding / update data, because there is empty field');
            }
        });
    }


</script>

<!-- ADD REMARK -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Edit</h4>
            </div>
            <div class="modal-body form">
                <form action="#" id="form">
                    <div class="form-body">

                        <?php
                        echo $cash_request_balance_id;
                        echo $cash_request_id;
                        echo $transaction_id;
                        echo $debit;
                        echo $credit;
                        ?>

                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="btnSave" onclick="save_adjustment()" class="btn btn-primary">Save</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->