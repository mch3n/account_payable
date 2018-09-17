<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Project_debit_model
 *
 * @author hendramchen
 */
class Project_debit_model extends CI_Model {
    public $arr_status = array(
        '<span class="label label-warning">To Be Check</span>', 
        '<span class="label label-info">To Be Approve</span>', 
        '<span class="label label-primary">Approved</span>', 
        '<span class="label label-success">Closed</span>'
        );

    public function get_project_debit_list($start_date='', $end_date='', $field_search='', $keyword=''){
        $sql  = 'SELECT p.*, v.vendor_name, b.branch_name FROM project_debit AS p ';
        $sql .= 'INNER JOIN vendor AS v ON p.vendor_id=v.vendor_id ';
        $sql .= 'INNER JOIN branch AS b ON p.branch_id=b.branch_id ';
        $sql .= 'WHERE  ';
        if ($start_date != '' && $end_date != ''){
            $sql .= ' p.project_date BETWEEN "'.$start_date.'" AND "'.$end_date.'" ';
            if ($keyword != ''){
                $sql .= ' AND '.$field_search.' LIKE "%'.$keyword.'%" ';
            }
        } else {
            if ($keyword != ''){
                $sql .= ' '.$field_search.' LIKE "%'.$keyword.'%" ';
            } else {
                $sql .= ' p.project_date BETWEEN "" AND "" ';
            }
        }
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_project_debit_by_id($id=0) {
        $sql  = 'SELECT p.*, v.vendor_name, b.branch_name FROM project_debit AS p ';
        $sql .= 'INNER JOIN vendor AS v ON p.vendor_id=v.vendor_id ';
        $sql .= 'INNER JOIN branch AS b ON p.branch_id=b.branch_id ';
        $sql .= 'WHERE p.project_debit_id='.$id;
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function get_vendor_list() {
        $sql  = 'SELECT * FROM vendor ';
        $sql .= 'ORDER BY vendor_name';
        $query = $this->db->query($sql);
        return $query;
    }
    
    public function insert() {                        
        $project_number = $this->input->post('project_number');
        $project_date = $this->input->post('project_date');
        $project_title = $this->input->post('project_title');
        $vendor_id = $this->input->post('vendor_id');
        $amount = $this->general_model->change_decimal($this->input->post('amount'));
        $branch_id = $this->input->post('branch_id');
        $data = array(
            'project_number' => $project_number,
            'project_date' => $project_date,
            'project_title' => $project_title,
            'vendor_id' => $vendor_id,
            'amount' => $amount,
            'checked_by' => 0,
            'approved_by' => 0,
            'file_name' => '0',
            'project_status' => 0,
            'branch_id' => $branch_id
        );
        $this->db->insert('project_debit', $data);
    }
    
    public function update() {
        $project_debit_id = $this->input->post('project_debit_id');
        $project_date = $this->input->post('project_date');
        $project_title = $this->input->post('project_title');
        $vendor_id = $this->input->post('vendor_id');
        $amount = $this->general_model->change_decimal($this->input->post('amount'));
        $branch_id = $this->input->post('branch_id');
        $data = array(
            'project_date' => $project_date,
            'project_title' => $project_title,
            'vendor_id' => $vendor_id,
            'amount' => $amount,
            'branch_id' => $branch_id
        );
        $this->db->where('project_debit_id', $project_debit_id);
        $this->db->update('project_debit', $data);
        // cek di project_balance
        $project_balance = $this->get_project_balance($project_debit_id);
        if ($project_balance->num_rows()!=0){
            $data_balance = array(
                'balance_date'=>$project_date,
                'debit'=>$amount
            );
            $this->db->where('project_debit_id', $project_debit_id);
            $this->db->update('project_balance', $data_balance);
        }
    }
    
    public function delete_by_id($id) {
        $this->db->where('project_debit_id', $id);
        $this->db->delete('project_debit');
        
        $this->db->where('project_debit_id', $id);
        $this->db->delete('project_balance');
    }
    
    public function get_project_balance($id=0) {
        $sql = 'SELECT * FROM project_balance WHERE project_balance_id='.$id;
        $query = $this->db->query($sql);
        return $query;
    }
}
