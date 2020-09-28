<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sale;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function actualizar(){
        $salesFile = fopen("ventas.txt", "r") or die("Unable to open file!");
        if ($salesFile) {
            //Sale::truncate();
            $contador=0;
            while (($line = fgets($salesFile)) !== false) {
                switch ($contador) {
                    case 0:
                        $contador+=1;
                        $sale = new Sale;
                        break;

                    case 1:
                        $words = array_pad(explode("                 ",$line), 2, null) ;
                        $sale->provider_id = $words[1];
                        $contador+=1;
                        break;
                    case 2:
                        $words = explode("                        ", $line);
                        $quantity = $words[1];
                        $contador+=1;
                        break;
                    case 3:
                        $words = explode("                    ", $line);
                        $price = $words[1];
                        $sale->total=intval($quantity)*intval($price);
                        $contador+=1;
                        break;
                    case 4:
                        $words = explode("                     ", $line);
                        $sale->datetime = $words[1];
                        $contador+=1;
                        break;
                    case 5:
                        $words = explode("                 ", $line);
                        $sale->barcode = $words[1];
                        $contador = 0;
                        $sale->save();
                    default:
                        break;
                }
            }
            fclose($salesFile);
        }
    }

    public function kanulki(){
        $folios = fopen("folios.csv", "r") or die("Unable to open file!");
        if ($folios) {
            $contador=0;
            while (($line = fgets($folios)) !== false) {
                $words = explode(",", $line);
                $folio="A-".$words[0];
                $id=substr($words[2], 0, -1);
                $old_pdf="FolioFiscal_".$id.".pdf";
                $new_pdf="/".$folio.".pdf";
                $old_xml="FolioFiscal_".$id.".xml";
                $new_xml=$folio.".xml";
                copy($old_xml , "/PDF2".$new_xml);
                //rename($old_pdf , $new_pdf);
                //rename($old_xml , $new_xml);
            }
            fclose($folios);
        }
    }
}
