<?php

namespace Corrigeaton\Bundle\ScheduleBundle\Constraint;


use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ContainsIDValid extends Constraint {
    public $message = 'ID invalide';

    public function validatedBy()
    {
        return 'id_valid_validator';
    }

} 