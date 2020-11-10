<?php
session_start();
if(isset($_SESSION['pctrl_user'])){
    function parseMoney($v){ return str_replace(',','.',str_replace('.','',$v)); }
    include('../../conn/function.php');
    if(isset($_GET['categoria'])){
        $_POST['catNome']=trim($_POST['catNome']);
        if($_POST['catNome']!='Outros' && $_POST['catNome']!='Transferência' && $_POST['catNome']!='Margem de Erro'){
            if(strlen($_POST['catNome'])>0){
                $sql = "insert into categoria(nome,usuario_id) values ('{$_POST['catNome']}','{$_SESSION['pctrl_user']}');";
                $data = enviarComand($sql,'bd_pctrl');
                if($data) header('Location: ../dashboard/index.php?cat=1#divCategoria');
                else      header('Location: ../dashboard/index.php?cat=2#divCategoria');
            }else header('Location: ../dashboard/index.php?cat=4#divCategoria');
        }else header('Location: ../dashboard/index.php?cat=3#divCategoria');
    }else
    if(isset($_GET['conta'])){
        $sql = "insert into conta(nome,usuario_id) values ('{$_POST['contaNome']}','{$_SESSION['pctrl_user']}');";
        $data = enviarComand($sql,'bd_pctrl');
        if($data) header('Location: ../dashboard/index.php?cont=1');
        else      header('Location: ../dashboard/index.php?cont=2');        
    }else
    if(isset($_GET['movimento'])){
        $obs = $_POST['movObs']?"'{$_POST['movObs']}'":'null';
        $valor = parseMoney($_POST['movValor']);
        $status = $_POST['movStatus']=="true"?',1,'.$_POST['movConta']:'';
        $colStatus = $_POST['movStatus']=="true"?',status,conta_id':'';
        if($_POST['movTipo']=="Transferencia"){
            $status = $_POST['movStatus']=="true"?1:0;
            $sql = "call prc_new_transferencia('{$_POST['movTransfereDe']}','{$_POST['movTransferePara']}','{$_POST['movData']}','$valor','{$_POST['movRelevancia']}','{$_SESSION['pctrl_user']}',$obs,$status);";
            $data = enviarComand($sql,'bd_pctrl');
            if($data){
                $data1 = enviarComand("call prc_update_caixa({$_SESSION['pctrl_user']});",'bd_pctrl');
                if($data1){
                    header('Location: ../dashboard/index.php?mov=1#divParcela');
                }else header('Location: ../dashboard/index.php?mov=2#divParcela');
            }else header('Location: ../dashboard/index.php?mov=2#divParcela');
        }
        else{
            $sql = "insert into movimento(tipo,data_,valor,descricao,obs,relevancia,categoria_id,usuario_id$colStatus) values ";
            $sql.= "('{$_POST['movTipo']}', '{$_POST['movData']}', '$valor', '{$_POST['movDescricao']}', $obs,'{$_POST['movRelevancia']}', '{$_POST['movCategoria']}', '{$_SESSION['pctrl_user']}'$status);";            
            $data = enviarComand($sql,'bd_pctrl');
            if($data){
                $data1 = enviarComand("call prc_update_caixa({$_SESSION['pctrl_user']});",'bd_pctrl');
                $data2 = enviarComand("call prc_update_saldo('{$_SESSION['pctrl_user']}');",'bd_pctrl');
                if($data1 && $data2) header('Location: ../dashboard/index.php?mov=1#divPendente');
                else header('Location: ../dashboard/index.php?mov=2#divPendente');
            }else header('Location: ../dashboard/index.php?mov=2#divPendente');
        }
    }else
    if(isset($_GET['objetivo'])){
        $valor = parseMoney($_POST['objValor']);
        $sql="insert objetivo(nome,valor,relevancia,usuario_id) values ('{$_POST['objNome']}', '$valor', '{$_POST['objRelevancia']}', '{$_SESSION['pctrl_user']}');";
        $data = enviarComand($sql,'bd_pctrl');
        if($data) header('Location: ../dashboard/index.php?obj=1#divObjetivo');
        else      header('Location: ../dashboard/index.php?obj=2#divObjetivo');
    }else
    if(isset($_GET['parcela'])){
        $valor = parseMoney($_POST['movValor']);
        $obs = $_POST['movObs']?"'{$_POST['movObs']}'":'null';
        $sql = "call prc_new_parcela('{$_POST['movTipo']}', '{$_POST['movData']}', '$valor', '{$_POST['movDescricao']}', $obs,'{$_POST['movRelevancia']}', '{$_POST['movCategoria']}', '{$_POST['parcRotatividade']}', '{$_POST['parcQtd']}','{$_SESSION['pctrl_user']}');";
        $data = enviarComand($sql,'bd_pctrl');
        if($data){
            $sql = "select parcela_id from movimento where tipo='{$_POST['movTipo']}' and data_='{$_POST['movData']}' and descricao='{$_POST['movDescricao']}' and categoria_id='{$_POST['movCategoria']}' and usuario_id='{$_SESSION['pctrl_user']}' order by id desc limit 1;";
            $dataid = enviarComand($sql,'bd_pctrl');
            $resid = $dataid->fetch_assoc();
            $data1 = enviarComand("call prc_update_caixa({$_SESSION['pctrl_user']});",'bd_pctrl');
            if($data1){
                header('Location: ../dashboard/index.php?parc='.$resid['parcela_id'].'#divParcela');
            }else header('Location: ../dashboard/index.php?mov=2#divParcela');
        }else header('Location: ../dashboard/index.php?mov=2#divParcela');
    }else
    if(isset($_GET['pagar'])){
        if(isset($_POST['pagarId'])&&isset($_POST['pagarConta'])&&isset($_POST['pagarDiv'])){
            $sql = "update movimento set status=true, conta_id='{$_POST['pagarConta']}' where id='{$_POST['pagarId']}' and usuario_id='{$_SESSION['pctrl_user']}';";
            $data = enviarComand($sql,'bd_pctrl');
            if($data){
                $sql = "call prc_update_caixa('{$_SESSION['pctrl_user']}');"; $dc = enviarComand($sql,'bd_pctrl');
                $sql = "call prc_update_saldo('{$_SESSION['pctrl_user']}');"; $ds = enviarComand($sql,'bd_pctrl');
                $concat = (isset($_GET['calendarM'])?'&calendarM='.$_GET['calendarM']:'').(isset($_GET['calendarY'])?'&calendarY='.$_GET['calendarY']:'');
                if($dc && $ds) header('Location: ../dashboard/index.php?pago'.$concat.'#'.$_POST['pagarDiv']);
                else header('Location: ../dashboard/index.php?errPg#divPendente');
            }
            
        }
    }else
    if(isset($_GET['pagarMult'])){
        $concat = (isset($_GET['calendarM'])?'&calendarM='.$_GET['calendarM']:'').(isset($_GET['calendarY'])?'&calendarY='.$_GET['calendarY']:'');
        if(isset($_POST['pagarMult'])){
            $ids = "";
            foreach($_POST as $k => $v){ if(!(strpos($k,'check')===false)){ $ids.="id='".$_POST['inp'.substr($k,5)]."' or "; } }
            if(strlen($ids)>0){
                $ids = substr($ids,0,-4);
                $sql = "update movimento set status='1',conta_id='{$_POST['pagarMult']}' where ".$ids." and usuario_id='{$_SESSION['pctrl_user']}';";
                $data = enviarComand($sql,'bd_pctrl');
                if($data){
                    $sql = "call prc_update_caixa('{$_SESSION['pctrl_user']}');"; $dc = enviarComand($sql,'bd_pctrl');
                    $sql = "call prc_update_saldo('{$_SESSION['pctrl_user']}');"; $ds = enviarComand($sql,'bd_pctrl');
                    if($dc && $ds) header('Location: ../dashboard/index.php?errFmm=0'.$concat.'#divPendente');
                    else header('Location: ../dashboard/index.php?errFmm=3#divPendente');
                }
            }else header('Location: ../dashboard/index.php?errFmm=1'.$concat.'#divPendente');
        }else header('Location: ../dashboard/index.php?errFmm=2'.$concat.'#divPendente');


//            
//        }
    }else
    if(isset($_GET['reset'])){
        $retorno = 0;
        $sql = "delete from objetivo where usuario_id='{$_SESSION['pctrl_user']}';";
        if(!enviarComand($sql,'bd_pctrl')) $retorno = 1;
        else{
            $sql = "delete from movimento where usuario_id='{$_SESSION['pctrl_user']}';";
            if(!enviarComand($sql,'bd_pctrl')) $retorno = 2;
            else{
                $sql = "delete from parcela where usuario_id='{$_SESSION['pctrl_user']}';";
                if(!enviarComand($sql,'bd_pctrl')) $retorno = 3;
                else{
                    $sql = "delete from caixa where usuario_id='{$_SESSION['pctrl_user']}';";
                    if(!enviarComand($sql,'bd_pctrl')) $retorno = 4;
                }
            }
            $sql = "call prc_update_caixa('{$_SESSION['pctrl_user']}');"; $retorno = enviarComand($sql,'bd_pctrl')?$retorno:5;
            $sql = "call prc_update_saldo('{$_SESSION['pctrl_user']}');"; $retorno = enviarComand($sql,'bd_pctrl')?$retorno:6;
        }
        header('Location: ../dashboard/index.php?reset='.$retorno);
    }else
    if(isset($_GET['apagar'])){
        if(enviarComand("select count(*) cont from movimento where id='{$_GET['apagar']}' and parcela is null;",'bd_pctrl')->fetch_assoc()['cont']==1){
            echo "Não é parcelado";
//            $sql = "delete from movimento where id='{$_GET['apagar']}' and usuario_id='{$_SESSION['pctrl_user']}';";
//            $data = enviarComand($sql,'bd_pctrl');
//            $sql = "call prc_update_caixa('{$_SESSION['pctrl_user']}');"; $retorno = enviarComand($sql,'bd_pctrl')?true:false;
//            $sql = "call prc_update_saldo('{$_SESSION['pctrl_user']}');"; $retorno = enviarComand($sql,'bd_pctrl')?$retorno:false;
//            if($data && $retorno) header('Location: ../dashboard/index.php?apagar=0');
//            else header('Location: ../dashboard/index.php?apagar=1');
        }else echo "Movimentação Parcelada. Isso mudara a estrutura da parcela";
    }else
    if(isset($_GET['update'])){
        $status = "";
        if($_POST['dtStatus']=='Pendente') $status = " status=0 , conta_id=null,";
        $obs = $_POST['dtObs']?"'{$_POST['dtObs']}'":'null';
        $_POST['dtValor'] = parseMoney($_POST['dtValor']);
        if($_POST['dtValor']>0 && $_POST['dtTipo']=='Despesa'||$_POST['dtValor']<0 && $_POST['dtTipo']=='Receita') $_POST['dtValor']*=(-1);
        $sql = "select id from caixa where year(mesano)=year('{$_POST['dtData']}') and month(mesano)=month('{$_POST['dtData']}') and usuario_id='{$_SESSION['pctrl_user']}';";
        $data = enviarComand($sql,'bd_pctrl');
        if($res = $data->fetch_assoc()) $caixa = $res['id'];
        else{
            $sql = "insert into caixa(mesano,usuario_id) values(concat(year('{$_POST['dtData']}'),'-',month('{$_POST['dtData']}'),'-01'),'{$_SESSION['pctrl_user']}');";
            enviarComand($sql,'bd_pctrl');
            $sql = "select id from caixa where year(mesano)=year('{$_POST['dtData']}') and month(mesano)=month('{$_POST['dtData']}') and usuario_id='{$_SESSION['pctrl_user']}';";
            $data = enviarComand($sql,'bd_pctrl');
            if($res = $data->fetch_assoc()) $caixa = $res['id'];
            else{
                enviarComand("call prc_update_caixa({$_SESSION['pctrl_user']});",'bd_pctrl');
                enviarComand("call prc_update_saldo('{$_SESSION['pctrl_user']}');",'bd_pctrl');
                $erro = 1;
            } 
        }
        if(!(isset($erro) && $erro==1)){   
            $sql = "update movimento set tipo='{$_POST['dtTipo']}', data_='{$_POST['dtData']}', valor='{$_POST['dtValor']}', descricao='{$_POST['dtDescricao']}', obs=$obs,$status relevancia='{$_POST['dtRelevancia']}', categoria_id='{$_POST['dtCategoria']}', caixa_id='$caixa' where id='{$_POST['dtId']}' and usuario_id='{$_SESSION['pctrl_user']}';";
            echo $sql;
            if(enviarComand($sql,'bd_pctrl')){
                enviarComand("call prc_update_caixa({$_SESSION['pctrl_user']});",'bd_pctrl');
                enviarComand("call prc_update_saldo('{$_SESSION['pctrl_user']}');",'bd_pctrl');
                header('Location: ../dashboard/index.php?update=0#divMovimento');
            } 
            else header('Location: ../dashboard/index.php?update=2#divMovimento');
        }else header('Location: ../dashboard/index.php?update=1#divMovimento');
    }else
    if(isset($_GET['meta'])){
        $res = (enviarComand("select count(*) tem from caixa where mesano='{$_POST['metaData']}' and usuario_id={$_SESSION['pctrl_user']};",'bd_pctrl'))->fetch_assoc();
        if($res['tem']!=1){
            if(!enviarComand("insert into caixa(mesano,usuario_id) values('{$_POST['metaData']}','{$_SESSION['pctrl_user']}');",'bd_pctrl'))
            { header('Location: ../dashboard/index.php?meta=1#divCaixa'); }
        }
        $valor = parseMoney($_POST['metaValor']);
        $sql = "update caixa set meta='$valor' where usuario_id='{$_SESSION['pctrl_user']}' and mesano='{$_POST['metaData']}';";
        if(enviarComand($sql,'bd_pctrl')){
            enviarComand("call prc_update_caixa({$_SESSION['pctrl_user']});",'bd_pctrl');
            header('Location: ../dashboard/index.php?meta=0#divCaixa');
        }else header('Location: ../dashboard/index.php?meta=1#divCaixa');
    }else
    if(isset($_GET['gerenciarCategoria'])){
        if(isset($_POST['fgcAcao'])&&isset($_POST['fgcId'])){
            if($_POST['fgcAcao']=="deletar"||$_POST['fgcAcao']=="substitui"){
                if($_POST['fgcAcao']=="substitui"){
                    $before = "update movimento set categoria_id='{$_POST['fgcSubstitui']}' where categoria_id='{$_POST['fgcId']}';";
                    if(!(enviarComand($before,'bd_pctrl'))) header('Location: ../dashboard/index.php?gerenciarCategoria&fgcErr=0#divCategoria');
                }
                $sql = "delete from categoria where id='{$_POST['fgcId']}';";
                if(enviarComand($sql,'bd_pctrl')) header('Location: ../dashboard/index.php?gerenciarCategoria&fgcErr=1#divCategoria');
                else header('Location: ../dashboard/index.php?gerenciarCategoria&fgcErr=2#divCategoria');
            }else
            if($_POST['fgcAcao']=="editar"){
                $sql = "update categoria set nome='{$_POST['fgcNome']}' where id='{$_POST['fgcId']}';";
                if(enviarComand($sql,'bd_pctrl')) header('Location: ../dashboard/index.php?gerenciarCategoria&fgcErr=3#divCategoria');
                else header('Location: ../dashboard/index.php?gerenciarCategoria&fgcErr=4#divCategoria');
            }
        }
    }
    else{ header('Location: ../'); } //Caso nenhuma das opções tenha sido selecionada será redirecionada para a tela de login. 
}else{ header('Location: ../'); }
?>