<?php
namespace AppBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class OrderDateValidator extends ConstraintValidator {
    public function validate($date, Constraint $constraint) {
       if ( !$this->isDateValid($date) ) {
            $this->context->buildViolation( $constraint->message )
               			  ->addViolation();
        }
    }

    public function isDateValid($date) {
    	//if day is either sunday or tuesday, we not good
    	$day = $date->format('w');
		if ( $day === '0' || $day === '2' )
    		return false;

    	//if chosen date is prior to today we not good
    	$today = new \DateTime('today midnight');
    	if ( $date < $today )
    		return false;

    	//if chosen date is either of the bank holidays, we not good
    	$dateClean = $date->format('d-m');
    	$forbiddenDates = ['01-05', '01-11', '25-12'];
    	if ( in_array($dateClean, $forbiddenDates) )
    		return false;
    	

    	return true;
    }
}