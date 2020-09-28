<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Provider;
use DB;

class ProvidersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search_term = $request->input('q');

        if ($search_term)
        {
            $results = Provider::where('name', 'LIKE', '%'.$search_term.'%')->paginate(10);
        }
        else
        {
            $results = Provider::paginate(10);
        }

        return $results;
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
        Provider::truncate();
        $recargas = new Provider;
        $recargas->id = 2;
        $recargas->name = "Recargas";
        $recargas->save();

        $sueldos = new Provider;
        $sueldos->id = 3;
        $sueldos->name = "Sueldos";
        $sueldos->save();

        $otros = new Provider;
        $otros->id = 5;
        $otros->name = "Otros";
        $otros->save();

        $departmentsFile = fopen("departamentos.txt", "r") or die("Unable to open file!");
        if ($departmentsFile) {
            $contador=0;
            while (($line = fgets($departmentsFile)) !== false) {
                switch ($contador) {
                    case 0:
                        $contador+=1;
                        $provider= new Provider;
                        break;

                    case 1:
                        $words = explode("                              ", $line);
                        $provider->id=$words[1];
                        $contador+=1;
                        break;
                    case 2:
                        $words = explode("                          ", $line);
                        $provider->name = substr(DB::connection()->getPdo()->quote(utf8_encode($words[1])),1,-5);
                        //dd($provider);
                        $provider->save();
                        $contador=0;
                        break;
                    default:
                        break;
                }
            }
            fclose($departmentsFile);
        }
    }
}
