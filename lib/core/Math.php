<?php
class Math
{

	public static function Div($num1, $num2, $zero = true)
	{
		if (! is_numeric ( $num1 ) || ! is_numeric ( $num2 )) throw new Exception ( 'Function Div() input error!' );
		
		if ($num2 == 0)
		{
			if ($zero) return 0;
			else throw new Exception ( 'Function Div() zero-overflow!' );
		}
		return $num1 / $num2;
	}
}
?>