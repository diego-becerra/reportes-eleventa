<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PurchaseRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;
use App\Provider;

/**
 * Class PurchaseCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PurchaseCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     * 
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Purchase::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/purchase');
        CRUD::setEntityNameStrings('Compra', 'Compras');

        $this->crud->addField(
            [  // Select
               'label'     => "Proveedor",
               'type'      => 'select2_from_ajax',
               'name'      => 'provider_id', // the db column for the foreign key
               'entity'    => 'provider', // the method that defines the relationship in your Model
               'attribute' => 'name', // foreign key attribute that is shown to user
               'data_source'=> url("api/provider"),
               'placeholder'=> "Selecciona un proveedor",
               'minimum_input_length' => 1,
               'model' => "App\Models\Provider",
               'dependencies' => ['provider'],
               'method' => 'GET',
               'include_all_form_fields' => false,
            ],  
        );

        $this->crud->addField(
            [   // Number
                'name' => 'total',
                'label' => 'Total',
                'type' => 'number',

                // optionals
                'attributes' => ["step" => "any"], // allow decimals
                'prefix'     => "$",
                // 'suffix'     => ".00",
            ],
        );

        //$this->crud->removeColumn('provider_id');

        //$this->crud->setRequiredFields(StoreRequest::class);

        $this->crud->setValidation(PurchaseRequest::class);

        $this->crud->setListView('list_purchases');

        $this->crud->allowAccess(['list','create', 'delete']);
        $this->crud->denyAccess('show');
    }

    /**
     * Define what happens when the List operation is loaded.
     * 
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        //CRUD::setFromDb(); // columns
        /*$this->crud->addField(
            [  // Select2
               'label'     => "Proveedor",
               'type'      => 'select2',
               'name'      => 'provider_id', // the db column for the foreign key
               'entity'    => 'provider', // the method that defines the relationship in your Model
               'attribute' => 'name', // foreign key attribute that is shown to user

               // optional
               'model'     => "App\Models\Provider", // foreign key model
               'default'   => 2, // set the default value of the select2
               'options'   => (function ($query) {
                    return $query->orderBy('name', 'ASC')->get();
                }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
            ],  
        );*/


         $this->crud->addColumn(
            [
                'name'  => 'provider', // name of relationship method in the model
                'type'  => 'relationship',
                'label' => 'Proveedor', // Table column heading
                'entity'    => 'provider', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model'     => App\Models\Provider::class, // foreign key model
            ]
        );

        $this->crud->addColumn(
            [
               'name'  => 'total', // The db column name
               'label' => 'Total', // Table column heading
               'type'  => 'number',
               'prefix'        => '$',
               'decimals'      => 2,
               'dec_point'     => '.',
               'thousands_sep' => ',',
            ]
        );

        $this->crud->addColumn(
            [
                'name'  => 'created_at', // The db column name
                'label' => 'Fecha Y Hora', // Table column heading
                'type'  => 'datetime',
                'format' => 'dddd D MMMM YYYY, h:mm A', // use something else than the base.default_datetime_format config value
            ]
        );


        /*
        $this->crud->addFilter([
        'type'  => 'date_range',
        'name'  => 'from_to',
        'label' => 'Date range'
        ],
        false,
        function ($value) { // if the filter is active, apply these constraints
        // $dates = json_decode($value);
        // $this->crud->addClause('where', 'date', '>=', $dates->from);
        // $this->crud->addClause('where', 'date', '<=', $dates->to . ' 23:59:59');
        });*/

        $this->crud->addFilter([
          'type'  => 'date_range',
          'name'  => 'from_to',
          'label' => 'Rango de Fechas',
          'startDate' => date('Y-m-d'),
          'endDate' => date('Y-m-d'),
          'currentValue' => [
              'from' => date('Y-m-d'),
              'to' => date('Y-m-d')
          ]
        ],
        true,
        function ($value) { // if the filter is active, apply these constraints
          $dates = json_decode($value);

          $this->crud->addClause('where', 'date', '>=', $dates->from);
          $this->crud->addClause('where', 'date', '<=', $dates->to);
        });


        $this->crud->addFilter([
          'name'  => 'provider_id',
          'type'  => 'select2',
          'label' => 'Proveedor'
        ], function () {
            return Provider::orderBy('name','ASC')->get()->keyBy('id')->pluck('name', 'id')->toArray();
        }, function ($value) { // if the filter is active
          $this->crud->addClause('where', 'provider_id', $value);
        });



        /**
         * Columns can be defined using the fluent syntax or array syntax:
         * - CRUD::column('price')->type('number');
         * - CRUD::addColumn(['name' => 'price', 'type' => 'number']); 
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation(PurchaseRequest::class);

        $this->crud->removeSaveAction('save_and_back');

        $this->crud->removeSaveAction('save_and_edit');


        //CRUD::setFromDb(); // fields

        $this->crud->removeField('date');

        $this->crud->removeField('provider_id');

        $this->crud->removeField('total');

        $this->crud->addField(
            [   // Hidden
                'name'  => 'date',
                'type'  => 'hidden',
                'value' => date('Y-m-d'),
            ],
        );

        $this->crud->addField(
            [  // Select2
               'label'     => "Proveedor",
               'type'      => 'select2',
               'name'      => 'provider_id', // the db column for the foreign key
               'entity'    => 'provider', // the method that defines the relationship in your Model
               'attribute' => 'name', // foreign key attribute that is shown to user

               // optional
               'model'     => "App\Models\Provider", // foreign key model
               'default'   => 1, // set the default value of the select2
               'options'   => (function ($query) {
                    return $query->orderBy('name', 'ASC')->get();
                }), // force the related options to be a custom query, instead of all(); you can use this to filter the results show in the select
            ],  
        );

        $this->crud->addField(
            [   // Number
                'name' => 'total',
                'label' => 'Total',
                'type' => 'number',

                // optionals
                'attributes' => ["step" => "any"], // allow decimals
                'prefix'     => "$",
                // 'suffix'     => ".00",
            ],
        );

        /**
         * Fields can be defined using the fluent syntax or array syntax:
         * - CRUD::field('price')->type('number');
         * - CRUD::addField(['name' => 'price', 'type' => 'number'])); 
         */
    }

    /**
     * Define what happens when the Update operation is loaded.
     * 
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function providerOptions(Request $request) {
      $term = $request->input('term');
      $options = Provider::where('name', 'like', '%'.$term.'%')->get()->pluck('name', 'id');
      return $options;
    }
}
