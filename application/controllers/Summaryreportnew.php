<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Summaryreport
 *
 * @author Hendra McHen
 */
class Summaryreportnew extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('supplier_report_model');
        $this->load->model('general_model');
    }

    public $category_index = 3;
    public $category = '';
    public $module = '';

    public $array_tbl = array(array());
    public $array_total = array(array());
    public $start_date = '';
    public $end_date = '';
    

    public function is_check_module($string = '', $category = '', $module = '') {
        if ($category == $this->asik_model->category_report) {
            if (($module == $this->asik_model->report_01) && ($string == $category . $module)) {
                $this->category = $category;
                $this->module = $module;
                return TRUE;
            }
        }
        return FALSE;
    }

    public function get_filter_date($button){
        $year = date('Y');
        $month = date('m');
        $day = date('d');

        switch ($button) {
            case 1:
                $this->start_date = date('Y-m-d');
                $this->end_date = date('Y-m-d');
                break;
            case 2:
                $this->start_date = date('Y-m-d', strtotime("-1 days"));
                $this->end_date = date('Y-m-d', strtotime("-1 days"));
                break;
            case 3:
                $signupdate = $year . '-' . $month . '-' . $day;
                $signupweek = date("W", strtotime($signupdate));

                $dto = new DateTime();
                $this->start_date = $dto->setISODate($year, $signupweek, 0)->format('Y-m-d');
                $this->end_date = $dto->setISODate($year, $signupweek, 6)->format('Y-m-d');
                break;
            case 4:
                $this->start_date = $year . '-' . $month . '-01';
                $this->end_date = $end = date("Y-m-t", strtotime($this->start_date));
                break;
            case 5:
                if ($month == 1) {
                    $last_month = '12';
                    $year = $year - 1;
                } else {
                    $last_month = $month - 1;
                }
                $this->start_date = $year . '-' . $last_month . '-01';
                $this->end_date = $end = date("Y-m-t", strtotime($this->start_date));
                break;
            case 6:
                $this->start_date = '2018-01-01';
                $this->end_date = date('Y-m-d');
                break;
        }
    }

    public function go($string = '', $button = 0, $startd = '', $endd = '') {
        $this->asik_model->is_login();
        $category = substr($string, 0, 6);
        $module = substr($string, 6, 8);
        // $is_module = $this->is_check_module($string, $category, $module);
        // if ($is_module) {
        //     if ($this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_view_data)) {
                $this->category = $category;
                $this->module = $module;
                /* start privilege */
                // value = TRUE or FALSE
                $data['action_add_val'] = $this->asik_model->is_privilege($category, $module, $this->session->userdata('priv_group_id'), $this->asik_model->action_add);
                /* end privilege */
                $this->load->helper('form');
                $data['pagecode'] = $string;
                $this->start_date = $this->input->post('start_date');
                $this->end_date = $this->input->post('end_date');
                if ($startd != '') {
                    $this->start_date = $startd;
                }
                if ($endd != '') {
                    $this->end_date = $endd;
                }

                $branch = $this->get_branch_list();
                $data['branch'] = $branch;

                
                $this->array_tbl[0][0] = '<strong>Opening Bank Balance</strong>';
                $this->array_tbl[1][0] = '<strong>Receipt in Bank</strong>';
                $this->array_tbl[2][0] = ' # From Revenue Bank';
                $this->array_tbl[3][0] = ' # Borrow Received';
                $this->array_tbl[4][0] = ' # Borrow Returned Inward';
                $this->array_tbl[5][0] = '<strong>Payment from Bank</strong>';
                $this->array_tbl[6][0] = ' # Expenses';
                $this->array_tbl[7][0] = ' # O/S Cash Request';
                $this->array_tbl[8][0] = ' # O/S Third Party';
                $this->array_tbl[9][0] = ' # O/S Outlet (Borrow Given)';
                $this->array_tbl[10][0] = ' # O/S Borrow Given (CR)'; // *start | update 2018-05-25                
                $this->array_tbl[11][0] = ' # Borrow Returned Outward'; // *start | update 2018-05-25
                $this->array_tbl[12][0] = '<strong>Closing Balance Before Adjustment</strong>';
                $this->array_tbl[13][0] = ' # Adjustment Nota Receive';
                $this->array_tbl[14][0] = '<strong>Closing Bank Balance</strong>';
                
                $this->get_filter_date($button);
      
                $prev_balance = $this->get_bank_balance($this->start_date);
                $receivebank = $this->get_receive_bank($this->start_date, $this->end_date);
                $received = $this->get_received($this->start_date, $this->end_date);
                $inward = $this->get_inward($this->start_date, $this->end_date);
                $expenses = $this->get_expense($this->start_date, $this->end_date);
                $outstanding_cr = $this->get_outstanding($this->start_date, $this->end_date, 1);
                $outstanding_th = $this->get_outstanding($this->start_date, $this->end_date, 3);
                $borrow_given = $this->get_outstanding($this->start_date, $this->end_date, 2);
                $borrow_given_cr = $this->get_outstanding($this->start_date, $this->end_date, 4);
                $borrow_returned = $this->get_borrow_returned($this->start_date, $this->end_date);
                $adjustment = $this->get_adjustment($this->start_date, $this->end_date);

                $data['prev_balance'] = $prev_balance;
                // update 14 June 2018
                // cek report file by start_date, end_date
                $report_type = 1;
                $reporthistory = $this->general_model->get_report_by_date($this->start_date, $this->end_date, $report_type);
                $report_id = 0;
                $checked_name = '0';
                $approved_name = '0';
                $report_file = '0';
                if ($reporthistory->num_rows() != 0) {
                    $row = $reporthistory->row();
                    $report_id = $row->report_file_id;
                    $checked_name = $this->general_model->get_user_by_id($row->checked_by);
                    if ($row->approved_by != 0) {
                        $approved_name = $this->general_model->get_user_by_id($row->approved_by);
                    }
                    $report_file = $row->file_name;
                }
                $data['report_id'] = $report_id;
                $data['checked_name'] = $checked_name;
                $data['approved_name'] = $approved_name;
                $data['report_file'] = $report_file;
                $data['report_type'] = $report_type;
                $data['url_module'] = 'summaryreport';

                // inisialissi array
                $this->get_inisialisasi($branch);
                $total = 0;
                // opening balance || previous balance
                $this->draw_previous_balance($prev_balance);
                // baris receive bank
                $this->draw_receive_bank($receivebank, $branch);
                // borrow received
                $this->draw_borrow_received($received, $branch);
                // borrow returned inward
                $this->draw_inward($inward, $branch);
                // total receive
                $this->draw_total_receive($branch);
                // expense
                $this->draw_expense($expenses, $branch);
                // o/s cash request
                $this->draw_os_cash_request($outstanding_cr, $branch);
                // o/s third party
                $this->draw_os_third_party($outstanding_th, $branch);
                // o/s outlet
                $this->draw_os_outlet($borrow_given, $branch);
                // borrow given cr
                $this->draw_borrow_given_cr($borrow_given_cr, $branch);
                // outward
                $this->draw_outward($borrow_returned, $branch);
                // total payment
                $this->draw_total_payment($branch);
                // before adjustment
                $this->draw_before_adjustment($branch);
                // adjustment
                $this->draw_adjustment($adjustment, $branch);
                // closing bank balance
                $this->draw_closing_bank_balance($branch);

                $data['array_tbl'] = $this->array_tbl;

                /* form search */
                $data['start_date'] = $this->start_date;
                $data['end_date'] = $this->end_date;
                /* ===== start datatable ===== */
                $data['datatable_title'] = 'Summary Report (from ' . $this->start_date . ' to ' . $this->end_date . ')';
                $footer_total = '"footerCallback": function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
                    var intVal = function ( i ) {
                            return typeof i === "string" ?
                                    i.replace(/[\$,]/g, "")*1 :
                                    typeof i === "number" ?
                                            i : 0;
                    };';

                $strtotal = '';
                $footer_total .= $strtotal . '}';
                $data['footer_total'] = $footer_total;
                /* ===== end datatable ===== */
                $data['active_li'] = $this->category_index;
                $header = $this->asik_model->draw_header('Summary Report', 'View', $this->category_index, $this->category, $this->module);
                $data['content_header'] = $header;
                $data['halaman'] = 'report/summary_report1.php';

                $this->load->view('template', $data);
        //     } else {
        //         show_404();
        //     }
        // } else {
        //     show_404();
        // }
    }

    public function get_inisialisasi($branch=array()){
        for($row=0; $row<15; $row++){
            $k = 1;
            foreach ($branch as $b) {
                $this->array_tbl[$row][$k] = 0;
                $this->array_total[$row][$k] = 0;
                $k++;
            }
            $this->array_tbl[$row][$k] = 0;
            $this->array_total[$row][$k] = 0;
        }
    }

    public function draw_previous_balance($prev_balance){
        $total = 0;
        if (sizeof($prev_balance)>0){
            $k = 1;
            foreach ($prev_balance as $value) {
                if ($k == sizeof($prev_balance)+1){
                    $this->array_tbl[0][$k] = $value;
                    $this->array_total[0][$k] = $value;
                } else {
                    $this->array_tbl[0][$k] = number_format($value);
                    $this->array_total[0][$k] = $value;
                }
                $total += $value;
                $k++;
            }
            $this->array_tbl[0][$k] = $total;
            $this->array_total[0][$k] = $total;
        }
    }

    public function draw_receive_bank($receivebank, $branch){
        if ($receivebank->num_rows() != 0) {
            $total = 0;
            foreach ($receivebank->result() as $value) {
                $k = 1;
                foreach ($branch as $b) {
                    if ($value->branch_name == $b) {
                        $this->array_tbl[2][$k] = '<a target="_blank" href="' . site_url('receiveinbank/go/20191121214305/0/' . $this->start_date . '/' . $this->end_date . '/' . $b) . '">' . number_format($value->total) . '</a>';
                        $this->array_total[2][$k] = $value->total;
                        $total = $total + $this->array_total[2][$k];
                    }
                    $k++;
                }
                $this->array_tbl[2][$k] = $total;
                $this->array_total[2][$k] = $total;
            }
        }
    }

    public function draw_borrow_received($received, $branch){
        $total = 0;
        if ($received->num_rows() != 0) {
            foreach ($received->result() as $value) {
                $k = 1;
                foreach ($branch as $b) {
                    if ($value->branch_name == $b) {
                        $this->array_tbl[3][$k] = '<a target="_blank" href="' . site_url('cashreceived/go/20191121214303/0/' . $this->start_date . '/' . $this->end_date . '/' . $b) . '">' . number_format($value->total) . '</a>';
                        $this->array_total[3][$k] = $value->total;
                        $total = $total + $this->array_total[3][$k];
                    }
                    $k++;
                }
                $this->array_tbl[3][$k] = $total;
                $this->array_total[3][$k] = $total;
            }
        }
    }

    public function draw_inward($inward, $branch){
        $total = 0;
        if ($inward->num_rows() != 0) {
            foreach ($inward->result() as $value) {
                $k = 1;
                foreach ($branch as $b) {
                    if ($value->branch_name == $b) {
                        $this->array_tbl[4][$k] = '<a target="_blank" href="' . site_url('cashreceived/go/20191121214303/0/' . $this->start_date . '/' . $this->end_date . '/' . $b) . '">' . number_format($value->total) . '</a>';
                        $this->array_total[4][$k] = $value->total;
                        $total = $total + $this->array_total[4][$k];
                    }
                    $k++;
                }
                $this->array_tbl[4][$k] = $total;
                $this->array_total[4][$k] = $total;
            }
        }
    }

    public function draw_total_receive($branch){
        $k = 1;
        foreach ($branch as $b) {
            $this->array_total[1][$k] = $this->array_total[2][$k] + $this->array_total[3][$k] + $this->array_total[4][$k];
            $this->array_tbl[1][$k] = number_format($this->array_total[1][$k]);
            $k++;
        }
        $this->array_tbl[1][$k] = $this->array_total[2][$k] + $this->array_total[3][$k] + $this->array_total[4][$k];
        $this->array_total[1][$k] = $this->array_total[2][$k] + $this->array_total[3][$k] + $this->array_total[4][$k];
    }

    public function draw_expense($expenses, $branch){
        $total = 0;
        if ($expenses->num_rows() != 0) {
            foreach ($expenses->result() as $value) {
                $k = 1;
                foreach ($branch as $b) {
                    if ($value->branch_name == $b) {
                        $this->array_tbl[6][$k] = '<a target="_blank" href="' . site_url('expensereport/go/20191341214303/0/' . $this->start_date . '/' . $this->end_date) . '">' . number_format($value->total) . '</a>';
                        $this->array_total[6][$k] = $value->total;
                        $total = $total + $this->array_total[6][$k];
                    }
                    $k++;
                }
                $this->array_tbl[6][$k] = $total;
                $this->array_total[6][$k] = $total;
            }
        }
    }

    public function draw_os_cash_request($outstanding_cr, $branch){
        $total = 0;
        if ($outstanding_cr->num_rows() != 0) {
            foreach ($outstanding_cr->result() as $value) {
                $k = 1;
                foreach ($branch as $b) {
                    if ($value->branch_name == $b) {
                        $this->array_tbl[7][$k] = '<a target="_blank" href="' . site_url('outstandingreport/go/20191341214304/0/' . $this->start_date . '/' . $this->end_date) . '">' . number_format($value->total) . '</a>';
                        $this->array_total[7][$k] = $value->total;
                        $total = $total + $this->array_total[7][$k];
                    }
                    $k++;
                }
                $this->array_tbl[7][$k] = $total;
                $this->array_total[7][$k] = $total;
            }
        }
    }

    public function draw_os_third_party($outstanding_th, $branch){
        $total = 0;
        if ($outstanding_th->num_rows() != 0) {
            foreach ($outstanding_th->result() as $value) {
                $k = 1;
                foreach ($branch as $b) {
                    if ($value->branch_name == $b) {
                        $this->array_tbl[8][$k] = '<a target="_blank" href="' . site_url('outstandingreport/go/20191341214306/0/' . $this->start_date . '/' . $this->end_date) . '">' . number_format($value->total) . '</a>';
                        $this->array_total[8][$k] = $value->total;
                        $total = $total + $this->array_total[8][$k];
                    }
                    $k++;
                }
                $this->array_tbl[8][$k] = $total;
                $this->array_total[8][$k] = $total;
            }
        }
    }

    public function draw_os_outlet($borrow_given, $branch){
        $total = 0;
        if ($borrow_given->num_rows() != 0) {
            foreach ($borrow_given->result() as $value) {
                $k = 1;
                foreach ($branch as $b) {
                    if ($value->branch_name == $b) {
                        $this->array_tbl[9][$k] = '<a target="_blank" href="' . site_url('outstandingreport/go/20191341214305/0/' . $this->start_date . '/' . $this->end_date) . '">' . number_format($value->total) . '</a>';
                        $this->array_total[9][$k] = $value->total;
                        $total = $total + $this->array_total[9][$k];
                    }
                    $k++;
                }
                $this->array_tbl[9][$k] = $total;
                $this->array_total[9][$k] = $total;
            }
        }
    }

    public function draw_borrow_given_cr($borrow_given_cr, $branch){
        $total = 0;
        if ($borrow_given_cr->num_rows() != 0) {
            foreach ($borrow_given_cr->result() as $value) {
                $k = 1;
                foreach ($branch as $b) {
                    if ($value->branch_name == $b) {
                        $this->array_tbl[10][$k] = '<a target="_blank" href="' . site_url('outstandingreport/go/20191341214305/0/' . $this->start_date . '/' . $this->end_date . '/4/') . '">' . number_format($value->total) . '</a>';
                        $this->array_total[10][$k] = $value->total;
                        $total = $total + $this->array_total[10][$k];
                    }
                    $k++;
                }
                $this->array_tbl[10][$k] = $total;
                $this->array_total[10][$k] = $total;
            }
        }
    }

    public function draw_outward($borrow_returned, $branch){
        $total = 0;
        if ($borrow_returned->num_rows() != 0) {
            foreach ($borrow_returned->result() as $value) {
                $k = 1;
                foreach ($branch as $b) {
                    if ($value->branch_name == $b) {
                        $this->array_tbl[11][$k] = '<a target="_blank" href="' . site_url('cashreturned/go/20191121214304/0/' . $this->start_date . '/' . $this->end_date) . '">' . number_format($value->total) . '</a>';
                        $this->array_total[11][$k] = $value->total;
                        $total = $total + $this->array_total[11][$k];
                    }
                    $k++;
                }
                $this->array_tbl[11][$k] = $total;
                $this->array_total[11][$k] = $total;
            }
        }
    }

    public function draw_total_payment($branch){
        $k = 1;
        foreach ($branch as $b) {
            $this->array_total[5][$k] = $this->array_total[6][$k] + $this->array_total[7][$k] + $this->array_total[8][$k] + $this->array_total[9][$k] + $this->array_total[11][$k];
            $this->array_tbl[5][$k] = number_format($this->array_total[5][$k]);

            $k++;
        }
        $this->array_tbl[5][$k] = $this->array_total[6][$k] + $this->array_total[7][$k] + $this->array_total[8][$k] + $this->array_total[9][$k] + $this->array_total[11][$k];
        $this->array_total[5][$k] = $this->array_total[6][$k] + $this->array_total[7][$k] + $this->array_total[8][$k] + $this->array_total[9][$k] + $this->array_total[11][$k];
    }

    public function draw_before_adjustment($branch){
        $k = 1;
        foreach ($branch as $b) {
            $this->array_total[12][$k] = $this->array_total[0][$k] + $this->array_total[1][$k] - $this->array_total[5][$k];
            $this->array_tbl[12][$k] = number_format($this->array_total[12][$k]);
            $k++;
        }
        $this->array_tbl[12][$k] = $this->array_total[0][$k] + $this->array_total[1][$k] - $this->array_total[5][$k];
        $this->array_total[12][$k] = $this->array_total[0][$k] + $this->array_total[1][$k] - $this->array_total[5][$k];
    }

    public function draw_adjustment($adjustment, $branch){
        $total = 0;
        if ($adjustment->num_rows() != 0) {
            foreach ($adjustment->result() as $value) {
                $k = 1;
                foreach ($branch as $b) {
                    if ($value->branch_name == $b) {
                        $this->array_tbl[13][$k] = '<a target="_blank" href="' . site_url('expensereport/go/20191341214303/0/' . $this->start_date . '/' . $this->end_date) . '/1/">' . number_format($value->total) . '</a>';
                        $this->array_total[13][$k] = $value->total;
                        $total = $total + $this->array_total[13][$k];
                    }
                    $k++;
                }
                $this->array_tbl[13][$k] = $total;
                $this->array_total[13][$k] = $total;
            }
        }
    }

    public function draw_closing_bank_balance($branch){
        $total = 0;
        $k = 1;
        foreach ($branch as $b) {
            $this->array_total[14][$k] = $this->array_total[12][$k] + $this->array_total[13][$k];
            $this->array_tbl[14][$k] = number_format($this->array_total[14][$k]);
            $total = $total + $this->array_total[14][$k];
            $k++;
        }
        $this->array_tbl[14][$k] = $total;
        $this->array_total[14][$k] = $total;
    }

    public function get_branch_list() {
        $sql = 'SELECT DISTINCT b.branch_name, b.branch_id FROM branch AS b 
        ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        $branch = array();
        if ($query->num_rows() != 0) {
            foreach ($query->result() as $value) {
                $branch[] = $value->branch_name;
            }
        }
        return $branch;
    }

    public function get_expense($start_date = '', $end_date = '') {
        $sql = 'SELECT b.branch_name, SUM(ex.amount) AS total FROM expense AS ex ';
        $sql .= 'INNER JOIN branch AS b ON ex.branch_id=b.branch_id ';
        $sql .= 'WHERE ex.expense_date BETWEEN "' . $start_date . '" AND "' . $end_date . '" ';
        $sql .= 'GROUP BY b.branch_id ';
        $sql .= 'ORDER BY b.branch_id ASC';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_receive_bank($start_date = '', $end_date = '') {
        $sql = 'SELECT SUM(t.amount) AS total, b.branch_name FROM transactions AS t
        INNER JOIN account AS a ON t.account_id=a.account_id
        INNER JOIN branch AS b ON a.branch_id=b.branch_id
        WHERE t.trans_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" AND a.account_type=2 AND a.account_name LIKE "Retained%" 
        GROUP BY a.account_id';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_outstanding($start_date = '', $end_date = '', $type = 0) {
        $sql = 'SELECT SUM(os.amount) AS total, b.branch_name  FROM outstanding AS os 
        INNER JOIN branch AS b ON os.branch_id=b.branch_id 
        WHERE os.outstanding_status IN (0,1)  
        AND os.outstanding_date BETWEEN "' . $start_date . '" AND "' . $end_date . '" AND os.outstanding_type=' . $type . ' ';
        $sql .= 'GROUP BY os.branch_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_received($start_date = '', $end_date = '') {
        $sql = 'SELECT SUM(cr.amount) AS total, b.branch_name FROM cash_receive AS cr 
        INNER JOIN branch AS b ON cr.branch_id=b.branch_id 
        INNER JOIN account AS a ON cr.account_from=a.account_id 
        WHERE cr.cash_receive_date BETWEEN "' . $start_date . '" AND "' . $end_date . '"
        GROUP BY cr.branch_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_borrow_given($start_date = '', $end_date = '') {
        $sql = 'SELECT SUM(cr.amount) AS total, b.branch_name FROM cash_receive AS cr 
        INNER JOIN account AS a ON cr.account_from=a.account_id 
        INNER JOIN branch AS b ON a.branch_id=b.branch_id 
        WHERE cr.cash_receive_status < 2 
        AND cr.cash_receive_date BETWEEN "' . $start_date . '" AND "' . $end_date . '"
        GROUP BY cr.branch_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_borrow_returned($start_date = '', $end_date = '') {
        $sql = 'SELECT SUM(ct.amount) AS total, b.branch_name FROM cash_return AS ct
        INNER JOIN branch AS b ON b.branch_id=ct.branch_id
        WHERE ct.cash_return_date BETWEEN "' . $start_date . '" AND "' . $end_date . '"
        GROUP BY ct.branch_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_opening_balance($start_date = '', $end_date = '') {
        $sql = 'SELECT SUM(op.amount)AS total, b.branch_name FROM opening_balance AS op
        INNER JOIN branch AS b ON b.branch_id=op.branch_id
        WHERE op.opening_balance_date BETWEEN "' . $start_date . '" AND "' . $end_date . ' "
        GROUP BY op.branch_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_previous_balance($start_date = '', $end_date = '') {
        $sql = 'SELECT L.account_id,  SUM(L.debit) AS total_debit, SUM(L.credit) AS total_credit FROM ledger AS L 
        INNER JOIN transactions AS t ON L.trans_id=t.trans_id
        WHERE  L.account_id IN (7,8,9,10,11,12,13,65) AND trans_date BETWEEN "' . $start_date . '" AND "' . $end_date . '" 
        GROUP BY L.account_id ORDER BY L.account_id ASC';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_inward($start_date = '', $end_date = '') {
        $sql = 'SELECT SUM(ct.amount) AS total, b.branch_name, ct.account_to, a.account_id, b.branch_id, ct.cash_return_date 
        FROM cash_return AS ct 
        INNER JOIN account AS a ON a.account_id=ct.account_to 
        INNER JOIN branch AS b ON b.branch_id=a.branch_id 
        WHERE ct.cash_return_date BETWEEN "' . $start_date . '" AND "' . $end_date . '" GROUP BY b.branch_id ORDER BY b.branch_id';
        $query = $this->db->query($sql);
        return $query;
    }

    public function get_adjustment($start_date = '', $end_date = '') {
        $sql = 'SELECT b.branch_name, SUM(ex.amount) AS total FROM expense AS ex ';
        $sql .= 'INNER JOIN branch AS b ON ex.branch_id=b.branch_id ';
        $sql .= 'WHERE ex.expense_date BETWEEN "' . $start_date . '" AND "' . $end_date . '" AND expense_type=1 ';
        $sql .= 'GROUP BY b.branch_id ';
        $sql .= 'ORDER BY b.branch_id ASC';
        $query = $this->db->query($sql);
        return $query;
    }

    public function action_checked($start_date = '', $end_date = '') {
        $this->general_model->action_checked($start_date, $end_date, 1, '/summaryreport/go/20191341214301/0/');
    }

    public function action_approved($report_file_id = 0, $start_date = '', $end_date = '') {
        $this->general_model->action_approved($report_file_id, $start_date, $end_date, '/summaryreport/go/20191341214301/0/');
    }

    public function do_upload() {
        $this->general_model->do_upload('/summaryreport/go/20191341214301/0/');
    }
    
    public function get_bank_balance($startdate='') {
        $previous = array();
        if ($startdate == '2018-02-01'){
            $opening = $this->get_opening_balance('2018-02-01', '2018-02-28');
            if ($opening->num_rows() != 0) {
                foreach ($opening->result() as $value) {
                    $previous[] = $value->total;
                }
            }
        } else {
            $end_date = date('Y-m-d', strtotime('-1 day', strtotime($startdate)));
            $sql = 'SELECT b.branch_id, a.account_id, b.branch_name, SUM(L.debit) AS total_debit, SUM(L.credit) AS total_credit
            FROM ledger AS L INNER JOIN transactions AS t ON L.trans_id=t.trans_id 
            INNER JOIN account AS a ON L.account_id=a.account_id 
            INNER JOIN branch AS b ON b.branch_id=a.branch_id 
            WHERE L.account_id IN (7,8,9,10,11,12,13,65) AND t.trans_date BETWEEN "2018-02-01" AND "'.$end_date.'" ';
            $sql .= 'GROUP BY b.branch_id, a.account_id';
            $query = $this->db->query($sql);
            if ($query->num_rows()!=0){
                foreach ($query->result() as $value) {
                    $total = $value->total_debit - $value->total_credit;
                    $previous[] = $total;
                }
            }
        }
        return $previous;
    }
    
    

}
