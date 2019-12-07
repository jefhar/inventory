<?php

/**
 * Copyright 2018, 2019 Jeff Harris
 * PHP Version 7.4
 */

declare(strict_types=1);

namespace App\Types\Controllers;

use App\Admin\Controllers\Controller;
use Domain\Products\Models\Type;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class TypesController extends Controller
{
    public const SHOW_NAME = 'types.show';
    public const SHOW_PATH = 'types/{type}';

    /**
     * @param Request $request
     * @param Type $type
     */
    public function show(Request $request, Type $type)
    {
        $form = [
            '[{"type":"autocomplete","label":"Autocomplete","className":"form-control","name":"autocomplete-1575674498319","values":[{"label":"Option 1","value":"option-1"},{"label":"Option 2","value":"option-2"},{"label":"Option 3","value":"option-3"}]},{"type":"button","label":"Button","subtype":"button","className":"btn-default btn","name":"button-1575674513870","style":"default"},{"type":"checkbox-group","label":"Checkbox Group","name":"checkbox-group-1575674517295","values":[{"label":"Option 1","value":"option-1","selected":true}]},{"type":"date","label":"Date Field","className":"form-control","name":"date-1575674520068"},{"type":"header","subtype":"h1","label":"Header"},{"type":"number","label":"Number","className":"form-control","name":"number-1575674525310"},{"type":"radio-group","label":"Radio Group","name":"radio-group-1575674527615","values":[{"label":"Option 1","value":"option-1"},{"label":"Option 2","value":"option-2"},{"label":"Option 3","value":"option-3"}]},{"type":"select","label":"Select","className":"form-control","name":"select-1575674530818","values":[{"label":"Option 1","value":"option-1","selected":true},{"label":"Option 2","value":"option-2"},{"label":"Option 3","value":"option-3"}]},{"type":"text","required":true,"label":"Text Field","className":"form-control","name":"text-1575674533242","subtype":"text"}]',
            '[{"type":"header","subtype":"h1","label":"Header"},{"type":"date","label":"Date Field","className":"form-control","name":"date-1575689365343"},{"type":"text","label":"Text Field","className":"form-control","name":"text-1575689367374","subtype":"text"},{"type":"checkbox-group","label":"Checkbox Group","name":"checkbox-group-1575689378743","values":[{"label":"Option 1","value":"option-1","selected":true}]}]',
            '[{"type":"radio-group","label":"Radio Group","name":"radio-group-1575689472139","values":[{"label":"Option 1","value":"option-1"},{"label":"Option 2","value":"option-2"},{"label":"Option 3","value":"option-3"}]},{"type":"select","label":"Select","className":"form-control","name":"select-1575689474390","values":[{"label":"Option 1","value":"option-1","selected":true},{"label":"Option 2","value":"option-2"},{"label":"Option 3","value":"option-3"}]},{"type":"textarea","label":"Text Area","className":"form-control","name":"textarea-1575689477555","subtype":"textarea"}]',
            '[{"type":"header","subtype":"h1","label":"Header"},{"type":"checkbox-group","label":"Checkbox Group","name":"checkbox-group-1575691722872","values":[{"label":"Option 1","value":"option-1","selected":true},{"label":"Flarp","value":"rod","selected":true},{"label":"My Label","value":"my_label","selected":true}]},{"type":"text","required":true,"label":"Text Field","className":"form-control","name":"text-1575691789364","subtype":"text"},{"type":"radio-group","label":"Radio Group","name":"radio-group-1575691849771","values":[{"label":"Option 1","value":"option-1"},{"label":"Option 2","value":"option-2"},{"label":"Option 3","value":"option-3"}]},{"type":"select","required":true,"label":"Select","className":"form-control","name":"select-1575691864839","values":[{"label":"Option 1","value":"option-1","selected":true},{"label":"Option 2","value":"option-2"},{"label":"Option 3","value":"option-3"}]}]',
        ];
        $form_object = json_decode(Arr::random($form));

        return response()->json($form_object);
    }
}
