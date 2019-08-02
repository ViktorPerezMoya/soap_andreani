<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Andreani\Andreani;
use Andreani\Requests\CotizarEnvio;
use NuSoapClient;

class SoapTestController extends Controller
{
    function index(){
        echo '<h1>Soap con andreani </h1>';
    }
    function andreaniTest(){
        $request = new CotizarEnvio();
        $request->setCodigoDeCliente('CL0003750');
        $request->setNumeroDeContrato('400006709');
        $request->setCodigoPostal('1014');
        $request->setPeso(500);
        $request->setVolumen(100);
        $request->setValorDeclarado(100);

        $andreani = new Andreani('eCommerce_Integra','passw0rd','test');
        $response = $andreani->call($request);
        if($response->isValid()){
            $tarifa = $response->getMessage()->CotizarEnvioResult->Tarifa;
            echo "La cotización funcionó bien y la tarifa es $tarifa";
        } else {
            echo "La cotización falló, el mensaje de error es el siguiente";
            var_dump($response->getMessage());
        }
    }

    function nusoapTest(){
        $client = new \NuSoapClient('https://cotizadorpreprod.andreani.com/ws?wsdl', 'wsdl');
        $client->soap_defencoding = 'UTF-8';
        $client->decode_utf8 = FALSE;
        $error  = $client->getError();

        $data = [
            'CPDestino' => "1014",
            'Cliente'         => 'CL0003750',
            'Contrato'         => '400006709',
            'Peso'          => '500',
            'SucursalRetiro'         => '',
            'Volumen'         => '100',
            'ValorDeclarado'       => '100'
        ];

        // Calls
        $result = $client->call("CotizarEnvio", $data);

        if ($client->fault) {
            echo "<h2>Fault</h2><pre>";
            print_r($result);
            echo "</pre>";
        } else {
            $error = $client->getError();
            if ($error) {
                echo "<h2>Error</h2><pre>" . $error . "</pre>";
            } else {
                echo "<h2>Main</h2>";
                echo $result;
            }
        }
    }
}
