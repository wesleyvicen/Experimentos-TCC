<?php
echo "hello world";

$ship_name = 0;
#$query = DB::select('SELECT ship_name FROM orders WHERE id = 30',[1]);

$query = DB::table('orders')
                     ->select(DB::raw('ship_name'))
                     ->where('id','=', 1)
                     #->groupBy('status')
                     ->get();
echo $query->ship_name;

    // $employee_id = rand(1, 9);
    // $order_date = date("Y-m-d H:i:s", time());
    // $shipped_date = NULL;
    // $shipper_id = rand(1, 3);
    // $customer_id = rand(1, 29);
    // $query = $db->query(
    //     'SELECT last_name, first_name, address, city, state_province, zip_postal_code, country_region
    //     FROM customers WHERE id = '.$customer_id.'');
    // $result = $query->getResultArray();
    // foreach ($result as $row){
    //     $ship_name = $row['first_name']." ".$row['last_name'];
    //     $ship_address = $row['address'];
    //     $ship_city =  $row['city'];
    //     $ship_state_province = $row['state_province'];
    //     $ship_zip_postal_code = $row['zip_postal_code'];
    //     $ship_country_region = $row['country_region'];
    // }