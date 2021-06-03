<?php
$inicio = 0;
$inicio = microtime(true);



$db = \Config\Database::connect();
$db2 = \Config\Database::connect();

$count = 0;

for($c=1;$c<=1000;$c++){ //c = número de pedidos a serem criados
    ## Tabela orders
    $query = $db->query('SELECT MAX(id) as id FROM orders');
    $result = $query->getResultArray();
    foreach ($result as $row){ $id = $row['id'] + 1; }
    $employee_id = rand(1, 9);
    $order_date = date("Y-m-d H:i:s", time());
    $shipped_date = NULL;
    $shipper_id = rand(1, 3);
    $customer_id = rand(1, 29);
    $query = $db->query(
        'SELECT last_name, first_name, address, city, state_province, zip_postal_code, country_region
        FROM customers WHERE id = '.$customer_id.'');
    $result = $query->getResultArray();
    foreach ($result as $row){
        $ship_name = $row['first_name']." ".$row['last_name'];
        $ship_address = $row['address'];
        $ship_city =  $row['city'];
        $ship_state_province = $row['state_province'];
        $ship_zip_postal_code = $row['zip_postal_code'];
        $ship_country_region = $row['country_region'];
    }
    $shipping_fee = 0.0000;
    $taxes = 0.0000;
    $payment_type = NULL;
    $paid_date = NULL;
    $notes = NULL;
    $tax_rate = 0;
    $tax_status_id = NULL;
    $status_id = 0;

    $data = [
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
    ];
    $db->table('orders')->insert($data);
    #echo $db->affectedRows();
    
    ## Tabela order_details
    
    $order_id = $id; //saving order's id as order_id
    
    $rand = rand(1,4);
    
    $query = $db->query('SELECT id, list_price, minimum_reorder_quantity FROM products ORDER BY RAND() LIMIT '.$rand.'');
    $result = $query->getResultArray();
    foreach ($result as $row){
        $query2 = $db2->query('SELECT MAX(id) as id FROM order_details');
        $result2 = $query2->getResultArray();
        foreach ($result2 as $row2){ $id = $row2['id'] + 1; }
        $rand = $row['minimum_reorder_quantity']; //Pedido minimo
        if ($rand <5) {$rand = 5;} //Caso o pedido minimo seja zero, um valor aleatorio dentre 2 e 10 será selecionado
        $rand = rand($rand,$rand*rand(2,5)); //Valor aleatorio de pedido dentre o minimo e um valor de 2 a 5 vezes maior que o pedido minimo previamente especificado por meio de um nestle de rand(). 
        $data = [
            'id' => $id,
            'order_id' => $order_id,
            'product_id' => $row['id'],
            'quantity' => $rand,
            'unit_price' => $row['list_price'],
            'discount' => 0,
            'status_id' => 0,
            'date_allocated' => NULL,
            'purchase_order_id' => NULL,
            'inventory_id' => NULL
        ];
        $db->table('order_details')->insert($data);
    }

    ## Exibição de Inserções
    
    ## Exibição de Pedido
    
    $query = $db->query(
        'SELECT MAX(id) as id, ship_name, ship_address, ship_city FROM orders'
    );
    $results = $query->getResultArray();

    echo "[Query " . "$c]";
    echo "</br></br>";
    echo "[Order ID | Costumer name | Costumer Address | Costumer City]</br></br>";
    foreach ($results as $row){
        echo $row['id'];
        echo " | ";
        echo $row['ship_name'];
        echo " | ";
        echo $row['ship_address'];
        echo " | ";
        echo $row['ship_city'];
        echo "</br></br>";
    }

    ## Exibição de Produto
    
    $query = $db->query(
        'SELECT order_details.order_id, order_details.quantity, order_details.unit_price, products.product_name, products.id, 
        order_details.product_id 
        FROM order_details, products 
        WHERE order_details.product_id = products.id AND order_details.order_id = '.$order_id.''
    );
    
    $results = $query->getResultArray();

    echo "[Product ID | Product Name | Quantity | Price]</br></br>";
    foreach ($results as $row){
        echo $row['id'];
        echo " | ";
        echo $row['product_name'];
        echo " | ";
        echo $row['quantity'];
        echo " | ";
        echo $row['unit_price'];
        echo "</br>";
    }
    echo "</br>";
    
    $count++;
}

echo "Total de Pedidos Registrados: ".$count;
$total_time = microtime(true) - $inicio;
$total_time = $total_time*100000;
$total_time = (int)$total_time;
$total_time = $total_time/100;
echo '</br>Tempo de execução do script: ' . number_format((float)$total_time, 2, '.', '') . " ms";