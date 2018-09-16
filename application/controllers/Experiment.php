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
    
    public function generate_string_third_party() {
        $pp_number = '42
45
47
48
49
50
51
53
59
60
61
65
69
70
72
74
75
76
77
78
82
89
91
92
93
94
96
97
98
99
100
109
112
113
115
116
118
119
120
123
124
125
126
128
129
132
133
134
141
144
145
154
162
169
170
171
174
176
177
178
179
180
181
184
199
204
205
209
210
211
213
214
219
220
237
249
250
251
288
313
337
338
339
340
341
342
343
344
345
346
367
380
389
392
446
473
483
582
587
594
611
646
653
663
695
715
729
732
733
735
742
743
744
746
751
753
754
758
763
768
776
777';
        
$third_party_id = '2
1
3
0
3
3
3
3
3
3
3
0
3
3
2
0
4
3
2
1
3
0
4
4
6
2
6
6
5
7
8
0
4
3
3
8
9
3
3
4
11
10
5
12
2
14
3
13
2
5
5
20
4
15
16
17
5
3
4
4
4
4
4
3
2
46
46
48
11
4
2
34
4
4
3
2
3
2
62
63
20
22
20
20
21
21
19
19
12
20
2
3
2
3
3
5
3
5
8
2
24
5
2
3
25
3
3
26
5
3
3
3
3
3
3
3
8
4
4
3
3
3';

$ppid = '593
583
726
822
771
692
844
755
777
915
1042
1135
1206
1190
1197
1289
1249
883
1275
1444
1393
1476
1477
1544
1659
1651
1654
1655
1653
1792
1830
1876
1911
1749
1909
1873
1848
2005
2133
2134
2257
2256
2192
2314
2281
2388
2289
2360
2153
2438
2439
2568
2603
2626
2627
2723
2745
2760
2892
2894
2895
2896
2897
2699
3155
3280
3281
3365
3370
3368
3288
2666
3427
3428
3499
3666
3681
3670
3862
4004
4037
4042
4036
4035
4049
4041
4029
4038
4039
4034
4206
4310
4350
4268
4544
4604
4605
4885
4942
4948
5180
5402
5272
5530
5881
5867
5996
6090
6063
6117
6148
6176
6177
6207
6279
6264
6318
6354
6423
6427
6429
6178';
        $arr = explode("\n", $pp_number);
        $arr_id = explode("\n", $third_party_id);
        $arr_pp = explode("\n", $ppid);
        $in = 'IN(';
        for($i=0; $i<sizeof($arr); $i++){
            echo '<div>UPDATE outstanding SET third_party_id='.$arr_id[$i].', pp_id='.$arr_pp[$i].' WHERE outstanding_id='.$arr[$i].';</div>';
            $in .='\''.$arr[$i].'\',';
        }
        $in = substr($in, 0, strlen($in)-1);
        $in .= ');';
        
    }
    
    public function testInsert() {
        $sql = "insert into third_party (third_party_id, third_party_name, description)
                values (29, 'test third party II', 'desc I');";
        $sql .="insert into third_party (third_party_id, third_party_name, description)
                values (30, 'test third party III', 'desc I');";
        $this->db->query($sql);
    }
    
}
