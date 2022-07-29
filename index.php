<?php
session_start();

define('MINES',10);
define('DIMENSIONS',10);

?>
<!DOCTYPE html>
<html>
<head>
<title>
    Buscamines
</title>
<script src="script.js"></script>
<style type="text/css">
    td.casella {
        cursor: pointer;
    }
    td{
        border: 1px solid black;
        padding: 0px;        
        margin: 0px;
        font-weight: bold
    }
    table{
        padding: 0px;        
        margin: 0px;
        border-spacing: 0px;
    }
</style>
</head>

<body>
<script type="text/javascript">
    document.oncontextmenu = function(){return false}
</script>
<!-- <button id="boto">Clickam!</button> -->
<!-- <table>
<tr>
<td id="boto">Clickam</td>
</tr>
</table>
<script>
let parent = document.getElementById('boto');

parent.addEventListener('mousedown', function() {
		console.log('El raton se esta presionando');
	});
parent.addEventListener('mouseup', function() {
			console.log('El raton NO se esta presionando');
		});
</script> -->

<?php
if (isset($_GET['cx']))
{
    $cx = $_GET['cx'];
    $cy = $_GET['cy'];
    mostrarCasella($_SESSION['taulell'],$cx,$cy);
    mostrarTaulell($_SESSION['taulell']);
}elseif(isset($_GET['bx']))
{
    $cbx = $_GET['bx'];
    $cby = $_GET['by'];
    if  ($_SESSION['banderes'] <= MINES)
    {
        if ($_SESSION['taulell'][$cby][$cbx]['esBandera'])
        {
            $_SESSION['taulell'][$cby][$cbx]['esBandera'] = false;
            $_SESSION['banderes']--;
        }else{
            if ($_SESSION['banderes'] != MINES)
            {
                 $_SESSION['taulell'][$cby][$cbx]['esBandera'] = true;
                $_SESSION['banderes']++;
            }
        }
    }
    
    // $_SESSION['taulell'][$by][$bx]['esBandera'] = !$_SESSION['taulell'][$by][$bx]['esBandera'];
    //mostrarCasella($_SESSION['taulell'],$cx,$cy);
    mostrarTaulell($_SESSION['taulell']);
}else{
    $_SESSION['final'] = array(
        'guanyat' => false,
        'perdut' => false
    );
    $_SESSION['banderes'] = 0;
    $_SESSION['bx'] = -1;
    $_SESSION['by'] = -1;
    $_SESSION['taulell'] = [];
    inicialitzarTaulell($_SESSION['taulell']);
    posarMines($_SESSION['taulell']);
    mirarNumeroMinesCasella($_SESSION['taulell']);
    mostrarTaulell($_SESSION['taulell']);
}
?>
</body>
</html>

<?php
function inicialitzarTaulell(&$taulell)
{
    for($i=0;$i < DIMENSIONS;$i++)
    {
        for($j=0;$j < DIMENSIONS;$j++)
        {
            $taulell[$i][$j] = array(
                'esMina' => false,
                'numero' => 0,
                'visible' => false,
                'esBandera' => false
            );
        }
    }
}

function getColor($num){

    if($num == 1) return 'blue';
    if($num == 2) return 'green';
    if($num == 3) return 'red';
    if($num == 4) return 'purple';
    return 'black';

}


function mostrarTaulell(&$taulell)
{
    if ($_SESSION['final']['perdut'])
    {
        foreach ($taulell as &$tau)
        {
            foreach ($tau as &$t)
            {
                if ($t['esMina'])
                {
                    $t['visible'] = true;
                }
            }
        }
        echo "<h1>FINAL DEL BUSCAMINES: HAS PERDUT!!!</h1>";
        echo "<input type='button' onclick='resetejar()' id='reset' name='reset' value='Tornari'>";
    }else
    {
        echo "<h1>JOC DEL BUSCAMINES</h1>";
        echo "<h2>BANDERES RESTANTS: ".(MINES - $_SESSION['banderes'])."<br>";
    }  
    echo "<table>";
    for($i=0;$i<DIMENSIONS;$i++)
    {
        echo "<tr>";
        for($j=0;$j<DIMENSIONS;$j++)
        {
            if (!$taulell[$j][$i]['visible'])
            {
                if ($taulell[$j][$i]['esBandera'])
                {
                    echo "<td class='casella' width='50px' height='50px'><img name='".$i."_".$j."' width='50px' height='50px' src='./bandera_bona.png'></img></td>";
                }
                elseif (!$_SESSION['final']['perdut'] && !$_SESSION['final']['guanyat'])
                {
                    echo "<td class='casella' width='50px' height='50px' bgcolor='gray' name='".$i."_".$j."' onclick='mostrarCasella(".$i.",".$j.")'></td>";
                    //echo "<td class='casella' width='50px' height='50px' bgcolor='gray'></td>";
                }
                else{
                    echo "<td width='50px' height='50px' bgcolor='gray'></td>";
                }
            }elseif ($taulell[$j][$i]['esMina'])
            {
                if ($_SESSION['bx'] == $i && $_SESSION['by'] == $j)
                {
                    echo "<td width='50px' height='50px' style='text-align: center; vertical-align: middle;' bgcolor='red'><b>M</b></td>";
                }else
                {
                    echo "<td width='50px' height='50px' style='text-align: center; vertical-align: middle;'>M</td>";  
                }    
            }elseif ($taulell[$j][$i]['visible']){
                if ($taulell[$j][$i]['numero'] != 0)
                {
                echo "<td style='text-align: center; color: ".getColor($taulell[$j][$i]['numero'])."; vertical-align: middle;' width='50px' height='50px'>".$taulell[$j][$i]['numero']."</td>";   
                }else
                {
                    echo "<td style='text-align: center; vertical-align: middle;' width='50px' height='50px'></td>";
                }
                
            }
            //echo "<td width='50px' height='50px' bgcolor='gray'></td>";
        }
        echo "</tr>";
    }
    if ($_SESSION['final']['guanyat'])
    {
        echo "<h1>FINAL DEL BUSCAMINES: HAS GUANYAT!!!</h1>";
        echo "<input type='button' onclick='resetejar()' id='reset' name='reset' value='Tornari'>";
    }
    echo "<script>
    let parent = document.getElementsByClassName('casella');
    for (var i=0; i < parent.length; i++) {
        parent[i].onmousedown = function(event){
            console.log(event);
            if (event.buttons == 2)
            {
                console.log(event.target.getAttribute('name'));
                var coord = event.target.getAttribute('name').split('_');
                posarBandera(coord[0],coord[1]);
            }
        }
    };
    </script>";
   
}

function posarMines(&$taulell)
{
    $i = 0;
    while ($i < MINES)
    {
        $posy = mt_rand(0,DIMENSIONS-1);
        $posx = mt_rand(0,DIMENSIONS-1);
        if (!$taulell[$posy][$posx]['esMina'])
        {
            $taulell[$posy][$posx]['esMina'] = true;
            $i++;
        }
    }
}

function mirarNumeroMinesCasella(&$taulell)
{
    for($i=0;$i<DIMENSIONS;$i++)
    {
        for($j=0;$j<DIMENSIONS;$j++)
        {
            $nMines = 0;
            if (!$taulell[$j][$i]['esMina'])
            {
                for ($k = $i-1;$k < ($i+2) ;$k++)
                {
                    for ($l = $j-1;$l < ($j+2) ;$l++)
                    {
                        if (($k >= 0 && $k < DIMENSIONS) && ($l >= 0 && $l < DIMENSIONS))
                        {
                            if ($k != $i || $l != $j)
                            {
                                //Estem miran a dins el taulell
                                if (hiHaMina($taulell,$k,$l))
                                {
                                    $nMines++;
                                }
                            }
                        }
                    }
                }
                $taulell[$j][$i]['numero'] = $nMines; 
            }
        }
    }
}

function hiHaMina($taulell,$posx,$posy)
{
    return $taulell[$posy][$posx]['esMina'];
}

function mostrarCasella(&$taulell,$posx,$posy)
{

    if (($posx >= 0 && $posx < DIMENSIONS) && ($posy >= 0 && $posy < DIMENSIONS))
    {
        //echo "SOC DINS EL BUCLE<br>";
        if ($taulell[$posy][$posx]['numero'] != 0 || $taulell[$posy][$posx]['esMina'] || $taulell[$posy][$posx]['esBandera'] )
        {
            if ($taulell[$posy][$posx]['esMina'])
            {
                $_SESSION['final']['perdut'] = true;
                $_SESSION['bx'] = $posx;
                $_SESSION['by'] = $posy;
                $taulell[$posy][$posx]['visible'] = true;
            }elseif (!$taulell[$posy][$posx]['esBandera'])
            {
                $taulell[$posy][$posx]['visible'] = true;
                comprobarSiGuanyat($taulell);  
            }
          //  echo "HE TROBAT UN NUMERO<br>";
            //$taulell[$posy][$posx]['visible'] = true;
        }elseif (!$taulell[$posy][$posx]['visible'])
        {
            //echo "HE TROBAT UN ESPAI EN BLANC";
            for ($i=$posx-1;$i < ($posx + 2);$i++)
            {
                for ($j=$posy-1;$j < ($posy+2);$j++)
                {
                    if ($i != $posx || $j != $posy)
                    {
                        mostrarCasella($taulell,$i,$j); 
                    }else{
                        $taulell[$posy][$posx]['visible'] = true;
                    }
                }
            }  
            comprobarSiGuanyat($taulell);  
        }
        
    }

}

function comprobarSiGuanyat(&$taulell)
{
    $i=0;
    foreach ($taulell as &$tau)
    {
        foreach ($tau as &$t)
        {
            if (!$t['visible'])
            {
                $i++;
            }
        }
    }
    if ($i == MINES)
    {
        $_SESSION['final']['guanyat'] = true;
    }
}



?>