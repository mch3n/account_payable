<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Projectbalance
 *
 * @author hendramchen
 */
class Projectbalance extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('general_model');
    }
    
    public $category_index = 3;
    public $category = '';
    public $module = '';
    
    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_report) {
            if (($module == $this->asik_model->report_13) && ($string == $category . $module)) {
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
                $header = $this->asik_model->draw_header('Project Balance', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'report/projectbalance_view.php';
                
                
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function get_all_balance() {
        $sql = 'SELECT v.vendor_id, v.vendor_name, SUM(pb.debit) AS sum_debit, 
        SUM(pb.credit) AS sum_credit FROM project_balance AS pb
        INNER JOIN vendor AS v ON v.vendor_id=pb.vendor_id
        GROUP BY v.vendor_id
        ORDER BY v.vendor_name';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_balance_detail($vendor_id=0) {
//        $sql  = 'SELECT pb.pp_id, pb.balance_date,  
//        pb.debit, pb.credit, pb.vendor_id 
//        FROM project_balance AS pb
//        WHERE pb.vendor_id='.$vendor_id.' 
//        ORDER BY pb.balance_date';
        $sql = 'SELECT b.project_debit_id, b.pp_id, d.project_number, d.project_title, b.vendor_id, 
        b.balance_date, d.project_number, 
        SUM(b.debit) AS debit_total, SUM(b.credit) AS credit_total FROM project_balance AS b
        INNER JOIN project_debit AS d ON b.project_debit_id=d.project_debit_id
        WHERE b.vendor_id='.$vendor_id.' 
        GROUP BY b.project_debit_id
        ORDER BY b.project_debit_id';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_vendor_name($vendor_id=0) {
        $sql  = 'SELECT vendor_name FROM vendor ';
        $sql .= 'WHERE vendor_id='.$vendor_id;
        $query = $this->db->query($sql);
        $name = '';
        if ($query->num_rows() != 0){
            $row = $query->row();
            $name = $row->vendor_name;
        }
        return $name;
    }
    
    public function get_balance_by_vendor($vendor_id=0) {
        $sql  = 'SELECT b.*, pp.pp_number, pp.pp_title, pp.pp_status FROM project_balance AS b ';
        $sql .= 'INNER JOIN payment_process AS pp ON pp.pp_id=b.pp_id ';
        $sql .= 'WHERE b.vendor_id='.$vendor_id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function detail($string = '', $vendor_id=0) {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;
                $data['pagecode'] = $string;
                
                $data['detail'] = $this->get_balance_detail($vendor_id);
                $data['project_vendor'] = $this->get_balance_by_vendor($vendor_id);
                //$detail = $this->get_balance_detail($vendor_id);
                $vendor_name = $this->get_vendor_name($vendor_id);
                
//                $pp_in = '';
//                if ($detail->num_rows()!=0){
//                    $ppids = '';
//                    foreach ($detail->result() as $value) {
//                        if ($value->pp_id != 0){
//                            $ppids .= $value->pp_id . ',';
//                        }                        
//                    }
//                    $pp_in = substr($ppids, 0, strlen($ppids)-1);
//                }
//                $data['arr_number'] = $this->get_pp_number($pp_in);
//                $data['arr_status'] = $this->get_pp_status($pp_in);
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
                                .column(4, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(4).footer() ).html(
                                numeral(alltotal1).format("0,0.00")
                        );
                        
                    alltotal2 = api
                                .column(5, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(5).footer() ).html(
                                numeral(alltotal2).format("0,0.00")
                        );
                    
                    alltotal3 = api
                                .column(6, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(6).footer() ).html(
                                numeral(alltotal3).format("0,0.00")
                        );
                }';
                /* ===== end datatable ===== */
                $data['footer_total'] = $footer_total;
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header($vendor_name, 'Detail', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'report/projectbalance_detail.php';                
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function mdetail($string = '', $project_debit_id=0, $vendor_id=0) {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module) {
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;
                $data['pagecode'] = $string;

                $data['detail'] = $this->get_more_detail($project_debit_id);
                
                $detail = $this->get_more_detail($project_debit_id);              
                $pp_in = '';
                if ($detail->num_rows()!=0){
                    $ppids = '';
                    foreach ($detail->result() as $value) {
                        if ($value->pp_id != 0){
                            $ppids .= $value->pp_id . ',';
                        }                        
                    }
                    $pp_in = substr($ppids, 0, strlen($ppids)-1);
                }
                $data['arr_number'] = $this->get_pp_number($pp_in);
                
                $vendor_name = '<a href="'. site_url('projectbalance/detail/'.$string.'/'.$vendor_id).'">'.$this->get_vendor_name($vendor_id).'</a>';
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'Project Balance';
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
                                .column(2, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(2).footer() ).html(
                                numeral(alltotal1).format("0,0.00")
                        );
                        
                    alltotal2 = api
                                .column(3, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(3).footer() ).html(
                                numeral(alltotal2).format("0,0.00")
                        );
                    
                    alltotal3 = api
                                .column(4, { page: "current"})
                                .data()
                                .reduce( function (a, b) {
                                        return intVal(a) + intVal(b);
                                }, 0 );
                        // Update footer
                        $( api.column(4).footer() ).html(
                                numeral(alltotal3).format("0,0.00")
                        );
                }';
                /* ===== end datatable ===== */
                $data['footer_total'] = $footer_total;
                /* ===== end datatable ===== */
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header($vendor_name, 'Detail', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'report/projectbalance_more_detail.php';                
                $this->load->view('template', $data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function get_more_detail($project_debit_id=0) {
        $sql  = 'SELECT * FROM project_balance WHERE project_debit_id='.$project_debit_id.' ';
        $sql .= 'ORDER BY balance_date';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_pp_number($in='') {
        $sql = 'SELECT pp_id, pp_number FROM payment_process WHERE pp_id IN('.$in.')';
        $query = $this->db->query($sql);
        $arr = array();
        if ($query->num_rows()!=0){
            foreach ($query->result() as $value) {
                $arr[$value->pp_id] = $value->pp_number;
            }
        }
        return $arr;
    }
    
    public function get_pp_status($in='') {
        $sql = 'SELECT pp_id, pp_status FROM payment_process WHERE pp_id IN('.$in.')';
        $query = $this->db->query($sql);
        $arr = array();
        if ($query->num_rows()!=0){
            foreach ($query->result() as $value) {
                $arr[$value->pp_id] = $value->pp_status;
            }
        }
        return $arr;
    }
    
}