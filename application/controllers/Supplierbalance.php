<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Supplierledger
 *
 * @author hendramchen
 */
class Supplierbalance extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
    }
    
    public $category_index = 3;
    public $category = '';
    public $module = '';
    
    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_report) {
            if (($module == $this->asik_model->report_12) && ($string == $category . $module)) {
                $this->category = $category;
                $this->module = $module;
                return TRUE;
            }
        }
        return FALSE;
    }
    
    public function go($string = '') {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;
                $data['pagecode'] = $string;
                $data['list'] = $this->get_all_balance();
                /* ===== start datatable ===== */
                $data['datatable_title'] = '';
                $data['footer_total'] = '';
                /* ===== end datatable ===== */
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Supplier Balance', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'report/supplierbalance_view.php';
                
                
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function get_all_balance() {
        $sql = 'SELECT s.supplier_id, s.supplier_name, SUM(b.debit) AS sum_debit, 
        SUM(b.credit) AS sum_credit FROM supplier_balance AS b
        INNER JOIN supplier AS s ON b.supplier_id=s.supplier_id
        GROUP BY s.supplier_id
        ORDER BY s.supplier_name';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_balance_detail($supplier_id=0) {
        $sql  = 'SELECT b.pp_id, b.balance_date, b.credit_invoice_id, b.pp_id, 
        b.debit, b.credit, b.supplier_id 
        FROM supplier_balance AS b
        WHERE b.supplier_id='.$supplier_id.' 
        ORDER BY b.balance_date';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_supplier_name($supplier_id=0) {
        $sql  = 'SELECT supplier_name FROM supplier ';
        $sql .= 'WHERE supplier_id='.$supplier_id;
        $query = $this->db->query($sql);
        $name = '';
        if ($query->num_rows() != 0){
            $row = $query->row();
            $name = $row->supplier_name;
        }
        return $name;
    }
    
    public function get_arr_credit_invoice($in='') {
        $sql  = 'SELECT credit_invoice_id, credit_invoice_number FROM credit_invoice ';
        $sql .= 'WHERE credit_invoice_id IN('.$in.')';
        $query = $this->db->query($sql);
        $arr = array();
        if ($query->num_rows() != 0){
            foreach ($query->result() as $value) {
                $arr[$value->credit_invoice_id] = $value->credit_invoice_number;
            }
        }
        return $arr;
    }
    
    public function get_arr_payment_process($in='') {
        $arr = array();
        if ($in != ''){
            $sql  = 'SELECT pp_id, pp_number FROM payment_process ';
            $sql .= 'WHERE pp_id IN('.$in.')';
            $query = $this->db->query($sql);
            if ($query->num_rows() != 0){
                foreach ($query->result() as $value) {
                    $arr[$value->pp_id] = $value->pp_number;
                }
            }
        }
        
        return $arr;
    }
    
    public function get_arr_status($in='') {
        $arr = array();
        if ($in != ''){
            $sql  = 'SELECT pp_id, pp_status FROM payment_process ';
            $sql .= 'WHERE pp_id IN('.$in.')';
            $query = $this->db->query($sql);
            if ($query->num_rows() != 0){
                foreach ($query->result() as $value) {
                    $arr[$value->pp_id] = $value->pp_status;
                }
            }
        }
        
        return $arr;
    }
    
    public function get_arr_status_credit_invoice($in='') {
        $sql  = 'SELECT credit_invoice_id, po_status FROM credit_invoice ';
        $sql .= 'WHERE credit_invoice_id IN('.$in.')';
        $query = $this->db->query($sql);
        $arr = array();
        if ($query->num_rows() != 0){
            foreach ($query->result() as $value) {
                $arr[$value->credit_invoice_id] = $value->po_status;
            }
        }
        return $arr;
    }
    
    public function get_arr_receive_date($in='') {
        $sql  = 'SELECT credit_invoice_id, receive_date FROM credit_invoice ';
        $sql .= 'WHERE credit_invoice_id IN('.$in.')';
        $query = $this->db->query($sql);
        $arr = array();
        if ($query->num_rows() != 0){
            
            foreach ($query->result() as $value) {
                if ($value->receive_date == ''){
                    $arr[$value->credit_invoice_id] = '0000-00-00';
                } else {
                    $arr[$value->credit_invoice_id] = $value->receive_date;
                }
                
            }
        }
        return $arr;
    }
    
    public function detail($string = '', $supplier_id=0) {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;
                $data['pagecode'] = $string;
                
                $data['detail'] = $this->get_balance_detail($supplier_id);
                $detail = $this->get_balance_detail($supplier_id);
                $in_ci = '';
                $in_pp = '';
                if ($detail->num_rows()!=0){
                    foreach ($detail->result() as $value) {
                        if ($value->credit_invoice_id != 0){
                            $in_ci .= $value->credit_invoice_id . ','; 
                        }
                        if ($value->pp_id != 0){
                            $in_pp .= $value->pp_id . ',';
                        }
                    }
                }
                $in_ci_id = substr($in_ci, 0, strlen($in_ci)-1);
                $in_pp_id = substr($in_pp, 0, strlen($in_pp)-1);
                
                $data['arr_ci_number'] = $this->get_arr_credit_invoice($in_ci_id);
                $data['arr_pp_number'] = $this->get_arr_payment_process($in_pp_id);
                $data['arr_pp_status'] = $this->get_arr_status($in_pp_id);
                $data['arr_ci_status'] = $this->get_arr_status_credit_invoice($in_ci_id);
                $data['arr_receive_date'] = $this->get_arr_receive_date($in_ci_id);
                
                $supplier_name = $this->get_supplier_name($supplier_id);
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'Supplier Balance';
                $footer_total = '"footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                            return typeof i === "string" ?
                                    i.replace(/[\$,]/g, "")*1 :
                                    typeof i === "number" ?
                                            i : 0;
                    };
    
                    alltotal1 = api
                                .column(5, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(5).footer() ).html(
                                numeral(alltotal1).format("0,0.00")
                        );
                        
                    alltotal2 = api
                                .column(6, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(6).footer() ).html(
                                numeral(alltotal2).format("0,0.00")
                        );
                    
                    alltotal3 = api
                                .column(7, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(7).footer() ).html(
                                numeral(alltotal3).format("0,0.00")
                        );
                }';
                /* ===== end datatable ===== */
                $data['footer_total'] = $footer_total;
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header($supplier_name, 'Detail', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'report/supplierbalance_detail.php';                
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
}
