<?php

namespace Corrigeaton\Bundle\ScheduleBundle\Constraint;

use Corrigeaton\Bundle\ScheduleBundle\Service\ADEService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class ContainsIDValidValidator extends ConstraintValidator {

    private $ADEService;

    public function __construct(ADEService $ade)
    {
        $this->ADEService = $ade;
    }

    public function validate($value, Constraint $constraint)
    {

        if ($this->ADEService->findClassroomName($value) == "") {
            $this->context->addViolation($constraint->message);
        }
    }
} 