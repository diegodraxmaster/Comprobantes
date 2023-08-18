<?php

namespace App\Http\Controllers;

use App\Models\Comprobante;
use App\Models\Customer;
use App\Models\DetalleComprobante;
use App\Models\Producto;
use App\Models\Supplier;
use DOMDocument;
use DOMXPath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleXMLElement;
use XMLReader;

class ComprobanteController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); // Tamaño de página, predeterminado a 10
        $page = $request->input('page', 1); // Número de página, predeterminado a 1

        $fields = ['id', 'tipo_comprobante', 'numero_comprobante', 'fecha_emision', 'monto_total'];

        $comprobantes = Comprobante::select($fields)
            ->with([
                'detallesComprobante' => function ($query) {
                    $query->select('id', 'id_comprobante', 'id_producto', 'cantidad', 'precio_unitario', 'subtotal')
                        ->with(['producto' => function ($query) {
                            $query->select('id', 'nombre', 'descripcion');
                        }])
                        ->with(['comprobante' => function ($query) {
                            $query->select('id', 'supplier_id', 'customer_id');
                        }])
                        ->with(['comprobante.supplier' => function ($query) {
                            $query->select('id', 'name', 'ruc');
                        }])
                        ->with(['comprobante.customer' => function ($query) {
                            $query->select('id', 'name', 'ruc');
                        }]);
                }
            ])
            ->orderBy('fecha_emision', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'status' => true,
            'data' => $comprobantes,
        ], 200);
    }
    public function getDetalles($id)
    {
        try {
            $Comprobante = Comprobante::find($id);
            $detalleComprobantes = DetalleComprobante::where('id_comprobante', $Comprobante->id)->get();
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Comprobante no encontrado'], 500);
        }

        $supplier_id = Supplier::find($Comprobante->id);
        $customer_id = Customer::find($Comprobante->id);
        foreach ($detalleComprobantes as $detalleComprobante) {

            $producto = Producto::find($detalleComprobante->id_producto);
            $data[] = [
                'detalleComprobante_id' => $detalleComprobante->id,
                'comprobante_id' => $detalleComprobante->id_comprobante,
                'tipo_comprobante' => $Comprobante->tipo_comprobante,
                'numero_comprobante' => $Comprobante->numero_comprobante,
                'fecha_emision' => $Comprobante->fecha_emision,
                'supplier_name' => $supplier_id->name,
                'supplier_ruc' => $supplier_id->ruc,
                'customer_name' => $customer_id->name,
                'customer_ruc' => $customer_id->ruc,
                'id_producto' => $detalleComprobante->id_producto,
                'nombre_producto' => $producto->nombre,
                'descripcion' => $detalleComprobante->descripcion,
                'cantidad' => $detalleComprobante->cantidad,
                'precio_unitario' => $detalleComprobante->precio_unitario,
                'subtotal' => $detalleComprobante->subtotal,
            ];
        }
        //return response()->json($data, 200, [], JSON_PRETTY_PRINT);
        return response()->json([
            'status' => true,
            'message' => 'detalles de un comprobante registrado',
            'data' => $data,
        ], 201);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function registrar(Request $request)
    {
        $xmlContent = file_get_contents($request->xml->path());
        //dd($xmlContent);
        $dom = new DOMDocument();
        $dom->loadXML($xmlContent);
        $invoice = $dom->getElementsByTagName('Invoice')->item(0);
        $InvoiceTypeCode = $invoice->getElementsByTagName('InvoiceTypeCode')->item(0)->nodeValue;
        $invoiceID = $invoice->getElementsByTagName('ID')->item(0)->nodeValue;
        $issueDate = $invoice->getElementsByTagName('IssueDate')->item(0)->nodeValue;
        $invoiceLines = $invoice->getElementsByTagName('InvoiceLine');
        $tipo_documento = "boleta";
        /* el tipo_documento "01" es ruc segun la documentacion */
        if ($InvoiceTypeCode == "01") {
            $tipo_documento = "ruc";
        }
        //dd($tipo_documento);
        /* suplier nodes*/
        $AccountingSupplierPartys = $invoice->getElementsByTagName('AccountingSupplierParty');
        $PartyNames = $invoice->getElementsByTagName('PartyName');
        foreach ($PartyNames as $PartyName) {
            $Name_supplier = $PartyName->getElementsByTagName('Name')->item(0)->nodeValue;
        }
        foreach ($AccountingSupplierPartys as $AccountingSupplierParty) {
            $AccountingSupplierParty_ruc = $AccountingSupplierParty->getElementsByTagName('PartyIdentification')->item(0);
        }
        $ruc_supplier = $AccountingSupplierParty_ruc->getElementsByTagName('ID')->item(0)->nodeValue;
        dump($Name_supplier);
        dump($ruc_supplier);
        /* customer nodes*/
        $AccountingCustomerPartys = $invoice->getElementsByTagName('AccountingCustomerParty');
        $PartyLegalEntitys = $invoice->getElementsByTagName('PartyLegalEntity');
        foreach ($PartyLegalEntitys as $PartyLegalEntity) {
            $Name_customer = $PartyLegalEntity->getElementsByTagName('RegistrationName')->item(0)->nodeValue;
            $direccion_customer = $PartyLegalEntity->getElementsByTagName('Line')->item(0)->nodeValue;
        }
        foreach ($AccountingCustomerPartys as $AccountingCustomerParty) {
            $AccountingCustomerParty = $AccountingCustomerParty->getElementsByTagName('PartyIdentification')->item(0);
        }
        $ruc_customer = $AccountingCustomerParty->getElementsByTagName('ID')->item(0)->nodeValue;

        dump($Name_customer);
        dump($direccion_customer);
        dump($ruc_customer);
        try {
            $sup=Supplier::where('ruc', $ruc_supplier)->get();
        } catch (\Throwable $th) {
            //throw $th;
        }
        dd($sup->id);
        $supplier = Supplier::create([
            'name' => $Name_supplier,
            'ruc' => $ruc_supplier,
            'address' => "No se sabe",
        ]);
        dump($supplier);
        dump($invoiceID);
        dump($issueDate);
        dump($invoiceLines);
        foreach ($invoiceLines as $invoiceLine) {
            $lineID = $invoiceLine->getElementsByTagName('ID')->item(0)->nodeValue;
            $quantity = $invoiceLine->getElementsByTagName('InvoicedQuantity')->item(0)->nodeValue;
            $amount = $invoiceLine->getElementsByTagName('LineExtensionAmount')->item(0)->nodeValue;

            $item = $invoiceLine->getElementsByTagName('Item')->item(0);
            $description = $item->getElementsByTagName('Description')->item(0)->nodeValue;
            $itemID = $item->getElementsByTagName('ID')->item(0)->nodeValue;

            echo "Detalles de la línea de factura:\n";
            dump("ID de línea: $lineID\n");
            echo "Cantidad: $quantity\n";
            echo "Monto: $amount\n";
            echo "Descripción del producto: $description\n";
            echo "ID del producto: $itemID\n";
            echo "-----------------------------\n";
        }
        /*   $comprobante = Comprobante::create([
            'tipo_comprobante' => 'required|string|max:10',
            'numero_comprobante' => 'required|string|max:15',
            'fecha_emision' => 'required|date',
            'monto_total' => 'required|numeric',
            'supplier_id' => 'required|exists:suppliers,id',
            'customer_id' => 'required|exists:customers,id',
            'detalles' => 'required|array|min:1',
        ]); */















        // Valida que se haya enviado un archivo y que sea válido
        $request->validate([
            'xml' => 'required|mimes:xml'
        ]);

        // Obtén el archivo XML de la solicitud
        $archivoXml = $request->file('xml');

        // Genera un nombre único para el archivo
        $nombreArchivo = 'archivo.' . $archivoXml->getClientOriginalExtension();

        // Define la ruta dentro del disco para guardar el archivo
        $rutaArchivo = 'xml_files/' . $nombreArchivo;

        // Guarda el archivo XML en el sistema de almacenamiento (disco local)
        Storage::putFileAs('xml_files', $archivoXml, $nombreArchivo);

        // Ruta del archivo XML que deseas leer
        $rutaArchivo = 'xml_files/archivo_1692313480.xml';

        // Verifica si el archivo existe
        if (Storage::exists('xml_files/ejemplo.xml')) {
            // Lee el contenido del archivo
            //dd($rutaArchivo);
            $contenidoXml = Storage::get('xml_files/ejemplo.xml');
            dd($contenidoXml);
            $peliculas = new SimpleXMLElement($contenidoXml);
            dd($peliculas);
            echo $peliculas->pelicula[0]->argumento;

            return "Archivo XML leído y procesado correctamente.";
        } else {
            return "El archivo XML no existe.";
        }


        try {
            if ($request->hasFile('xml') && $request->file('xml')->isValid()) {
                $xmlContent = file_get_contents($request->xml->path());
                //$xmlContent = str_replace("\n", "", $xmlContent);

                // Carga el XML desde la cadena
                $xmlObj = simplexml_load_string($xmlContent);

                // Convierte a JSON y luego decodifica como un array
                $json = json_encode($xmlObj);
                $array = json_decode($json, true);

                // Ahora puedes trabajar con el contenido del XML en forma de array
                dd($xmlContent);
                //$xml = new SimpleXMLElement($xmlContent, LIBXML_PARSEHUGE);

                // Ahora puedes acceder a los elementos y atributos del XML
                // Realiza las operaciones necesarias con los datos del XML

                return "XML procesado correctamente.";
            } else {
                return "No se proporcionó un archivo XML válido.";
            }
        } catch (\Exception $e) {
            Log::error("Error al procesar XML: " . $e->getMessage());
            return "Error al procesar el XML.";
        }
        foreach ($xml->path('//cac:InvoiceLine') as $invoiceLine) {
            $line_id = (string) $invoiceLine->cbc->ID;
            dd($line_id);
            $description = (string) $invoiceLine->cbc->Description;
            $quantity = (float) $invoiceLine->cbc->InvoicedQuantity;
            $unit_price = (float) $invoiceLine->cac->Price->cbc->PriceAmount;
            $line_extension_amount = (float) $invoiceLine->cac->LineExtensionAmount;
        }
        return response()->json([
            'status' => true,
            'message' => 'Cliente registrado exitosamente',
            'cliente' => $line_id,
        ], 201);
    }

    public function destroy(Comprobante $comprobante)
    {

        try {
            // Eliminar los detalles de comprobante asociados
            $comprobante->detallesComprobante()->delete();
            // Eliminar el comprobante
            $comprobante->delete();
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Error al eliminar comprobante'], 500);
        }
        return response()->json([
            'status' => true,
            'message' => 'Comprobante y sus detalles eliminados correctamente.',
        ], 200);
    }
    public function getMontoTotalArticulos()
    {
        $montoTotalPorArticulo = DetalleComprobante::select('id_producto')
            ->selectRaw('SUM(subtotal) as monto_total')
            ->groupBy('id_producto')
            ->with('producto')
            ->get();
        //dd($montoTotalPorArticulo);
        return response()->json([
            'status' => true,
            'data' => $montoTotalPorArticulo,
        ], 200);
    }
    public function getMontoTotalComprobantes()
    {
        $montoTotal = Comprobante::sum('monto_total'); // Ajusta el campo según tu modelo Comprobante

        return response()->json([
            'status' => true,
            'monto_total' => $montoTotal,
        ], 200);
    }
}
