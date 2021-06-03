<?php

use Cake\ORM\TableRegistry;

$orders = TableRegistry::getTableLocator()->get('Orders');
$order_details = TableRegistry::getTableLocator()->get('OrderDetails');
$products = TableRegistry::getTableLocator()->get('Products');
$customers = TableRegistry::getTableLocator()->get('Customers');



$count = 0;
$inicio = 0;
$inicio = microtime(true);

for($c=1;$c<=1;$c++){

    ## Tabela orders
    $orders = TableRegistry::getTableLocator()->get('Orders');

    $query_maxid = $orders->find('all',array(
        'order'=>'Orders.id DESC',
        'limit'=>1
    ));

    foreach($query_maxid as $max_id){ 
        $id = $max_id->id + 1; 
    }

    $employee_id = rand(1, 9);
    $order_date = date("Y-m-d H:i:s", time());
    $shipped_date = NULL;
    $shipper_id = rand(1, 3);
    $customer_id = rand(1, 29);
    
    $customers = TableRegistry::getTableLocator()->get('Customers');

    $query_customers = $customers->find('all',array(
        array('conditions'=>array('id'=>$customer_id)),
    ));

    foreach($query_customers as $row){
        $ship_name = "$row->first_name"." "."$row->last_name";
        $ship_address = $row->address;
        $ship_city =  $row->city;
        $ship_state_province = $row->state_province;
        $ship_zip_postal_code = $row->zip_postal_code;
        $ship_country_region = $row->country_region;
    }

    $shipping_fee = 0.0000;
    $taxes = 0.0000;
    $payment_type = NULL;
    $paid_date = NULL;
    $notes = NULL;
    $tax_rate = 0;
    $tax_status_id = NULL;
    $status_id = 0;

    $orders_insert = $orders->query();

    $orders_insert->insert(['employee_id', 'order_date', 'shipped_date', 'shipper_id', 'customer_id', 'ship_name', 'ship_address', 'ship_city', 'ship_state_province', 'ship_zip_postal_code', 'ship_country_region', 'shipping_fee', 'taxes', 'payment_type', 'paid_date', 'notes', 'tax_rate', 'tax_status_id', 'status_id'])
        ->values([
            'id' => $id,
            'employee_id' => $employee_id,
            'order_date'  => $order_date,
            'shipped_date'  => $shipped_date,
            'shipper_id'  => $shipper_id,
            'customer_id' => $customer_id,
            'ship_name'  => $ship_name,
            'ship_address'  => $ship_address,
            'ship_city'  => $ship_city,
            'ship_state_province'  => $ship_state_province,
            'ship_zip_postal_code'  => $ship_zip_postal_code,
            'ship_country_region'  => $ship_country_region,
            'shipping_fee'  => $shipping_fee,
            'taxes'  => $taxes,
            'payment_type'  => $payment_type,
            'paid_date'  => $paid_date,
            'notes'  => $notes,
            'tax_rate'  => $tax_rate,
            'tax_status_id'  => $tax_status_id,
            'status_id'  => $status_id
        ])
        ->execute();
    // $order_insert = $orders->newEmptyEntity();
    // $order_insert->id = $id;
    // $order_insert->employee_id = $employee_id;
    // $order_insert->order_date = $order_date;
    // $order_insert->shipped_date = $shipped_date;
    // $order_insert->shipper_id = $shipper_id;
    // $order_insert->customer_id = $customer_id;
    // $order_insert->ship_name = $ship_name;
    // $order_insert->ship_address = $ship_address;
    // $order_insert->ship_city = $ship_city;
    // $order_insert->ship_state_province = $ship_state_province;
    // $order_insert->ship_zip_postal_code = $ship_zip_postal_code;
    // $order_insert->ship_country_region = $ship_country_region;
    // $order_insert->shipping_fee = $shipping_fee;
    // $order_insert->taxes = $taxes;
    // $order_insert->payment_type = $payment_type;
    // $order_insert->paid_date = $paid_date;
    // $order_insert->notes = $notes;
    // $order_insert->tax_rate = $tax_rate;
    // $order_insert->tax_status_id = $tax_status_id;
    // $order_insert->status_id = $status_id;

    // if ($orders->save($order_insert)) {
    //     $id = $order_insert->id;
    // };

    ## Tabela order_details

    $order_id = $id; //salvando order id as order_id

    $rand = rand(1,4);

    $products = TableRegistry::getTableLocator()->get('Products');
    $query_products = $products->find('all',array(
        'order'=>'rand()',
        'limit'=>$rand
    ));

    foreach ($query_products as $product_list){
        $order_details = TableRegistry::getTableLocator()->get('OrderDetails');
        $query_maxid_orderdetails = $order_details->find('all',array(
            'order'=>'OrderDetails.id DESC',
            'limit'=>1
        ));

        foreach ($query_maxid_orderdetails as $max_id_orderdetails){ $id = $max_id_orderdetails->id + 1; }
        
        $rand = $product_list->minimum_reorder_quantity; //Pedido minimo
        if ($rand <5) {$rand = 5;} //Caso o pedido minimo seja zero, um valor aleatorio dentre 2 e 10 será selecionado
        $rand = rand($rand,$rand*rand(2,5)); //Valor aleatorio de pedido dentre o minimo e um valor de 2 a 5 vezes maior que o pedido minimo previamente especificado por meio de um nestle de rand(). 
        
        $orderdetails_insert = $order_details->query();

        $orderdetails_insert->insert(['id', 'order_id', 'product_id', 'quantity', 'unit_price', 'discount', 'status_id', 'date_allocated', 'purchase_order_id', 'inventory_id'])
        ->values([
            'id' => $id,
            'order_id' => $order_id,
            'product_id' => $product_list->id,
            'quantity' => $rand,
            'unit_price' => $product_list->list_price,
            'discount' => 0,
            'status_id' => 0,
            'date_allocated' => NULL,
            'purchase_order_id' => NULL,
            'inventory_id' => NULL
        ])
        ->execute();
    }

    // ## Exibição de Inserções

    // ## Exibição de Pedido


    echo "[Query " . "$c]";
    echo "</br>";
    echo "[Order ID | Costumer name | Costumer Address | Costumer City]</br></br>";

    $orders = TableRegistry::getTableLocator()->get('Orders');

    $query_order = $orders->find('all',array(
        'order'=>'Orders.id DESC',
        'limit'=>1
    ));

    

    foreach ($query_order as $order){
        echo $order->id;
        echo " | ";

        $query_order2 = $orders->find('all',array(
            'conditions' => array('id' => $order->id),
            'fields' => array('ship_name', 'ship_address', 'ship_city')
    ));
        
        foreach ($query_order2 as $order2){
            echo $order2->ship_name;
            echo " | ";
            echo $order2->ship_address;
            echo " | ";
            echo $order2->ship_city;
            echo "</br></br>";
        }
    }

    ## Exibição de Produto

    echo "[Product ID | Product Name | Quantity | Price]</br></br>";

    $order_details = TableRegistry::getTableLocator()->get('OrderDetails');

    $query_order_details = $order_details->find('all',array(
        'conditions' => array('order_id' => $order_id),
        'fields' => array('order_id', 'quantity', 'unit_price', 'product_id')
    )); 
    
    foreach($query_order_details as $details){
        $products = TableRegistry::getTableLocator()->get('Products');
        $query_product_details = $products->find('all',array(
            'conditions' => array('id' => $details->product_id),
            'fields' => array('product_name', 'id')
        )); 
        
        foreach($query_product_details as $product_details){
            echo $product_details->id;
            echo " | ";
            echo $product_details->product_name;
            echo " | ";
            echo $details->quantity;
            echo " | ";
            echo $details->unit_price;
            echo "</br>";
        }
    }
    echo "</br>";

    $count++;
}//FINAL FOR

    echo "Total de Pedidos Registrados: ".$count;
    $total_time = microtime(true) - $inicio;
    $total_time = $total_time*100000;
    $total_time = (int)$total_time;
    $total_time = $total_time/100;
    echo '</br>Tempo de execução do script: ' . number_format((float)$total_time, 2, '.', '') . " ms";