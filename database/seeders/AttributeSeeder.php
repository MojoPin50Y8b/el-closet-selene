<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Attribute;
use App\Models\AttributeValue;
// use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        // Talla
        $talla = Attribute::firstOrCreate(
            ['slug' => 'talla'],
            ['name' => 'Talla', 'input_type' => 'select', 'is_variant' => 1]
        );

        foreach (['XS', 'S', 'M', 'L', 'XL'] as $i => $v) {
            AttributeValue::firstOrCreate(
                ['attribute_id' => $talla->id, 'value' => $v],
                ['code' => $v, 'sort_order' => $i]
            );
        }

        // Color
        $color = Attribute::firstOrCreate(
            ['slug' => 'color'],
            ['name' => 'Color', 'input_type' => 'color', 'is_variant' => 1]
        );

        $colors = [
            ['Negro', 'BLACK', '#000000'],
            ['Blanco', 'WHITE', '#FFFFFF'],
            ['Rojo', 'RED', '#FF0000'],
            ['Azul', 'BLUE', '#0066FF'],
        ];

        foreach ($colors as $i => [$name, $code, $hex]) {
            AttributeValue::firstOrCreate(
                ['attribute_id' => $color->id, 'value' => $name],
                ['code' => $code, 'hex' => $hex, 'sort_order' => $i]
            );
        }
    }
}
