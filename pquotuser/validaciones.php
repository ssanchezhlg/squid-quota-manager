<?php

function validaIp($ip){
    $ipPattern = '^([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})\.([0-9]{1,3})$';
    if (preg_match('/'.$ipPattern.'/', $ip, $arr)){
        $biger = 0;
        $i = 0;
        while ((!$biger) && ($i<=count($arr)) ){
            if ($arr[$i] > 255){
                $biger = 1;
                return false;
            }
            $i++;
        }
        return true;
    }else
    {
        return false;
    }
}

function validaCuota($quota){
    $idPattern = '^[0-9]+$';
    if (preg_match('/'.$idPattern.'/', $quota)){
        return true;
    }
    else{
        return false;
    }
}
?>
