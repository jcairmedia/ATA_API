<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CustomDateMeeting implements Rule
{
    private $message = '';

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->message = '';
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $date = new \DateTime($value);
        $now = new \DateTime();
        $dt_interval = $now->diff($date);
        if ((int) $dt_interval->invert == 1 && $dt_interval->days >0) {
            // $fail('Fecha pasada invalida');
            $this->message = 'El :attribute no puede ser una fecha anterior a la fecha actual';

            return false;
        }
        if (($dt_interval->days < 0 || $dt_interval->days >= 26)) {
            // $fail('No se pueden realizar citas con más de 25 días o menos días a la fecha actual');
            $this->message = 'El :attribute no puede ser mayor en 25 días apartir de la fecha actual';

            return false;
        }
        $dia_week = (int) $date->format('N');
        if ($dia_week >= 6) {
            $this->message = 'El :attribute no puede estar en fines de semana';

            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message != '' ? $this->message : 'The :attribute is invalid';
    }
}
