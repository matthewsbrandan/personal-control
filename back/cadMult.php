<pre>
<?php
session_start();
if(isset($_SESSION['pctrl_user'])){
    function parseMoney($v){ return str_replace(',','.',str_replace('.','',$v)); }
    function readyArr($value){
        if($value[6]!=1) $value[5]="null";
        $value[8] = $value[2]>0?'Receita':'Despesa';
        foreach($value as &$v){ if($v!="null") $v = "'$v'"; }
        return $value;
    }

    include('../../conn/function.php');
    if(count($_POST)%8==0){
        $arr = array_chunk($_POST,8);        
        
        $arr = array_map('readyArr',$arr);

        $erros = 0;
        for($c=0;$c<count($arr);$c++){
            $sql = "insert into movimento(data_,descricao,valor,categoria_id,relevancia,conta_id,status,obs,tipo,usuario_id) values (";
            foreach($arr[$c] as $value){ $sql .= "$value,"; }
            $sql .= "'{$_SESSION['pctrl_user']}');";
            if(!enviarComand($sql,'bd_pctrl')) $erros++;
        }

        $data1 = enviarComand("call prc_update_caixa({$_SESSION['pctrl_user']});",'bd_pctrl');
        $data2 = enviarComand("call prc_update_saldo('{$_SESSION['pctrl_user']}');",'bd_pctrl');
        $erros = $erros>0?'?errMult='.$erros:'';
        if($data1 && $data2) header('Location: ../dashboard/index.php'.$erros.'#divCategoria');
        else header('Location: ../dashboard/index.php?errMult=0');
    }else header('Location: ../intable/index.php?erro=0');
}else header('Location: ../'); 
?>