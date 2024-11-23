<?php

/**
 *
 * Function code for the complex asech() function
 *
 */
namespace Complex;

/**
 * Returns the inverse hyperbolic secant of a complex number.
 *
 * @param     Complex|mixed    $complex    Complex number or a numeric value.
 * @return    Complex          The inverse hyperbolic secant of the complex argument.
 * @throws    Exception        If argument isn't a valid real or complex number.
 * @throws    \InvalidArgumentException    If function would result in a division by zero
 */
function asech($complex)
{
    $complex = Complex::validateComplexArgument($complex);

    if ($complex->getReal() == 0.0 && $complex->getImaginary() == 0.0) {
        return INF;
    }

    return acosh(inverse($complex));
}
