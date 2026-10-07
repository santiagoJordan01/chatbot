<?php

namespace App\Services;

class ClinicReplyGuard
{
    /**
     * @param  array<int, array{role: string, content: string}>  $history
     */
    public function apply(string $query, array $history, string $reply): string
    {
        $reply = $this->stripMarkdown($reply);
        $reply = $this->correctUnspecifiedVisit($query, $history, $reply);
        $reply = $this->correctWeekendDental($query, $history, $reply);
        $reply = $this->correctLateDental($query, $history, $reply);
        $reply = $this->correctWeightPrice($query, $history, $reply);
        $reply = $this->correctFasting($query, $history, $reply);

        return $this->removeFalseAvailability($reply);
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     */
    protected function correctUnspecifiedVisit(string $query, array $history, string $reply): string
    {
        if ($this->isDentalConversation($query, $history)) {
            return $reply;
        }

        $asksSlot = preg_match('/turno|tarde|disponib|cita|agendar/u', mb_strtolower($query)) === 1;
        $soundsDental = preg_match('/14:00|14\.00|raza|limpieza dental/u', mb_strtolower($reply)) === 1;

        if (! $asksSlot || ! $soundsDental) {
            return $reply;
        }

        return 'La clínica atiende de lunes a viernes de 8:00 a 18:00 y los sábados de 8:00 a 13:00. Por la tarde se puede pedir consulta, estética o control. La limpieza dental solo entra de lunes a viernes de 8:00 a 14:00. ¿Qué servicio necesitas?';
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     */
    protected function correctWeekendDental(string $query, array $history, string $reply): string
    {
        if (! $this->isDentalConversation($query, $history) || $this->refusesWeekendDental(mb_strtolower($reply))) {
            return $reply;
        }

        $userWantsWeekend = preg_match('/s[aá]bado|domingo/u', mb_strtolower($query)) === 1;
        $offersSaturday = preg_match('/s[aá]bado/u', mb_strtolower($reply)) === 1;

        if (! $userWantsWeekend && ! $offersSaturday) {
            return $reply;
        }

        return 'Las limpiezas dentales solo se agendan de lunes a viernes, entre 8:00 y 14:00. El sábado la clínica atiende de 8:00 a 13:00, pero ese día no hace limpiezas. ¿Qué día de lunes a viernes te sirve?';
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     */
    protected function correctLateDental(string $query, array $history, string $reply): string
    {
        if (! $this->isDentalConversation($query, $history)) {
            return $reply;
        }

        $asksLate = preg_match('/\b(1[5-9]|2[0-3])\s*:\s*\d{2}\b|\b(1[5-9]|2[0-3])\s*h\b|[4-6]\s*(?:p\.?\s*m\.?|de la tarde)|despu[eé]s de las 14/u', mb_strtolower($query)) === 1;
        $refuses = preg_match('/no |solo se agenda|entre 8:00 y 14:00|entre las 8:00 y las 14:00/u', mb_strtolower($reply)) === 1;

        if (! $asksLate || $refuses) {
            return $reply;
        }

        return 'La limpieza dental solo se agenda de lunes a viernes entre 8:00 y 14:00. Después de las 14:00 la clínica sigue abierta para consulta, estética y control, no para limpiezas.';
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     */
    protected function correctWeightPrice(string $query, array $history, string $reply): string
    {
        $kg = $this->weightKg($query, $history);
        if ($kg === null || ! $this->isDentalConversation($query, $history)) {
            return $reply;
        }

        if ($kg < 10) {
            [$size, $price] = ['pequeño', '180.000'];
        } elseif ($kg <= 25) {
            [$size, $price] = ['mediano', '240.000'];
        } else {
            [$size, $price] = ['grande', '320.000'];
        }

        preg_match_all('/180[.\s]?000|240[.\s]?000|320[.\s]?000/u', $reply, $found);
        $normalized = array_values(array_unique(array_map(function (string $amount) {
            $digits = preg_replace('/\D/', '', $amount) ?? $amount;

            return match ($digits) {
                '180000' => '180.000',
                '240000' => '240.000',
                '320000' => '320.000',
                default => $amount,
            };
        }, $found[0])));

        if (count($normalized) !== 1 || $normalized[0] === $price) {
            return $reply;
        }

        $label = rtrim(rtrim(number_format($kg, 1, '.', ''), '0'), '.');

        return "Un perro de {$label} kg es {$size}. La limpieza dental cuesta {$price} pesos. De 10 a 25 kg, incluidos 10 y 25, son 240.000 pesos. Más de 25 kg son 320.000 pesos. Menos de 10 kg son 180.000 pesos.";
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     */
    protected function correctFasting(string $query, array $history, string $reply): string
    {
        if (! $this->isDentalConversation($query, $history)) {
            return $reply;
        }

        $asksArrival = preg_match('/a qu[eé] hora|hora de llegada|minutos antes|debo llegar|tengo que llegar/u', mb_strtolower($query)) === 1;
        if (! $asksArrival) {
            return $reply;
        }

        return 'No hay una hora de llegada distinta a la de la cita. El ayuno de 8 horas significa no comer durante las 8 horas anteriores, no llegar antes.';
    }

    protected function removeFalseAvailability(string $reply): string
    {
        $parts = preg_split('/(?<=[\.\!\?])\s+/u', $reply) ?: [$reply];
        $kept = [];
        $removed = false;

        foreach ($parts as $part) {
            $lower = mb_strtolower($part);
            $claimsSlot = preg_match('/(está|esta) disponible|tenemos disponibilidad|hay disponibilidad|hay cupo|horario de .+ disponible|queda reservad|qued[oó] reservad|qued[oó] agendad/u', $lower) === 1;
            $denies = preg_match('/\bno\b/u', $lower) === 1;

            if ($claimsSlot && ! $denies) {
                $removed = true;
                continue;
            }

            $kept[] = $part;
        }

        if (! $removed) {
            return $reply;
        }

        return trim(implode(' ', $kept)."\nEse horario lo confirma el equipo. Desde el chat no se puede ver si el cupo está libre.");
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     */
    protected function weightKg(string $query, array $history): ?float
    {
        $sources = [$query];
        foreach (array_reverse($history) as $item) {
            if (($item['role'] ?? '') === 'user') {
                $sources[] = (string) $item['content'];
            }
        }

        foreach ($sources as $source) {
            if (preg_match('/(\d+(?:[.,]\d+)?)\s*kg/ui', $source, $match) === 1) {
                return (float) str_replace(',', '.', $match[1]);
            }
        }

        return null;
    }

    /**
     * @param  array<int, array{role: string, content: string}>  $history
     */
    protected function isDentalConversation(string $query, array $history): bool
    {
        $blob = mb_strtolower($query.' '.collect($history)->pluck('content')->implode(' '));

        return preg_match('/limpieza|dental|sarro/u', $blob) === 1;
    }

    protected function refusesWeekendDental(string $reply): bool
    {
        return preg_match('/no podemos|no se agenda|no agendamos|no hacemos|nunca el s[aá]bado|solo de lunes|únicamente de lunes|unicamente de lunes/u', $reply) === 1;
    }

    protected function stripMarkdown(string $reply): string
    {
        $reply = preg_replace('/\*\*(.*?)\*\*/s', '$1', $reply) ?? $reply;
        $reply = str_replace(['**', '__'], '', $reply);

        return trim($reply);
    }
}
