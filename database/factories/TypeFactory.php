<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Domain\Products\Models\Type;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(
    Type::class,
    function (Faker $faker) {
        $forms = [
            '[{"type":"text","label":"Serial Number","className":"form-control","name":"serial","subtype":"text"},{"type":"checkbox-group","label":"Checkbox Group","name":"checkbox-group-1578085609729","values":[{"label":"Option 1","value":"option-1","selected":true},{"label":"Option 2","value":"option-2"}]}]',
            '[{"type":"text","label":"Serial Number","className":"form-control","name":"serial","subtype":"text"},{"type":"text","label":"Something","className":"form-control","name":"something","subtype":"text"},{"type":"radio-group","label":"Radio Group","name":"radio-group-1578085735545","values":[{"label":"Option 1","value":"option-1"},{"label":"Option 2","value":"option-2"},{"label":"Option 3","value":"option-3"}]}]',
            '[{"type":"text","label":"Serial Number","className":"form-control","name":"serial","subtype":"text"},{"type":"text","label":"Something","className":"form-control","name":"something","subtype":"text"},{"type":"radio-group","label":"Yes Or No","inline":true,"name":"radio-group-1578085735545","values":[{"label":"Yes","value":"yes"},{"label":"No","value":"no"}]}]',
            '[{"type":"text","label":"Serial Number","placeholder":"Enter a Serial Number","className":"form-control","name":"serial","subtype":"text"},{"type":"select","label":"Select","className":"form-control","name":"select-1578085955357","values":[{"label":"Option 1","value":"option-1","selected":true},{"label":"Option 2","value":"option-2"}]}]',
            '[{"type":"text","label":"Serial Number","placeholder":"Enter a Serial Number","className":"form-control","name":"serial","subtype":"text"},{"type":"select","label":"Size","className":"form-control","name":"select-1578085955357","values":[{"label":"Big","value":"big","selected":true},{"label":"Tiny","value":"tiny"}]}]',
        ];

        return [
            Type::NAME => Str::lower($faker->catchPhrase),
            Type::FORM => Arr::random($forms),
        ];
    }
);
