<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            // ── General (5) — homepage, contact, booking ──
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
                'scope' => Faq::SCOPE_GENERAL,
                'page_keys' => ['home', 'contact', 'booking.step1'],
                'sort_order' => 30,
                'question_en' => 'What is the minimum age to rent a vehicle?',
                'answer_en' => 'Drivers must typically be at least 21 years old (25 for luxury and exotic vehicles). A valid license held for at least one year is required. Young-driver fees may apply for renters under 25.',
                'question_es' => '¿Cuál es la edad mínima para alquilar un vehículo?',
                'answer_es' => 'Los conductores deben tener al menos 21 años (25 para vehículos de lujo y exóticos). Se requiere licencia válida por al menos un año. Pueden aplicarse cargos para conductores menores de 25.',
            ],
            [
                'scope' => Faq::SCOPE_GENERAL,
                'page_keys' => ['home', 'contact'],
                'sort_order' => 40,
                'question_en' => 'Can I pay online with a credit card?',
                'answer_en' => 'Yes. Secure card payment is available at checkout through Stripe. You can also choose pay-at-pickup on eligible bookings. A confirmation email is sent once payment or reservation is complete.',
                'question_es' => '¿Puedo pagar en línea con tarjeta de crédito?',
                'answer_es' => 'Sí. El pago seguro con tarjeta está disponible en el checkout mediante Stripe. También puede elegir pago al recoger en reservas elegibles. Se envía un correo de confirmación al completar el pago o la reserva.',
            ],
            [
                'scope' => Faq::SCOPE_GENERAL,
                'page_keys' => ['home', 'contact'],
                'sort_order' => 50,
                'question_en' => 'Do you accept international driver\'s licenses?',
                'answer_en' => 'Yes, we welcome international visitors. Bring your passport, home-country license, and an International Driving Permit if required by Florida law for your country. Contact us before arrival if you are unsure.',
                'question_es' => '¿Aceptan licencias de conducir internacionales?',
                'answer_es' => 'Sí, recibimos visitantes internacionales. Traiga pasaporte, licencia de su país y Permiso Internacional de Conducir si la ley de Florida lo requiere. Contáctenos antes de llegar si tiene dudas.',
            ],

            // ── Vehicles (5) — all vehicle detail pages ──
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
                'scope' => Faq::SCOPE_VEHICLE,
                'page_keys' => null,
                'sort_order' => 30,
                'question_en' => 'Is there a mileage limit on rentals?',
                'answer_en' => 'Most daily and weekly rentals include generous mileage. Long-term and specialty vehicles may have specific limits — see your quote or listing details. Extra miles are billed at a per-mile rate shown before you confirm.',
                'question_es' => '¿Hay límite de millas en los alquileres?',
                'answer_es' => 'La mayoría de alquileres diarios y semanales incluyen millas generosas. Vehículos de largo plazo o especiales pueden tener límites — consulte su cotización. Las millas extra se facturan según la tarifa indicada antes de confirmar.',
            ],
            [
                'scope' => Faq::SCOPE_VEHICLE,
                'page_keys' => null,
                'sort_order' => 40,
                'question_en' => 'Can I add an additional driver?',
                'answer_en' => 'Yes. Additional drivers must meet the same age and license requirements and be added before or at pickup. A small daily fee may apply per extra driver — we will confirm the amount when you book.',
                'question_es' => '¿Puedo agregar un conductor adicional?',
                'answer_es' => 'Sí. Los conductores adicionales deben cumplir los mismos requisitos de edad y licencia y registrarse antes o al recoger el vehículo. Puede aplicarse una tarifa diaria por conductor extra.',
            ],
            [
                'scope' => Faq::SCOPE_VEHICLE,
                'page_keys' => null,
                'sort_order' => 50,
                'question_en' => 'What fuel policy do you use?',
                'answer_en' => 'Most rentals use a full-to-full policy: receive the vehicle with a full tank and return it full to avoid refueling charges. If you return with less fuel, a refueling fee plus the cost of missing fuel may apply.',
                'question_es' => '¿Cuál es su política de combustible?',
                'answer_es' => 'La mayoría de alquileres usan política lleno a lleno: reciba el vehículo con tanque lleno y devuélvalo lleno para evitar cargos. Si devuelve con menos combustible, puede aplicarse tarifa de reabastecimiento.',
            ],

            // ── Properties / Buildings (5) — all listing detail pages ──
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
            [
                'scope' => Faq::SCOPE_PROPERTY,
                'page_keys' => null,
                'sort_order' => 30,
                'question_en' => 'How much is the security deposit?',
                'answer_en' => 'Security deposits vary by property and are typically equal to one month\'s rent or as noted on the listing. The deposit is held for damages or unpaid balances and returned after move-out inspection per your lease terms.',
                'question_es' => '¿Cuánto es el depósito de seguridad?',
                'answer_es' => 'Los depósitos varían según la propiedad y suelen equivaler a un mes de renta o lo indicado en el anuncio. Se retiene por daños o saldos pendientes y se devuelve tras la inspección de salida según el contrato.',
            ],
            [
                'scope' => Faq::SCOPE_PROPERTY,
                'page_keys' => null,
                'sort_order' => 40,
                'question_en' => 'Are pets allowed in rental homes?',
                'answer_en' => 'Many listings are pet-friendly — look for the pets-allowed badge on each property. Breed and size restrictions may apply in condos and HOAs. A pet deposit or monthly pet fee may be required.',
                'question_es' => '¿Se permiten mascotas en las propiedades?',
                'answer_es' => 'Muchos anuncios aceptan mascotas — busque el indicador en cada propiedad. Pueden aplicarse restricciones de raza o tamaño en condominios. Se puede requerir depósito o tarifa mensual por mascota.',
            ],
            [
                'scope' => Faq::SCOPE_PROPERTY,
                'page_keys' => null,
                'sort_order' => 50,
                'question_en' => 'How do I apply or schedule a property tour?',
                'answer_en' => 'Click the inquiry button on any listing or message us on WhatsApp with the property reference. We arrange in-person or virtual tours and guide you through the application, lease, and move-in process.',
                'question_es' => '¿Cómo solicito o programo una visita a la propiedad?',
                'answer_es' => 'Use el botón de consulta en el anuncio o escríbanos por WhatsApp con la referencia. Coordinamos visitas presenciales o virtuales y le guiamos en la solicitud, contrato y mudanza.',
            ],
        ];

        foreach ($items as $item) {
            $existing = Faq::where('scope', $item['scope'])
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
