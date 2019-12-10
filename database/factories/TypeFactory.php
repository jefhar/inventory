<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Domain\Products\Models\Type;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(
    Type::class,
    function (Faker $faker) {
        $name = $faker->catchPhrase;
        $form = '[{"type":"autocomplete","label":"Autocomplete","className":"form-control","name":"autocomplete-1575674498319","values":[{"label":"Option 1","value":"option-1"},{"label":"Option 2","value":"option-2"},{"label":"Option 3","value":"option-3"}]},{"type":"button","label":"Button","subtype":"button","className":"btn-default btn","name":"button-1575674513870","style":"default"},{"type":"checkbox-group","label":"Checkbox Group","name":"checkbox-group-1575674517295","values":[{"label":"Option 1","value":"option-1","selected":true}]},{"type":"date","label":"Date Field","className":"form-control","name":"date-1575674520068"},{"type":"number","label":"Number","className":"form-control","name":"number-1575674525310"},{"type":"radio-group","label":"Radio Group","name":"radio-group-1575674527615","values":[{"label":"Option 1","value":"option-1"},{"label":"Option 2","value":"option-2"},{"label":"Option 3","value":"option-3"}]},{"type":"select","label":"Select","className":"form-control","name":"select-1575674530818","values":[{"label":"Option 1","value":"option-1","selected":true},{"label":"Option 2","value":"option-2"},{"label":"Option 3","value":"option-3"}]},{"type":"text","required":true,"label":"Text Field","className":"form-control","name":"text-1575674533242","subtype":"text"}]';

        return [
            Type::NAME => $name,
            Type::SLUG => Str::slug($name),
            Type::FORM => $form,
        ];
    }
);
