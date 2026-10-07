<?php

namespace Tests\Unit;

use App\Services\ClinicReplyGuard;
use Tests\TestCase;

class ClinicReplyGuardTest extends TestCase
{
    public function test_it_rejects_a_saturday_dental_booking(): void
    {
        $reply = (new ClinicReplyGuard())->apply(
            'Sabado a las 9am',
            [
                ['role' => 'user', 'content' => 'Quiero la limpieza dental de Igor'],
                ['role' => 'assistant', 'content' => 'El precio es 240.000 pesos'],
            ],
            'Claro, tenemos disponibilidad el sábado a las 9 a.m. Precio **240.000** pesos.',
        );

        $this->assertStringContainsString('lunes a viernes', $reply);
        $this->assertStringNotContainsString('disponibilidad el sábado', $reply);
        $this->assertStringNotContainsString('**', $reply);
    }

    public function test_it_keeps_a_reply_that_already_refuses_saturday(): void
    {
        $original = 'No podemos agendar la limpieza el sábado. Solo de lunes a viernes entre 8:00 y 14:00.';

        $reply = (new ClinicReplyGuard())->apply('Sabado a las 9 am', [
            ['role' => 'user', 'content' => 'limpieza dental'],
        ], $original);

        $this->assertSame($original, $reply);
    }

    public function test_it_prices_a_25kg_dog_as_medium(): void
    {
        $reply = (new ClinicReplyGuard())->apply(
            'Mi perro pesa 25 kg. ¿Cuánto cuesta la limpieza dental?',
            [],
            'El precio para un perro de más de 25 kg es de 320.000 pesos.',
        );

        $this->assertStringContainsString('240.000', $reply);
        $this->assertStringContainsString('mediano', $reply);
        $this->assertStringNotContainsString('320.000 pesos.', mb_substr($reply, 0, 80));
    }

    public function test_it_does_not_treat_an_afternoon_slot_as_dental(): void
    {
        $reply = (new ClinicReplyGuard())->apply(
            '¿Tienen turno mañana por la tarde?',
            [],
            'Sí, tenemos turnos de 8:00 a 14:00. Necesito especie, edad, peso y raza.',
        );

        $this->assertStringContainsString('18:00', $reply);
        $this->assertStringContainsString('¿Qué servicio necesitas?', $reply);
    }

    public function test_it_explains_fasting_instead_of_an_arrival_time(): void
    {
        $reply = (new ClinicReplyGuard())->apply(
            'La cita es a las 9. ¿A qué hora debo llegar?',
            [
                ['role' => 'user', 'content' => 'Quiero la limpieza dental'],
                ['role' => 'assistant', 'content' => 'Hay ayuno de 8 horas.'],
            ],
            'Llega a las 8:45, unos 15 minutos antes.',
        );

        $this->assertStringContainsString('no comer', $reply);
        $this->assertStringNotContainsString('8:45', $reply);
    }

    public function test_it_does_not_claim_a_slot_is_free(): void
    {
        $reply = (new ClinicReplyGuard())->apply(
            'Confirmo la consulta del sábado a las 9.',
            [],
            'El sábado a las 9 está disponible. Cuesta 60.000 pesos.',
        );

        $this->assertStringContainsString('confirma el equipo', $reply);
        $this->assertStringNotContainsString('está disponible', $reply);
        $this->assertStringContainsString('60.000', $reply);
    }
}
