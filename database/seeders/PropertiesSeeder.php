<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropertiesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $typeIds = DB::table('property_types')->pluck('id', 'slug');

        $listings = [
            [
                'type' => 'apartment',
                'ref' => 'PR001',
                'address' => '1200 Brickell Bay Dr',
                'neighborhood' => 'Brickell',
                'beds' => 2, 'baths' => 2, 'sqft' => 1050,
                'price' => 3200,
                'amenities' => ['air_conditioning', 'in_unit_laundry', 'pool', 'fitness_center', 'parking'],
                'pets' => true,
                'furnished' => true,
                'featured' => true,
                'en_title' => 'Modern Brickell Apartment with Bay Views',
                'en_desc' => '<p>Spacious 2-bedroom apartment in the heart of Brickell. Floor-to-ceiling windows, in-unit washer/dryer, and resort-style pool. Walk to restaurants, shops, and Metromover.</p>',
                'image' => '/theme/img/carousel-2.jpg',
            ],
            [
                'type' => 'house',
                'ref' => 'PR002',
                'address' => '4520 SW 88th St',
                'neighborhood' => 'Kendall',
                'beds' => 4, 'baths' => 3, 'sqft' => 2400,
                'price' => 4800,
                'amenities' => ['air_conditioning', 'parking', 'yard', 'dishwasher', 'utilities_included'],
                'pets' => true,
                'furnished' => false,
                'featured' => true,
                'en_title' => 'Family Home with Private Yard — Kendall',
                'en_desc' => '<p>Beautiful single-family home with 4 bedrooms, updated kitchen, and fenced backyard. Quiet residential street, close to schools and highways.</p>',
                'image' => '/theme/img/carousel-1.jpg',
            ],
            [
                'type' => 'condo',
                'ref' => 'PR003',
                'address' => '1800 Collins Ave',
                'neighborhood' => 'Miami Beach',
                'beds' => 1, 'baths' => 1, 'sqft' => 780,
                'price' => 2750,
                'amenities' => ['air_conditioning', 'pool', 'balcony', 'elevator', 'gated_community'],
                'pets' => false,
                'furnished' => true,
                'featured' => true,
                'en_title' => 'South Beach Condo Steps from the Ocean',
                'en_desc' => '<p>Stylish 1-bedroom condo on Collins Avenue. Private balcony, building pool, and secure parking. Perfect for seasonal or long-term rental.</p>',
                'image' => '/theme/img/THOR-Vision-Vehicle-TVV-electric-rv-2.jpg',
            ],
            [
                'type' => 'townhouse',
                'ref' => 'PR004',
                'address' => '890 NW 23rd St',
                'neighborhood' => 'Wynwood',
                'beds' => 3, 'baths' => 2.5, 'sqft' => 1650,
                'price' => 3900,
                'amenities' => ['air_conditioning', 'in_unit_laundry', 'parking', 'hardwood_floors', 'balcony'],
                'pets' => true,
                'furnished' => false,
                'featured' => false,
                'en_title' => 'Wynwood Townhouse Near Art District',
                'en_desc' => '<p>Contemporary 3-bed townhouse with rooftop terrace. Minutes from Wynwood Walls, galleries, and dining.</p>',
                'image' => '/theme/img/about-img.jpg',
            ],
            [
                'type' => 'villa',
                'ref' => 'PR005',
                'address' => '10101 Sunset Dr',
                'neighborhood' => 'Coral Gables',
                'beds' => 5, 'baths' => 4, 'sqft' => 3800,
                'price' => 9500,
                'amenities' => ['air_conditioning', 'pool', 'parking', 'yard', 'gated_community', 'fitness_center'],
                'pets' => true,
                'furnished' => true,
                'featured' => true,
                'en_title' => 'Luxury Coral Gables Villa with Pool',
                'en_desc' => '<p>Executive villa with pool, summer kitchen, and 3-car garage. Ideal for corporate housing or extended stays.</p>',
                'image' => '/theme/img/carousel-2.jpg',
            ],
            [
                'type' => 'apartment',
                'ref' => 'PR006',
                'address' => '300 Biscayne Blvd Way',
                'neighborhood' => 'Downtown',
                'beds' => 1, 'baths' => 1, 'sqft' => 650,
                'price' => 2100,
                'amenities' => ['air_conditioning', 'fitness_center', 'elevator', 'pool'],
                'pets' => false,
                'furnished' => false,
                'featured' => false,
                'en_title' => 'Downtown Studio-Style Apartment',
                'en_desc' => '<p>Efficient 1-bed layout in a full-service building. Great for professionals working downtown.</p>',
                'image' => '/theme/img/carousel-1.jpg',
            ],
            [
                'type' => 'house',
                'ref' => 'PR007',
                'address' => '8317 Hudson Dr',
                'city' => 'San Diego',
                'state' => 'CA',
                'zip' => '92119',
                'neighborhood' => 'San Carlos',
                'beds' => 3,
                'baths' => 2,
                'sqft' => 1200,
                'price' => 4250,
                'amenities' => ['air_conditioning', 'parking', 'yard', 'dishwasher', 'hardwood_floors'],
                'pets' => true,
                'furnished' => false,
                'featured' => true,
                'en_title' => 'Completely Remodeled San Carlos Home — 3 Bed / 2 Bath',
                'en_desc' => '<p><strong>COMPLETELY REMODELED!</strong> Private 3-bedroom, 2-bath single-family home in the desirable San Carlos neighborhood. Modern finishes and updated systems throughout — ideal for monthly corporate housing or extended stays.</p>'
                    . '<h6 class="mt-3 mb-2">Recent Improvements Include:</h6>'
                    . '<ul class="mb-3">'
                    . '<li>New roof</li>'
                    . '<li>New kitchen with shaker cabinetry, quartz countertops, matte black hardware, and center island seating for 4–6</li>'
                    . '<li>Updated bathrooms with contemporary finishes</li>'
                    . '<li>New flooring throughout</li>'
                    . '<li>Interior and exterior paint</li>'
                    . '<li>Updated lighting and fixtures</li>'
                    . '<li>Improved landscaping and curb appeal</li>'
                    . '<li>Vaulted ceilings for a bright, open living space</li>'
                    . '<li>Cozy fireplace in the living room</li>'
                    . '<li>Spacious backyard — room for entertaining, pets, or future enhancements</li>'
                    . '</ul>'
                    . '<p class="mb-0"><strong>Property highlights:</strong> Built 1961 · 1,200 sq ft · 2-car garage · ~6,000 sq ft lot · Single story · San Diego Unified schools nearby.</p>',
                'en_highlights' => 'Remodeled 3/2 home in San Carlos. New kitchen, baths, roof, and yard. Monthly rental — schedule a tour.',
                'es_title' => 'Casa Completamente Remodelada en San Carlos — 3 Hab / 2 Baños',
                'es_desc' => '<p><strong>¡COMPLETAMENTE REMODELADA!</strong> Casa unifamiliar privada de 3 habitaciones y 2 baños en el codiciado barrio de San Carlos. Acabados modernos y sistemas actualizados — ideal para estadías corporativas o alquiler mensual extendido.</p>'
                    . '<h6 class="mt-3 mb-2">Mejoras recientes incluyen:</h6>'
                    . '<ul class="mb-3">'
                    . '<li>Techo nuevo</li>'
                    . '<li>Cocina nueva con gabinetes shaker, encimeras de cuarzo, herrajes negro mate e isla central</li>'
                    . '<li>Baños actualizados con acabados contemporáneos</li>'
                    . '<li>Pisos nuevos en toda la casa</li>'
                    . '<li>Pintura interior y exterior</li>'
                    . '<li>Iluminación y accesorios actualizados</li>'
                    . '<li>Landscaping y fachada mejorados</li>'
                    . '<li>Techos abovedados y sala con chimenea</li>'
                    . '<li>Patio amplio para entretenimiento o mascotas</li>'
                    . '</ul>'
                    . '<p class="mb-0"><strong>Datos clave:</strong> Construida 1961 · 1,200 pies² · Garaje 2 autos · Una planta.</p>',
                'es_highlights' => 'Casa remodelada 3/2 en San Carlos. Cocina, baños, techo y patio nuevos. Alquiler mensual.',
                'image' => '/theme/img/carousel-1.jpg',
            ],
            [
                'type' => 'apartment',
                'ref' => 'PR008',
                'address' => '7450 Mission Gorge Rd',
                'city' => 'San Diego',
                'state' => 'CA',
                'zip' => '92120',
                'neighborhood' => 'Allied Gardens',
                'beds' => 2,
                'baths' => 2,
                'sqft' => 1050,
                'price' => 3400,
                'amenities' => ['air_conditioning', 'in_unit_laundry', 'parking', 'balcony', 'dishwasher', 'hardwood_floors'],
                'pets' => true,
                'furnished' => true,
                'featured' => true,
                'en_title' => 'Fully Updated 2-Bed Apartment Near Mission Trails',
                'en_desc' => '<p><strong>MOVE-IN READY!</strong> Bright 2-bedroom, 2-bath apartment with a private balcony and in-unit laundry. Walk to Mission Trails, shops, and dining — perfect for professionals or small families on a monthly lease.</p>'
                    . '<h6 class="mt-3 mb-2">Apartment Features:</h6>'
                    . '<ul class="mb-3">'
                    . '<li>Open living room with vaulted ceiling and natural light</li>'
                    . '<li>Updated kitchen with quartz counters and stainless appliances</li>'
                    . '<li>Primary suite with walk-in closet and en-suite bath</li>'
                    . '<li>Second bedroom ideal for office or guest room</li>'
                    . '<li>In-unit washer and dryer</li>'
                    . '<li>Reserved parking space included</li>'
                    . '<li>Private balcony overlooking landscaped grounds</li>'
                    . '<li>Pet-friendly building (breed restrictions may apply)</li>'
                    . '</ul>'
                    . '<p class="mb-0"><strong>Lease details:</strong> 1,050 sq ft · Furnished option available · 30-day minimum · Utilities billed separately.</p>',
                'en_highlights' => 'Updated 2/2 apartment with balcony, in-unit laundry, and parking. Monthly lease near Mission Trails.',
                'es_title' => 'Apartamento 2 Hab Actualizado Cerca de Mission Trails',
                'es_desc' => '<p><strong>¡LISTO PARA MUDARSE!</strong> Apartamento luminoso de 2 habitaciones y 2 baños con balcón privado y lavandería en la unidad. Cerca de Mission Trails, tiendas y restaurantes.</p>'
                    . '<h6 class="mt-3 mb-2">Características:</h6>'
                    . '<ul class="mb-3">'
                    . '<li>Sala abierta con techo abovedado y mucha luz natural</li>'
                    . '<li>Cocina actualizada con cuarzo y electrodomésticos de acero</li>'
                    . '<li>Dormitorio principal con baño en suite</li>'
                    . '<li>Lavandería en la unidad y estacionamiento reservado</li>'
                    . '<li>Balcón privado con vistas al jardín</li>'
                    . '<li>Edificio pet-friendly</li>'
                    . '</ul>'
                    . '<p class="mb-0"><strong>Alquiler mensual</strong> · Opción amueblada · Mínimo 30 días.</p>',
                'es_highlights' => 'Apartamento 2/2 con balcón, lavandería y estacionamiento. Alquiler mensual.',
                'image' => '/theme/img/carousel-2.jpg',
            ],
        ];

        foreach ($listings as $item) {
            $slugBase = Str::slug($item['en_title'] ?? $item['ref']);

            $propertyData = [
                'property_type_id' => $typeIds[$item['type']] ?? null,
                'slug' => $slugBase,
                'address_line1' => $item['address'],
                'city' => $item['city'] ?? 'Miami',
                'state' => $item['state'] ?? 'FL',
                'zip' => $item['zip'] ?? '33130',
                'neighborhood' => $item['neighborhood'],
                'bedrooms' => $item['beds'],
                'bathrooms' => $item['baths'],
                'sqft' => $item['sqft'],
                'max_guests' => $item['beds'] * 2,
                'min_nights' => 30,
                'price_per_month' => $item['price'],
                'security_deposit' => $item['price'],
                'featured' => $item['featured'],
                'pets_allowed' => $item['pets'],
                'furnished' => $item['furnished'],
                'amenities' => json_encode($item['amenities']),
                'status' => 'available',
                'updated_at' => $now,
            ];

            $existing = DB::table('properties')->where('reference', $item['ref'])->first();

            if ($existing) {
                if (! empty($existing->slug)) {
                    unset($propertyData['slug']);
                } else {
                    $propertyData['slug'] = $this->uniquePropertySlug($slugBase, (int) $existing->id);
                }

                DB::table('properties')->where('id', $existing->id)->update($propertyData);
                $propertyId = $existing->id;
            } else {
                $propertyData['slug'] = $this->uniquePropertySlug($slugBase);
                $propertyId = DB::table('properties')->insertGetId(array_merge($propertyData, [
                    'reference' => $item['ref'],
                    'created_at' => $now,
                ]));
            }

            $enHighlights = $item['en_highlights'] ?? 'Available for monthly rental. Contact us to schedule a tour.';
            $esHighlights = $item['es_highlights'] ?? 'Disponible para alquiler mensual.';

            foreach (['en' => [
                'title' => $item['en_title'],
                'description' => $item['en_desc'],
                'highlights' => $enHighlights,
                'meta_title' => $item['en_title'] . ' | MV Miami Rental',
                'meta_description' => Str::limit(strip_tags($item['en_desc']), 320, ''),
            ], 'es' => [
                'title' => $item['es_title'] ?? $item['en_title'],
                'description' => $item['es_desc'] ?? $item['en_desc'],
                'highlights' => $esHighlights,
                'meta_title' => ($item['es_title'] ?? $item['en_title']) . ' | MV Miami Rental',
                'meta_description' => Str::limit(strip_tags($item['es_desc'] ?? $item['en_desc']), 320, ''),
            ]] as $locale => $translation) {
                DB::table('property_translations')->updateOrInsert(
                    ['property_id' => $propertyId, 'locale' => $locale],
                    $translation
                );
            }

            $hasImage = DB::table('property_images')
                ->where('property_id', $propertyId)
                ->where('is_primary', true)
                ->exists();

            if (! $hasImage) {
                DB::table('property_images')->insert([
                    'property_id' => $propertyId,
                    'path' => $item['image'],
                    'alt_text' => $item['en_title'],
                    'is_primary' => true,
                    'sort_order' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    protected function uniquePropertySlug(string $base, ?int $ignoreId = null): string
    {
        $slug = $base ?: 'property';
        $candidate = $slug;
        $i = 1;

        while ($this->propertySlugExists($candidate, $ignoreId)) {
            $candidate = $slug.'-'.$i;
            $i++;
        }

        return $candidate;
    }

    protected function propertySlugExists(string $slug, ?int $ignoreId = null): bool
    {
        $query = DB::table('properties')->where('slug', $slug);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }
}
