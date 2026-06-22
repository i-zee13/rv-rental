<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BlogPostsSeeder extends Seeder
{
    public function run(): void
    {
        $authorId = DB::table('users')->value('id');

        $posts = [
            [
                'slug' => 'best-miami-neighborhoods-monthly-rentals',
                'image' => '/theme/img/carousel-2.jpg',
                'title_en' => 'Best Miami Neighborhoods for Monthly Rentals',
                'excerpt_en' => 'From Brickell high-rises to Coral Gables villas — where to stay for 30+ nights.',
                'content_en' => '<p>Miami offers diverse neighborhoods for extended stays. Brickell suits professionals who want walkable dining and bay views. Miami Beach works for seasonal renters near the ocean. Coral Gables and Kendall appeal to families needing space and schools nearby.</p><p>Compare commute times, parking, and amenities before you book — our team can help match you with the right area.</p>',
                'title_es' => 'Mejores barrios de Miami para alquileres mensuales',
                'excerpt_es' => 'Desde Brickell hasta Coral Gables — dónde quedarse 30+ noches.',
                'content_es' => '<p>Miami ofrece barrios diversos para estadías prolongadas. Brickell es ideal para profesionales; Miami Beach para quienes buscan el océano; Coral Gables y Kendall para familias.</p><p>Compare tiempos de viaje, estacionamiento y amenidades antes de reservar.</p>',
            ],
            [
                'slug' => 'rv-camping-everglades-guide',
                'image' => '/theme/img/THOR-Vision-Vehicle-TVV-electric-rv-2.jpg',
                'title_en' => 'RV Camping Near the Everglades: A Quick Guide',
                'excerpt_en' => 'Plan your RV adventure from Miami to the Glades with permits, routes, and gear tips.',
                'content_en' => '<p>Renting an RV from Miami puts the Everglades within easy reach. Book campgrounds early in peak season, carry plenty of water, and check vehicle height limits on park roads.</p><p>We offer RV delivery and orientation so your first night on the road is stress-free.</p>',
                'title_es' => 'Camping en RV cerca de los Everglades',
                'excerpt_es' => 'Planifique su aventura en RV desde Miami con rutas y consejos prácticos.',
                'content_es' => '<p>Alquilar un RV desde Miami acerca los Everglades. Reserve campamentos con anticipación y revise límites de altura en las carreteras del parque.</p>',
            ],
            [
                'slug' => 'mia-airport-car-rental-pickup-tips',
                'image' => '/theme/img/carousel-1.jpg',
                'title_en' => 'MIA Airport Pickup: 5 Tips for a Smooth Start',
                'excerpt_en' => 'Skip the stress — documents, timing, and curbside pickup explained.',
                'content_en' => '<p>Have your license and confirmation ready before you land. Allow extra time during holidays. Message us on WhatsApp when you land for curbside coordination.</p><p>International travelers should confirm IDP requirements before pickup.</p>',
                'title_es' => 'Recogida en MIA: 5 consejos para empezar bien',
                'excerpt_es' => 'Documentos, horarios y recogida en acera — sin estrés.',
                'content_es' => '<p>Tenga licencia y confirmación listas al aterrizar. Reserve tiempo extra en temporada alta. Escríbanos por WhatsApp al llegar.</p>',
            ],
            [
                'slug' => 'luxury-car-rental-miami-what-to-know',
                'image' => '/theme/img/car-2.png',
                'title_en' => 'Luxury Car Rental in Miami: What to Know',
                'excerpt_en' => 'Deposits, insurance add-ons, and age requirements for premium vehicles.',
                'content_en' => '<p>Luxury and exotic rentals may require a higher security deposit and minimum driver age. Optional coverage protects against minor damage and roadside issues.</p><p>Browse our fleet online or ask for a custom quote for events and photo shoots.</p>',
                'title_es' => 'Alquiler de autos de lujo en Miami',
                'excerpt_es' => 'Depósitos, seguros opcionales y requisitos de edad para vehículos premium.',
                'content_es' => '<p>Los alquileres de lujo pueden requerir mayor depósito y edad mínima. La cobertura opcional protege ante daños menores.</p>',
            ],
            [
                'slug' => 'pet-friendly-rentals-miami',
                'image' => '/theme/img/about-img.jpg',
                'title_en' => 'Pet-Friendly Rentals: Cars and Homes in Miami',
                'excerpt_en' => 'Traveling with pets? Here is how to filter listings and avoid surprise fees.',
                'content_en' => '<p>Many of our homes and some vehicles welcome pets — look for the pet-friendly badge on listings. Cleaning fees may apply. Keep carriers and vaccination records handy for condos with strict rules.</p>',
                'title_es' => 'Alquileres pet-friendly en Miami',
                'excerpt_es' => 'Viaje con mascotas — cómo filtrar anuncios y evitar cargos sorpresa.',
                'content_es' => '<p>Muchas propiedades y algunos vehículos aceptan mascotas. Revise el badge pet-friendly y tenga registros de vacunación a mano.</p>',
            ],
            [
                'slug' => 'corporate-housing-miami-monthly',
                'image' => '/theme/img/carousel-2.jpg',
                'title_en' => 'Corporate Housing in Miami for Monthly Stays',
                'excerpt_en' => 'Furnished apartments and invoicing options for relocating teams.',
                'content_en' => '<p>Companies relocating staff to Miami often need 30–90 day furnished units with Wi‑Fi and parking included. We coordinate tours, lease paperwork, and recurring billing for HR teams.</p><p>Contact us for a portfolio of Brickell, Downtown, and Kendall options.</p>',
                'title_es' => 'Vivienda corporativa en Miami',
                'excerpt_es' => 'Apartamentos amueblados y facturación para equipos en traslado.',
                'content_es' => '<p>Las empresas que trasladan personal suelen necesitar unidades amuebladas de 30–90 días. Coordinamos visitas, contratos y facturación recurrente.</p>',
            ],
            [
                'slug' => 'stripe-online-payment-rental-booking',
                'image' => '/theme/img/features-img.png',
                'title_en' => 'Pay Online Securely with Stripe at Checkout',
                'excerpt_en' => 'How our secure card payment works — and when pay-at-pickup still applies.',
                'content_en' => '<p>At checkout you can pay securely by card via Stripe or choose pay-at-pickup where available. Online payment confirms your booking instantly and sends email confirmation.</p><p>Your card is processed on Stripe\'s encrypted checkout — we never store full card numbers.</p>',
                'title_es' => 'Pague en línea con Stripe al reservar',
                'excerpt_es' => 'Cómo funciona el pago seguro — y cuándo pagar al recoger.',
                'content_es' => '<p>En el checkout puede pagar con tarjeta vía Stripe o elegir pago al recoger. El pago en línea confirma su reserva al instante.</p>',
            ],
            [
                'slug' => 'weekend-keys-to-key-west-rv',
                'image' => '/theme/img/carousel-1.jpg',
                'title_en' => 'Weekend Road Trip: Miami to Key West by RV',
                'excerpt_en' => 'Overseas Highway highlights, overnight stops, and RV size tips.',
                'content_en' => '<p>The drive to Key West is one of Florida\'s best RV routes. Plan two days if you want to stop in Islamorada and Marathon. Check bridge height clearances and reserve RV spots early on weekends.</p>',
                'title_es' => 'Fin de semana en RV: Miami a Key West',
                'excerpt_es' => 'Highlights de la Overseas Highway y consejos para RV.',
                'content_es' => '<p>El viaje a Key West es una de las mejores rutas en RV. Planifique dos días con paradas en Islamorada y Marathon.</p>',
            ],
        ];

        foreach ($posts as $item) {
            $existing = BlogPost::where('slug', $item['slug'])->first();

            if ($existing) {
                $post = $existing;
                $post->update([
                    'status' => 'published',
                    'featured_image' => $item['image'],
                    'author_id' => $authorId,
                ]);
            } else {
                $post = BlogPost::create([
                    'slug' => $item['slug'],
                    'status' => 'published',
                    'featured_image' => $item['image'],
                    'author_id' => $authorId,
                ]);
            }

            foreach (['en', 'es'] as $locale) {
                $post->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'title' => $item['title_'.$locale],
                        'excerpt' => $item['excerpt_'.$locale],
                        'content' => $item['content_'.$locale],
                        'meta_title' => $item['title_'.$locale].' | MV Miami Rental',
                        'meta_description' => $item['excerpt_'.$locale],
                    ]
                );
            }
        }
    }
}
