<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request; //novo
use DB; //novo




class OrderController extends Controller{
    
    function index(){
        $count = 0;
        $inicio = 0;
        $inicio = microtime(true);

        for($c=1;$c<=1000;$c++){


            ## Tabela orders
            $query_maxid = DB::table('orders')
                ->select(DB::raw('max(id) as id'))
                ->get();

            foreach($query_maxid as $max_id){ 
                $id = $max_id->id + 1; 
            }

            $employee_id = rand(1, 9);
            $order_date = date("Y-m-d H:i:s", time());
            $shipped_date = NULL;
            $shipper_id = rand(1, 3);
            $customer_id = rand(1, 29);
                
            $query_customerbuild = DB::table('customers')
                ->where('id', '=', ''.$customer_id.'')
                ->get();

            foreach($query_customerbuild as $query_customer){
                $ship_name = "$query_customer->first_name"." "."$query_customer->last_name";
                $ship_address = $query_customer->address;
                $ship_city =  $query_customer->city;
                $ship_state_province = $query_customer->state_province;
                $ship_zip_postal_code = $query_customer->zip_postal_code;
                $ship_country_region = $query_customer->country_region;
            }

            $shipping_fee = 0.0000;
            $taxes = 0.0000;
            $payment_type = NULL;
            $paid_date = NULL;
            $notes = NULL;
            $tax_rate = 0;
            $tax_status_id = NULL;
            $status_id = 0;

            DB::table('orders')->insert(
                [
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
                ]
            );

            ## Tabela order_details

            $order_id = $id; //salvando order id as order_id

            $rand = rand(1,4);

            $query_products = DB::table('products')
                ->select(DB::raw('id, list_price, minimum_reorder_quantity'))
                ->inRandomOrder()
                ->take($rand)
                ->get();

            foreach ($query_products as $products){
                $query_maxid_orderdetails = DB::table('order_details')
                ->select(DB::raw('max(id) as id'))
                ->get();

                foreach ($query_maxid_orderdetails as $max_id_orderdetails){ $id = $max_id_orderdetails->id + 1; }

                $rand = $products->minimum_reorder_quantity; //Pedido minimo
                if ($rand <5) {$rand = 5;} //Caso o pedido minimo seja zero, um valor aleatorio dentre 2 e 10 será selecionado
                $rand = rand($rand,$rand*rand(2,5)); //Valor aleatorio de pedido dentre o minimo e um valor de 2 a 5 vezes maior que o pedido minimo previamente especificado por meio de um nestle de rand(). 
                
                DB::table('order_details')->insert( [
                    'id' => $id,
                    'order_id' => $order_id,
                    'product_id' => $products->id,
                    'quantity' => $rand,
                    'unit_price' => $products->list_price,
                    'discount' => 0,
                    'status_id' => 0,
                    'date_allocated' => NULL,
                    'purchase_order_id' => NULL,
                    'inventory_id' => NULL
                ]);
            }

            ## Exibição de Inserções
        
            ## Exibição de Pedido


            echo "[Query " . "$c]";
            echo "</br>";
            echo "[Order ID | Costumer name | Costumer Address | Costumer City]</br></br>";

            $query_order = DB::table('orders')
                ->select(DB::raw('max(id) as id'))
                ->get();

            foreach ($query_order as $order){
                echo $order->id;
                echo " | ";

                $query_order2 = DB::table('orders')->select('ship_name', 'ship_address', 'ship_city')->where('id',$order->id)->get();
                
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

            $query_order_details = DB::table('order_details')
                ->select('order_id', 'quantity', 'unit_price', 'product_id')
                ->where('order_id',$order_id)
                ->get();
            
            foreach($query_order_details as $order_details){
                
                $query_product_details = DB::table('products')
                ->select('product_name', 'id')
                ->where('id', $order_details->product_id)
                ->get();
                
                foreach($query_product_details as $product_details){
                    echo $product_details->id;
                    echo " | ";
                    echo $product_details->product_name;
                    echo " | ";
                    echo $order_details->quantity;
                    echo " | ";
                    echo $order_details->unit_price;
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

        //-------------------------------FINAL-------------------------
        
        return view('tcc.index',[
                'max_id'=> $query_maxid,
                'build_customer'=> $query_customerbuild,
                'products' => $query_products,
                'max_id_orderdetails'=> $query_maxid_orderdetails,
                'last_order'=>$query_order,
                'order_details' => $query_order_details,
                'product_details' => $query_product_details
            ]);
    }
}
