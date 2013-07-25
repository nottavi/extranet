<?php
namespace ad\ExtraBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class FileTypeNonAllowedValidator extends ConstraintValidator
{
	public function validate($value, Constraint $constraint)
	{	
		var_dump($value);
		die;
		
		if (strpos($value->getClientOriginalName(), '.exe')) {
			$this->context->addViolation($constraint->message, array('%string%' => $value));
		}
	}
}