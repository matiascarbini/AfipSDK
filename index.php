<?php
    include 'vendor/afipsdk/afip.php/src/Afip.php';

    $cuit = 20339698407;
    $afip = new Afip([
        'CUIT' => $cuit, 
        'production' => false,            
        //'cert' => '',
        //'key' => '',
        //'res_folder' => '',
        //'ta_folder' => '',
    ]);

    // Muestra el objeto $afip
    $server_status = $afip->ElectronicBilling->GetServerStatus();

    echo 'Este es el estado del servidor:';
    echo '<pre>';
    print_r($server_status);
    echo '</pre>';
    echo '<hr>';


    //Devuelve el número del último comprobante creado para el punto de venta 1 y el tipo de comprobante 11 (Factura C)
    $last_voucher = $afip->ElectronicBilling->GetLastVoucher(1,11);
    echo 'Numero del ultimo comprobante para el punto de venta 1';
    echo "<br>";
    echo $last_voucher;
    echo "<hr>";

    //Devuelve informacion del comprobante    
    $voucher_info = $afip->ElectronicBilling->GetVoucherInfo($last_voucher,1,11); //Devuelve la información del ultimo comprobante para el punto de venta 1 y el tipo de comprobante 11 (Factura B)

    if($voucher_info === NULL){
        echo 'El comprobante no existe';
    }
    else{
        echo 'Esta es la información del comprobante:';
        echo '<pre>';
        print_r($voucher_info);
        echo '</pre>';
    }    
    echo "<hr>";     
    
    //Devuelve el proximo nro a facturar    
    $next_voucher = $last_voucher + 1;
    echo 'Proximo numero a facturar:';
    echo '<pre>';
    print_r($next_voucher);
    echo '</pre>';
    echo "<hr>";         

    // Crea un comprobante y retorna el CAE
    $data = array(
        'CantReg' 		=> 1, // Cantidad de comprobantes a registrar
        'PtoVta' 		=> 1, // Punto de venta
        'CbteTipo' 		=> 11, // Tipo de comprobante (ver tipos disponibles) 
        'Concepto' 		=> 1, // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
        'DocTipo' 		=> 80, // Tipo de documento del comprador (ver tipos disponibles)
        'DocNro' 		=> 20111111112, // Numero de documento del comprador
        'CbteDesde' 	=> $next_voucher, // Numero de comprobante o numero del primer comprobante en caso de ser mas de uno
        'CbteHasta' 	=> $next_voucher, // Numero de comprobante o numero del ultimo comprobante en caso de ser mas de uno
        'CbteFch' 		=> intval(date('Ymd')), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
        'ImpTotal' 		=> 150, // Importe total del comprobante
        'ImpTotConc' 	=> 0, // Importe neto no gravado
        'ImpNeto' 		=> 150, // Importe neto gravado
        'ImpOpEx' 		=> 0, // Importe exento de IVA
        'ImpIVA' 		=> 0, //Importe total de IVA
        'ImpTrib' 		=> 0, //Importe total de tributos
        'FchServDesde' 	=> NULL, // (Opcional) Fecha de inicio del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
        'FchServHasta' 	=> NULL, // (Opcional) Fecha de fin del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
        'FchVtoPago' 	=> NULL, // (Opcional) Fecha de vencimiento del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
        'MonId' 		=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
        'MonCotiz' 		=> 1, // Cotización de la moneda usada (1 para pesos argentinos)  
        /*
        'CbtesAsoc' 	=> array( // (Opcional) Comprobantes asociados
            array(
                'Tipo' 		=> 6, // Tipo de comprobante (ver tipos disponibles) 
                'PtoVta' 	=> 1, // Punto de venta
                'Nro' 		=> 1, // Numero de comprobante
                'Cuit' 		=> 20111111112 // (Opcional) Cuit del emisor del comprobante
                )
            ),
        'Tributos' 		=> array( // (Opcional) Tributos asociados al comprobante
            array(
                'Id' 		=>  99, // Id del tipo de tributo (ver tipos disponibles) 
                'Desc' 		=> 'Ingresos Brutos', // (Opcional) Descripcion
                'BaseImp' 	=> 150, // Base imponible para el tributo
                'Alic' 		=> 5.2, // Alícuota
                'Importe' 	=> 7.8 // Importe del tributo
            )
        ),         
        'Iva' 			=> array( // (Opcional) Alícuotas asociadas al comprobante
            array(
                'Id' 		=> 5, // Id del tipo de IVA (ver tipos disponibles) 
                'BaseImp' 	=> 100, // Base imponible
                'Importe' 	=> 21 // Importe 
            )
        ),
        'Opcionales' 	=> array( // (Opcional) Campos auxiliares
            array(
                'Id' 		=> 17, // Codigo de tipo de opcion (ver tipos disponibles) 
                'Valor' 	=> 2 // Valor 
            )
        ),         
        'Compradores' 	=> array( // (Opcional) Detalles de los clientes del comprobante 
            array(
                'DocTipo' 		=> 80, // Tipo de documento (ver tipos disponibles) 
                'DocNro' 		=> 20111111112, // Numero de documento
                'Porcentaje' 	=> 100 // Porcentaje de titularidad del comprador
            )
        )
        */
    );
    
    try {
        $voucher = $afip->ElectronicBilling->CreateVoucher($data);    
        echo 'CAE y VTO del comprobante creado:';
        echo '<pre>';
        print_r($voucher);
        echo '</pre>';        
    } catch (\Throwable $th) {
        echo 'Error al crear comprobante: '. $th->getMessage();
    }
    echo "<hr>";              
?>