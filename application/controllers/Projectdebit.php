<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Projectdebit
 *
 * @author hendramchen
 */
class Projectdebit extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('project_debit_model');
        $this->load->model('general_model');
    }
    
    public $category_index = 2;
    public $category = '';
    public $module = '';

    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_configuration) {
            if (($module == $this->asik_model->config_03) && ($string == $category . $module)) {
                $this->category = $category;
                $this->module = $module;
                return TRUE;
            }
        }
        return FALSE;
    }
    
    public function go($string = '', $button=0){
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        $is_module = $this->is_check_module($string, $category, $module);
        if ($is_module){
            if($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)){
                $this->category = $category;
                $this->module = $module;
                /* privilege action button */
                // value = TRUE or FALSE
                $data['is_add_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_add);
                $data['is_edit_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_edit);
                $data['is_delete_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_delete);
                $data['is_upload'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_upload);
                $data['is_download'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_download);
                $data['is_checked'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_checked);
                $data['is_approved'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_approved);
                
                $this->load->helper('form');
                $this->load->helper('file');
                $start_date = $this->input->post('start_date')==''? date('Y-m-d'):$this->input->post('start_date');
                $end_date = $this->input->post('end_date')==''? date('Y-m-d'):$this->input->post('end_date');
                $field_search = $this->input->post('field_search');
                $keyword = $this->input->post('keyword');

                /*======= start form field =======*/                
                /* for pp project */
                $vendor_list = $this->project_debit_model->get_vendor_list();
                $vendor_opt = array();
                if ($vendor_list->num_rows()!=0){
                    $vendor_opt[0] = 'None';
                    foreach ($vendor_list->result() as $value) {
                        $vendor_opt[$value->vendor_id] = $value->vendor_name;
                    }
                }

                $this->load->model('branch_model');
                $branch_data = $this->branch_model->get_branch_list();
                $branch_opt = array();
                if ($branch_data->num_rows()!=0){
                    $branch_opt[0] = 'None';
                    foreach ($branch_data->result() as $value) {
                        $branch_opt[$value->branch_id] = $value->branch_name;
                    }
                }

                $pd_number = $this->general_model->get_generate_number('PD', 'project_debit', 'project_debit_id');
                $data['project_debit_id'] = $this->general_model->draw_hidden_field('project_debit_id', '');
                $data['project_number_disabled'] = $this->general_model->draw_text_disabled('Project Number', 'project_number_disabled', $pd_number);
                $data['project_number'] = $this->general_model->draw_hidden_field('project_number', $pd_number); 
                $data['project_date'] = $this->general_model->draw_datepicker('Date', 1, 'project_date', date('Y-m-d'));
                $data['project_title'] = $this->general_model->draw_text_field('Title', 1, 'project_title', '', '', '');
                $data['vendor_id'] = $this->general_model->draw_select('Vendor', 0, 'vendor_id', 1, $vendor_opt, '');
                $data['amount'] = $this->general_model->draw_input_currency('Amount', 0, 'amount', '');
                $data['branch_id'] = $this->general_model->draw_select('Outlet', 0, 'branch_id', 1, $branch_opt, '');
                                
                /* end form */
    
                /*=============================================================*/
                if ($button != 0) {
                    $year = date('Y');
                    $month = date('m');
                    $day = date('d');
                    //////////////////                    
                    switch ($button) {
                        case 1:
                            $start_date = date('Y-m-d');
                            $end_date = date('Y-m-d');
                            break;
                        case 2:
                            $start_date = date('Y-m-d',strtotime("-1 days"));
                            $end_date = date('Y-m-d',strtotime("-1 days"));
                            break;
                        case 3:
                            $signupdate = $year.'-'.$month.'-'.$day;
                            $signupweek = date("W",strtotime($signupdate));

                            $dto = new DateTime();
                            $start_date = $dto->setISODate($year, $signupweek, 0)->format('Y-m-d');
                            $end_date = $dto->setISODate($year, $signupweek, 6)->format('Y-m-d');
                            break;
                        case 4:
                            $start_date = $year.'-'.$month.'-01';
                            $end_date = $end = date("Y-m-t", strtotime($start_date));
                            break;
                        case 5:
                            if ($month == 1){
                                $last_month = '12';
                                $year = $year - 1;
                            } else {
                                $last_month = $month - 1;
                            }
                            $start_date = $year.'-'.$last_month.'-01';
                            $end_date = $end = date("Y-m-t", strtotime($start_date));
                            break;
                        case 6:
                            $start_date = '2018-01-01';
                            $end_date = date('Y-m-d');
                            break;
                    }
                }
                
                $data['list'] = $this->project_debit_model->get_project_debit_list($start_date, $end_date, $field_search, $keyword);
                $fields = array(
                    "project_number"=>"ID", 
                    "vendor_name"=>"Vendor name"
                    );
                $data['field_opt'] = $fields;
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'Project Debit';
                $footer_total = '"footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                            return typeof i === "string" ?
                                    i.replace(/[\$,]/g, "")*1 :
                                    typeof i === "number" ?
                                            i : 0;
                    };
                    // Total over all pages
                    total = api
                            .column(6)
                            .data()
                            .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                            }, 0 );
                    // Total over this page
                    pageTotal = api
                            .column(6, { page: "current"})
                            .data()
                            .reduce( function (a, b) {
                                    return intVal(a) + intVal(b);
                            }, 0 );
                    // Update footer
                    $( api.column(6).footer() ).html(
                            numeral(pageTotal).format("0,0.00")
                    );
                }';
                $data['footer_total'] = $footer_total;
                /* ===== end datatable ===== */
                $header = $this->asik_model->draw_header('Project Debit', 'view', $this->category_index, $category, $module);
                $data['pagecode'] = $string;
                $data['start_date'] = $start_date;
                $data['end_date'] = $end_date;
                $data['active_li'] = $this->category_index;
                $data['content_header'] = $header;
                $data['show_modal'] = 'project_debit/project_debit_modal.php';
                $data['halaman'] = 'project_debit/project_debit_view.php';  
                
                /* datatable */
               
                $this->load->view('template', $data);
            } else {
                 show_404();
            }
        } else {
            show_404();
        }
    }
    
    public function ajax_edit($id) {
        $data = $this->project_debit_model->get_project_debit_by_id($id)->row();
        echo json_encode($data);
    }
    
    public function projectdebit_add() {        
        $this->load->library('form_validation');        
        $this->form_validation->set_rules('project_title', 'Title', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->project_debit_model->insert();
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function projectdebit_update() {
        $this->load->library('form_validation');
        $this->form_validation->set_rules('project_title', 'Title', 'required');
        $this->form_validation->set_rules('amount', 'Amount', 'required');
        
        if ($this->form_validation->run() == TRUE) {
            $this->project_debit_model->update();
            echo json_encode(array("status" => TRUE));
        }
    }
    
    public function projectdebit_delete($id=0) {
        $this->project_debit_model->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
    }
    
    public function checked($id) {
        $data = array(
            'project_status' => 1
        );
        $this->db->where('project_debit_id', $id);
        $this->db->update('project_debit', $data);
        /*== redirect ==*/
        $back = '/projectdebit/go/' . $this->asik_model->category_configuration;
        $back .= $this->asik_model->config_03 . '/';
        redirect($back);
    }
    
    public function approved($id) {
        $data = array(
            'project_status' => 2
        );
        $this->db->where('project_debit_id', $id);
        $this->db->update('project_debit', $data);
        // get data project balance
        $pdebit = $this->project_debit_model->get_project_debit_by_id($id);
        if ($pdebit->num_rows()!=0){
            $row = $pdebit->row();
            // insert to project balance
            $balance = array(
                'balance_date'=>$row->project_date,
                'vendor_id'=>$row->vendor_id,
                'pp_id'=>0,
                'project_debit_id'=>$row->project_debit_id,
                'debit'=>$row->amount,
                'credit'=>0
            );
            $this->db->insert('project_balance', $balance);
        }
        
        /*== redirect ==*/
        $back = '/projectdebit/go/' . $this->asik_model->category_configuration;
        $back .= $this->asik_model->config_03 . '/';
        redirect($back);
    }
    
    
}
