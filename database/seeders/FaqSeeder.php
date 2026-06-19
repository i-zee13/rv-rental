<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'scope' => Faq::SCOPE_GENERAL,
                'page_keys' => ['home', 'contact'],
                'sort_order' => 10,
                'question_en' => 'What documents do I need to rent a car in Miami?',
                'answer_en' => 'You need a valid driver\'s license, a major credit card in the renter\'s name, and proof of insurance if required. International visitors may need a passport and International Driving Permit depending on their country.',
                'question_es' => '¿Qué documentos necesito para alquilar un auto en Miami?',
                'answer_es' => 'Necesita una licencia de conducir válida, una tarjeta de crédito principal a nombre del arrendatario y comprobante de seguro si es requerido. Los visitantes internacionales pueden necesitar pasaporte y Permiso Internacional de Conducir.',
            ],
            [
                'scope' => Faq::SCOPE_GENERAL,
                'page_keys' => ['home', 'contact'],
                'sort_order' => 20,
                'question_en' => 'Do you offer airport pickup or delivery?',
                'answer_en' => 'Yes. We offer flexible pickup and delivery options in the Miami area including MIA airport. Contact us or mention it in your booking request for a custom quote.',
                'question_es' => '¿Ofrecen recogida en el aeropuerto o entrega?',
                'answer_es' => 'Sí. Ofrecemos opciones flexibles de recogida y entrega en el área de Miami, incluido el aeropuerto MIA. Contáctenos o menciónelo en su solicitud de reserva.',
            ],
            [
                'scope' => Faq::SCOPE_VEHICLE,
                'page_keys' => null,
                'sort_order' => 10,
                'question_en' => 'Is insurance included with the vehicle rental?',
                'answer_en' => 'Our vehicles are fully insured for basic coverage. Optional supplemental protection and add-ons are available during checkout. Ask us if you need help choosing the right coverage.',
                'question_es' => '¿El seguro está incluido con el alquiler del vehículo?',
                'answer_es' => 'Nuestros vehículos cuentan con seguro básico incluido. Protección adicional opcional está disponible durante el checkout. Pregúntenos si necesita ayuda para elegir la cobertura adecuada.',
            ],
            [
                'scope' => Faq::SCOPE_VEHICLE,
                'page_keys' => null,
                'sort_order' => 20,
                'question_en' => 'What is your cancellation policy for car rentals?',
                'answer_en' => 'Free cancellation is available on most bookings when cancelled within the allowed window before pickup. Specific terms may vary by vehicle and season — check your confirmation or contact us.',
                'question_es' => '¿Cuál es su política de cancelación para alquileres de autos?',
                'answer_es' => 'La cancelación gratuita está disponible en la mayoría de reservas cuando se cancela dentro del plazo permitido antes de la recogida. Los términos específicos pueden variar según el vehículo y la temporada.',
            ],
            [
                'scope' => Faq::SCOPE_PROPERTY,
                'page_keys' => null,
                'sort_order' => 10,
                'question_en' => 'What is the minimum lease term for homes and apartments?',
                'answer_en' => 'Most of our Miami rentals require a minimum stay of 30 nights. Some listings may offer weekly or nightly rates — see the listing details or send an inquiry for availability.',
                'question_es' => '¿Cuál es la estadía mínima para casas y apartamentos?',
                'answer_es' => 'La mayoría de nuestros alquileres en Miami requieren una estadía mínima de 30 noches. Algunos anuncios pueden ofrecer tarifas semanales o por noche — consulte los detalles o envíe una consulta.',
            ],
            [
                'scope' => Faq::SCOPE_PROPERTY,
                'page_keys' => null,
                'sort_order' => 20,
                'question_en' => 'Are utilities included in the monthly rent?',
                'answer_en' => 'It depends on the property. Each listing notes whether utilities, Wi‑Fi, parking, or other amenities are included. Review the amenities section or ask us before booking.',
                'question_es' => '¿Los servicios públicos están incluidos en la renta mensual?',
                'answer_es' => 'Depende de la propiedad. Cada anuncio indica si servicios, Wi‑Fi, estacionamiento u otras comodidades están incluidos. Revise la sección de amenidades o pregúntenos antes de reservar.',
            ],
        ];

        foreach ($items as $item) {
            $existing = Faq::where('scope', $item['scope'])
                ->where('sort_order', $item['sort_order'])
                ->whereHas('translations', fn ($q) => $q->where('locale', 'en')->where('question', $item['question_en']))
                ->first();

            if ($existing) {
                continue;
            }

            $faq = Faq::create([
                'scope' => $item['scope'],
                'page_keys' => $item['page_keys'],
                'sort_order' => $item['sort_order'],
                'is_active' => true,
            ]);

            $faq->translations()->createMany([
                [
                    'locale' => 'en',
                    'question' => $item['question_en'],
                    'answer' => $item['answer_en'],
                ],
                [
                    'locale' => 'es',
                    'question' => $item['question_es'],
                    'answer' => $item['answer_es'],
                ],
            ]);
        }
    }
}
