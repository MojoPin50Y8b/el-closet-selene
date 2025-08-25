<?php

namespace Database\Seeders;

use App\Models\{
    Brand,
    Category,
    Product,
    ProductImage,
    ProductVariant,
    Attribute,
    AttributeValue,
    VariantValue
};
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Requisitos
        $brand = Brand::firstOrCreate(['slug' => 'generico'], ['name' => 'Genérico']);
        $cat = Category::firstOrCreate(['slug' => Str::slug('Mujeres')], ['name' => 'Mujeres', 'is_active' => 1]);

        $attrTalla = Attribute::where('slug', 'talla')->first();
        $attrColor = Attribute::where('slug', 'color')->first();

        if (!$attrTalla || !$attrColor) {
            $this->command->warn('Atributos talla/color no existen. Corre AttributeSeeder primero.');
            return;
        }

        $tallas = AttributeValue::where('attribute_id', $attrTalla->id)->pluck('id', 'value'); // ['S'=>id,...]
        $colores = AttributeValue::where('attribute_id', $attrColor->id)
            ->get(['id', 'value', 'code'])
            ->keyBy('value'); // ['Negro'=>model,...]

        // Producto base
        $slug = 'blazer-clasico';
        $product = Product::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => 'Blazer Clásico',
                'description' => 'Blazer femenino de corte clásico ideal para oficina.',
                'brand_id' => $brand->id,
                'status' => 'published',
                'main_category_id' => $cat->id,
                'seo_title' => 'Blazer Clásico Mujer',
                'seo_description' => 'Blazer clásico para mujer con variantes de talla y color.'
            ]
        );

        // Categorías adicionales (si quieres)
        $product->categories()->syncWithoutDetaching([$cat->id]);

        // Imagen de muestra
        ProductImage::firstOrCreate(
            ['product_id' => $product->id, 'url' => '/images/dummy/blazer.jpg'],
            ['is_cover' => 1, 'sort_order' => 1]
        );

        // Variantes: combina 3 tallas x 2 colores
        $tallasUsar = collect(['S', 'M', 'L'])->filter(fn($t) => isset($tallas[$t]));
        $coloresUsar = collect(['Negro', 'Rojo'])->filter(fn($c) => isset($colores[$c]));

        foreach ($tallasUsar as $talla) {
            foreach ($coloresUsar as $colorName) {
                $color = $colores[$colorName];
                $sku = 'BLAZ-' . strtoupper($talla) . '-' . strtoupper($color['code'] ?? Str::slug($colorName, ''));
                $variant = ProductVariant::firstOrCreate(
                    ['sku' => $sku],
                    [
                        'product_id' => $product->id,
                        'price' => 999.00,
                        'sale_price' => 899.00,
                        'stock' => 10,
                    ]
                );

                // Vincula valores de atributos (talla y color)
                VariantValue::updateOrCreate(
                    ['variant_id' => $variant->id, 'attribute_id' => $attrTalla->id],
                    ['attribute_value_id' => $tallas[$talla]]
                );
                VariantValue::updateOrCreate(
                    ['variant_id' => $variant->id, 'attribute_id' => $attrColor->id],
                    ['attribute_value_id' => $color['id']]
                );
            }
        }
    }
}
