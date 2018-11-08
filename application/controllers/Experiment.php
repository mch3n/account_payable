<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Experiment
 *
 * @author hendramchen
 */
class Experiment extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('supplier_report_model');
        $this->load->model('general_model');
    }

    public $category_index = 0;
    public $category = '';
    public $module = '';

    public function is_check_module($string = '', $category = '', $module = '') {
        $category_dash = $this->asik_model->category_dashboard;
        if ($category == $category_dash) {
            if (($module == $this->asik_model->dash_01) && ($string == $category . $module)) {
                $this->category_index = 0;
                return TRUE;
            }
        }
        return FALSE;
    }
    
    public function get_payment_process_debit() {
        $sql = 'SELECT pp.* FROM payment_process AS pp 
        INNER JOIN supplier AS s ON pp.supplier_id=s.supplier_id
        WHERE pp.pp_number LIKE "SC%" ORDER BY pp.pp_date';
        $query = $this->db->query($sql);
        $data_query = array();
        if ($query->num_rows()!=0){
            foreach ($query->result() as $value) {
                echo '<div style="background-color:#DDD; margin:5px 0px;">';
                echo 'balance_date:'.$value->pp_date .'<br>';
                echo 'supplier_id:'.$value->supplier_id .'<br>';
                echo 'pp_id:'.$value->pp_id .'<br>';
                echo 'debit:'.$value->total .'<br>';
                echo '</div>';

                $data = array(
                    'balance_date' => $value->pp_date,
                    'supplier_id' => $value->supplier_id,
                    'pp_id' => $value->pp_id,
                    'debit' => $value->total
                );
                $data_query[] = $data;
            }
            $this->db->insert_batch('supplier_balance', $data_query);
        }
        return $query;
    }
    
    public function get_payment_process_credit() {
        $sql = 'SELECT pp.*, pv.pv_id, pv.pv_date, pv.trans_id FROM payment_process AS pp 
        INNER JOIN supplier AS s ON pp.supplier_id=s.supplier_id
        INNER JOIN payment_voucher AS pv ON pp.pp_id=pv.pp_id
        WHERE pp.pp_number LIKE "SC%" AND pp_status=4 ORDER BY pp.pp_date';
        $query = $this->db->query($sql);
        $data_query = array();
        if ($query->num_rows()!=0){
            foreach ($query->result() as $value) {
                $trans_id = isset($value->trans_id)? $value->trans_id:0;
                echo '<div style="background-color:#DDD; margin:5px 0px;">';
                echo 'balance_date:'.$value->pp_date .'<br>';
                echo 'supplier_id:'.$value->supplier_id .'<br>';
                echo 'pp_id:'.$value->pp_id .'<br>';
                echo 'credit:'.$value->total .'<br>';
                echo '</div>';

                $data = array(
                    'balance_date' => $value->pv_date,
                    'supplier_id' => $value->supplier_id,
                    'pp_id' => $value->pp_id,
                    'pv_id' => $value->pv_id,
                    'trans_id' => $trans_id,
                    'credit' => $value->total
                );
                $data_query[] = $data;
            }
            $this->db->insert_batch('supplier_balance', $data_query);
        }
        return $query;
    }
    
    public function get_project_debit() {
        $sql = 'SELECT pp.* FROM payment_process AS pp 
        INNER JOIN vendor AS v ON pp.vendor_id=v.vendor_id
        WHERE pp.pp_number LIKE "PR%" ORDER BY pp.pp_date';
        $query = $this->db->query($sql);
        $data_query = array();
        if ($query->num_rows()!=0){
            foreach ($query->result() as $value) {
                echo '<div style="background-color:#DDD; margin:5px 0px;">';
                echo 'balance_date:'.$value->pp_date .'<br>';
                echo 'vendor_id:'.$value->vendor_id .'<br>';
                echo 'pp_id:'.$value->pp_id .'<br>';
                echo 'debit:'.$value->total .'<br>';
                echo '</div>';

                $data = array(
                    'balance_date' => $value->pp_date,
                    'vendor_id' => $value->vendor_id,
                    'pp_id' => $value->pp_id,
                    'debit' => $value->total
                );
                $data_query[] = $data;
            }
            $this->db->insert_batch('project_balance', $data_query);
        }
        return $query;
    }
    
    public function get_project_credit() {
        $sql = 'SELECT pp.*, pv.pv_id, pv.pv_date, pv.trans_id FROM payment_process AS pp 
        INNER JOIN vendor AS v ON pp.vendor_id=v.vendor_id
        INNER JOIN payment_voucher AS pv ON pp.pp_id=pv.pp_id
        WHERE pp.pp_number LIKE "PR%" AND pp_status=4 ORDER BY pp.pp_date';
        $query = $this->db->query($sql);
        $data_query = array();
        if ($query->num_rows()!=0){
            foreach ($query->result() as $value) {
                $trans_id = isset($value->trans_id)? $value->trans_id:0;
                echo '<div style="background-color:#DDD; margin:5px 0px;">';
                echo 'balance_date:'.$value->pp_date .'<br>';
                echo 'vendor_id:'.$value->vendor_id .'<br>';
                echo 'pp_id:'.$value->pp_id .'<br>';
                echo 'credit:'.$value->total .'<br>';
                echo '</div>';

                $data = array(
                    'balance_date' => $value->pv_date,
                    'vendor_id' => $value->vendor_id,
                    'pp_id' => $value->pp_id,
                    'pv_id' => $value->pv_id,
                    'trans_id' => $trans_id,
                    'credit' => $value->total
                );
                $data_query[] = $data;
            }
            $this->db->insert_batch('project_balance', $data_query);
        }
        return $query;
    }
    
    public function get_thirdparty_debit() {
        $sql = 'SELECT pp.* FROM payment_process AS pp 
        INNER JOIN third_party AS t ON pp.third_party_id=t.third_party_id
        WHERE pp.pp_number LIKE "OT%" ORDER BY pp.pp_date';
        $query = $this->db->query($sql);
        $data_query = array();
        if ($query->num_rows()!=0){
            foreach ($query->result() as $value) {
                echo '<div style="background-color:#DDD; margin:5px 0px;">';
                echo 'balance_date:'.$value->pp_date .'<br>';
                echo 'third_party_id:'.$value->third_party_id .'<br>';
                echo 'pp_id:'.$value->pp_id .'<br>';
                echo 'debit:'.$value->total .'<br>';
                echo '</div>';

                $data = array(
                    'balance_date' => $value->pp_date,
                    'third_party_id' => $value->third_party_id,
                    'pp_id' => $value->pp_id,
                    'debit' => $value->total
                );
                $data_query[] = $data;
            }
            $this->db->insert_batch('third_party_balance', $data_query);
        }
        return $query;
    }
    
    public function get_thirdparty_credit() {
        $sql = 'SELECT pp.*, pv.pv_id, pv.pv_date, pv.trans_id FROM payment_process AS pp 
        INNER JOIN third_party AS t ON pp.third_party_id=t.third_party_id
        INNER JOIN payment_voucher AS pv ON pp.pp_id=pv.pp_id
        WHERE pp.pp_number LIKE "OT%" AND pp_status=4 ORDER BY pp.pp_date';
        $query = $this->db->query($sql);
        $data_query = array();
        if ($query->num_rows()!=0){
            foreach ($query->result() as $value) {
                $trans_id = isset($value->trans_id)? $value->trans_id:0;
                echo '<div style="background-color:#DDD; margin:5px 0px;">';
                echo 'balance_date:'.$value->pp_date .'<br>';
                echo 'third_party_id:'.$value->third_party_id .'<br>';
                echo 'pp_id:'.$value->pp_id .'<br>';
                echo 'credit:'.$value->total .'<br>';
                echo '</div>';

                $data = array(
                    'balance_date' => $value->pv_date,
                    'third_party_id' => $value->third_party_id,
                    'pp_id' => $value->pp_id,
                    'pv_id' => $value->pv_id,
                    'trans_id' => $trans_id,
                    'credit' => $value->total
                );
                $data_query[] = $data;
            }
            $this->db->insert_batch('third_party_balance', $data_query);
        }
        return $query;
    }
    
    public function update_outstanding() {
        $sql = 'SELECT third_party_id, pv_number FROM payment_process WHERE third_party_id !=0';
        $query = $this->db->query($sql);
        if ($query->num_rows()!=0){
            foreach ($query->result() as $value) {
                $data = array(
                    'third_party_id' => $value->third_party_id
                );
                $this->db->where('pv_number', $value->pv_number);
                $this->db->update('outstanding', $data);
            }
        }
    }
    
    public function testInsert() {
        $sql = "insert into third_party (third_party_id, third_party_name, description)
                values (29, 'test third party II', 'desc I');";
        $sql .="insert into third_party (third_party_id, third_party_name, description)
                values (30, 'test third party III', 'desc I');";
        $this->db->query($sql);
    }
    
    public function get_json() {
        $arr = [
            'settings' => [
                'key' => "daily_mission_01",
                'value' => [
                    'rewards'=>[
                        [
                            "id"=>"1",
                            "name"=>"1 Voucher Rezeki Vaganza",
                            "type"=>"voucher",
                            "value"=>1
                        ],
                        [
                            "id"=>"2",
                            "name"=>"2 Ticket",
                            "type"=>"ticket",
                            "value"=>2
                        ]
                    ]
                ]
            ],
            'description' => 'this is description',
            'info_page_daily_mission' => 'info page'
        ];

        $json = json_encode($arr);
        $data = json_decode($json);
        echo $data->settings->value->rewards[1]->value;
    }

    public function draw_datatable(){
        /* ===== start datatable ===== */
        $data['datatable_title'] = 'Datatable example ';
        $footer_total = '';
        $data['footer_total'] = $footer_total;
        /* ===== end datatable ===== */
        $data['active_li'] = 0;
        $header = $this->asik_model->draw_header('Datatable', 'View', 0, $this->asik_model->category_dashboard, $this->asik_model->dash_01);
        $data['content_header'] = $header;
        $data['halaman'] = 'datatable_ex.php';

        $this->load->view('template', $data);
    }

    // public function go_datatable($string = '') {
    //     $this->asik_model->is_login();
    //     $category = substr($string, 0, 6);
    //     $module = substr($string, 6, 8);
    //     $is_module = $this->is_check_module($string, $category, $module);
    //     if ($is_module) {
    //         if ($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)) {
    //             $this->category = $category;
    //             $this->module = $module;
    //             /* ===== get active period ===== */
                
    //             $header = $this->asik_model->draw_header('Datatable', $period_title, $this->category_index, $category, $module);
    //             $data['content_header'] = $header;
    //             $data['active_li'] = $this->category_index;
    //             $data['page_name'] = 'Datatable';
    //             $data['halaman'] = 'datatable_ex.php';
    //             $this->load->view('template', $data);
    //         } else {
    //             show_404();
    //         }
    //     } else {
    //         show_404();
    //     }
    // }
    
}
