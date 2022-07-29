<?php

$num = 2;
$multi = 6;
echo "Numero Original: ".$num;
echo "<br>Multiplicat x: ".$multi;


echo "<br>Resultat: ".multiplicar($num,$multi);

function multiplicar($valor,$multiplicador)
{
    if ($multiplicador == 1)
    {
        return $valor;
    }else
    {   
        return $valor + multiplicar($valor,$multiplicador-1);
    }
}