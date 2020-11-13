<?php
    session_start();
    include('../../conn/function.php');
    if(!(isset($_SESSION['pctrl_user']))){ header('Location: ../index.php?msg=2'); }else{
        $sql = "select * from usuario where id={$_SESSION['pctrl_user']}";
        $data = enviarComand($sql,'bd_pctrl');
        $res = $data->fetch_assoc();
        if($res){ $_SESSION['nome'] = $res['nome']; $_SESSION['sobrenome'] = $res['sobrenome']; }
    }
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../img/arrow-icon.jpg" type="image">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="../../css/scroll.css" rel="stylesheet">
    <title>PCtrl - Cadastro em Tabela</title>
    <!-- Bootstrap core CSS -->
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="../dashboard/product.css" rel="stylesheet">
    <style>
        body{
            background: #eee;
            padding-top: 50px;
        }
        .bg-transparent{ background: transparent; }
        .bx-shadow{ box-shadow: 0px 0px 5px #001; }
        .btn-star{
            color: #ffc107;
            font-size: 15pt;
            margin:0px;
            padding: 8px;
        }
        .btn-star-sm{
            color: #ffc107;
            font-size: 11pt;
            margin:0px;
            padding: 8px;
        }
        .btn-star:hover{ color: #ffc107; text-shadow: 0px 0px 5px #ffc107; }
        .girar90{ transform: rotate(90deg); }
        .align-fecha{ vertical-align: -5px; }
        .cursor{ cursor: pointer; }
        .btn-back{ top: 1rem; transition: background,box-shadow .6s; }
        .btn-back:hover{ background: #ddd; box-shadow: 0 0 10px rgba(0,0,0,.3) }
    </style>
    <script src="../../jquery/jquery.js"></script>
    <script src="../back/mascara.js"></script>
    <script>
        function toggleStatus(elem,p){
            if($(p).val()==1){
                $(p).val(0);
                elem.removeClass('bg-success').addClass('bg-danger rounded-left');
                elem.children('span').html('clear');
                elem.parent().prev('select').hide();
            }else{
                $(p).val(1);
                elem.removeClass('bg-danger rounded-left').addClass('bg-success');
                elem.children('span').html('done');
                elem.parent().prev('select').show();
            }
        }
        function alterAll(elem){
            if(elem.attr('title')=="Categoria"){
                alert('Aqui');
            }
        }
        function msg(p){
            $('#modalMsgBody').html(p);
            $('#modalMsgAutoClick').click();
        }
        $(function(){
            $('.border-rs').each(function(index){
                if($(this).val()>0){
                    $(this).addClass('text-success');
                }
                else if($(this).val()<0){
                    $(this).addClass('text-danger');
                } 
            });
        <?php 
            if(isset($_GET['erro'])){
                if($_GET['erro']==0)
                    echo " msg('Formato de dados Incompatível. Certifique-se de que sua planilha está dentro dos padrões.'); ";
            }
        ?>
        });
    </script>
  </head>
  <body>
      <a href="../dashboard/" class="p-1 px-3 text-dark ml-3 mt-0 position-absolute border rounded btn-back">
        <span class="material-icons align-middle">arrow_back</span>  
      </a>
      <section class="mb-5">
        <!--Body-->
        <div class="mx-auto text-center" style="width: 80%;">
            <h2 class="display-5 pb-0 mb-0">Cadastro</h2>
            <p class="lead pt-0 mt-0">Multiplas Movimentações</p>
        </div>
        <div class="bg-light box-shadow mx-auto" style="width: 80%; border-radius: 21px 21px 0 0;">
            <div class="container-fluid p-0 pt-1">
                <div class="table-responsive">
                <!--Tabela Geral-->
                    <table class="table table-hover mb-1" id="tblGeral">
                        <thead>
                            <tr class="pt-0 mt-0">
                                <?php 
                                    $sql = "select * from categoria where usuario_id='{$_SESSION['pctrl_user']}';";
                                    $dtCat = enviarComand($sql,"bd_pctrl"); $iC = 0;
                                    while($resTemp = $dtCat->fetch_assoc()){ $resCat[$iC] = $resTemp; $iC++; }
    
                                    $sql = "select * from conta where usuario_id='{$_SESSION['pctrl_user']}';";
                                    $dtConta = enviarComand($sql,"bd_pctrl"); $iC = 0;
                                    while($resTemp = $dtConta->fetch_assoc()){ $resConta[$iC] = $resTemp; $iC++; }

                                    $msg = "<div class=\'bg-dark d-block rounded p-2\'>";
                                    $msg .= "<h5 class=\'text-light border-bottom pb-2 border-secondary\'>Descrição dos Campos</h5>";
                                    $msg .= "<span class=\'badge badge-light m-1 p-2\'>Data</span>";
                                    $msg .= "<span class=\'badge badge-light m-1 p-2\'>Descrição</span>";
                                    $msg .= "<span class=\'badge badge-light m-1 p-2\'>Valor</span><br/>";
                                    $msg .= "<span class=\'badge badge-light m-1 p-2\'>Categoria</span>";
                                    $msg .= "<span class=\'badge badge-light m-1 p-2\'>Relevância</span>";
                                    $msg .= "<span class=\'badge badge-light m-1 p-2\'>Status</span>";
                                    $msg .= "<span class=\'badge badge-light m-1 p-2\'>Obs</span></div>";

                                    $content = "<select class=\'custom-select m-1\' title=\'Categoria\' onchange=\'alterAll($(this));\'>";
                                    foreach($resCat as $value){ $content .= "<option value=\'".$value['id']."\'>".$value['nome']."</option>"; }
                                    $content .= "</select>";

                                    $content .= "<div class=\'input-group m-1\' title=\'Relevância\'>";
                                    $content .= "<input type=\'number\' class=\'form-control\' value=\'3\' min=\'1\' max=\'5\'>";
                                    $content .= "<div class=\'input-group-append\'>";
                                    $content .= "<div class=\'input-group-text text-warning border-0 bg-dark\'>&#9733</div>";
                                    $content .= "</div></div>";

                                    $content .= "<div class=\'input-group m-1\' title=\'Status\'>";
                                    $content .= "<select class=\'form-control\'>";
                                    foreach($resConta as $value)
                                    { $content .= "<option value=\'".$value['id']."\'>".$value['nome']."</option>"; }
                                    $content .= "</select>";
                                    $content .= "<div class=\'input-group-append\'>";
                                    $content .= "<div class=\'input-group-text text-light border-0 bg-success rounded-right cursor\'>";
                                    $content .= "<span class=\'material-icons\'>done</span>"; 
                                    $content .= "</div></div><input type=\'hidden\' value=\'1\'></div>";
                                ?>
                                <th scope="col" colspan="3" class="border-top-0 text-center pt-1 pb-2">
                                    <button type="button" class="btn btn-danger active font-weight-bold"
                                        onclick="msg('<?php echo $msg; ?>')">Tabela de Entrada
                                        <span class="badge badge-success">?</span></button>
                                    <button type="button" class="btn btn-dark active font-weight-bold"
                                        onclick="msg('<?php echo $content; ?>')">Alterar Todos</button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <form method="POST" action="../back/cadMult.php">
                            <?php
                                function separador($value){  return explode(';',$value); }
                                function reduz($value){
                                    $value = str_replace('COMPRA CARTAO MAESTRO','',$value);
                                    $date = date_create(str_replace('/','-',$value[0]));
                                    $value[0] = date_format($date,'Y-m-d');
                                    $value[1] = trim($value[1]);
                                    if(substr($value[1],2,1)=='/'){ $value[1] = substr($value[1],6); }
                                    $value[2] = trim(str_replace(',','.',$value[2]));
                                    return $value;
                                }

                                if(isset($_FILES['fileMov'])) $arr = file($_FILES['fileMov']['tmp_name']);
                                else header('Location: ../dashboard/index.php?errMult=-1');
                                $arr = array_reverse($arr);
                                for($i=0;$i<count($arr);$i++){
                                    if(strlen(trim(str_replace(';','',$arr[$i])))==0){ unset($arr[$i]); }
                                }
                                $arr = array_reverse($arr);
                                $arr = array_map("separador",$arr);
                                $arr = array_map("reduz",$arr);
                                
                                for($c=0;$c<count($arr);$c++){
                            ?>
                            <tr>
                                <td class="td-data text-nowrap my-0"  colspan="3">
                                    <div class="form-inline justify-content-center">
                                        <span class="badge badge-dark mr-1"><?php echo $c+1;?></span>
                                        <!-- DATA -->
                                        <input type="date" class="form-control m-1" title="Data"
                                            value="<?php echo $arr[$c][0];?>" name="data<?php echo $c; ?>">
                                        <!-- DESCRIÇÃO -->
                                        <input type="text" class="form-control m-1" title="Descrição"
                                            value="<?php echo $arr[$c][1];?>" name="descricao<?php echo $c; ?>">
                                        <!-- VALOR -->
                                        <input type="number" class="form-control border-rs m-1" title="Valor"
                                            value="<?php echo $arr[$c][2];?>" name="valor<?php echo $c; ?>">
                                    </div>
                                    <div class="border rounded">
                                        <div class="form-inline justify-content-center">
                                            <!-- CATEGORIA -->
                                            <select class="custom-select m-1" name="categoria<?php echo $c; ?>" title="Categoria">
                                                <?php foreach($resCat as $value){ ?>
                                                <option value="<?php echo $value['id']; ?>"><?php echo $value['nome']; ?></option>
                                                <?php } ?>
                                            </select>
                                            <!-- RELEVÂNCIA -->
                                            <div class="input-group m-1" title="Relevância">
                                                <input type="number" class="form-control"
                                                    value="3" min="1" max="5"  name="relevancia<?php echo $c; ?>">
                                                <div class="input-group-append">
                                                    <div class="input-group-text text-warning border-0 bg-dark">&#9733</div>
                                                </div>
                                            </div>
                                            <!-- CONTA/STATUS -->
                                            <div class="input-group m-1" title="Status">
                                                <select class="form-control" name="conta<?php echo $c; ?>">
                                                    <?php foreach($resConta as $value){?>
                                                    <option value="<?php echo $value['id']; ?>"><?php echo $value['nome']; ?></option>
                                                    <?php } ?>
                                                </select>
                                                <div class="input-group-append">
                                                    <div class="input-group-text text-light border-0 bg-success rounded-right cursor"
                                                        onclick="toggleStatus($(this),'#status<?php echo $c; ?>');">
                                                            <span class="material-icons">done</span>
                                                    </div>
                                                </div>
                                                <input type="hidden" value="1" name="status<?php echo $c; ?>" id="status<?php echo $c; ?>">
                                            </div>
                                            <button type="button" class="btn btn-primary m-1" title="Observação"
                                                onclick="$(this).parent().next().toggle()">Obs</button>
                                        </div>
                                        <div class="p-2" style="display:none">
                                            <textarea class="form-control" name="obs<?php echo $c; ?>"></textarea>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <th class="text-center pt-2 pb-1">
                                    <button type="submit" class="btn btn-success font-weight-bold px-5">Concluir</button>
                                </th>
                            </tr>
                        </form>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php 
            include('../modais/modalMsg.php');
            include('../../function/global.php');
        ?>
      </section>
      <!-- Bootstrap core JavaScript ================================================== -->
      <!-- Placed at the end of the document so the pages load faster -->
      <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
      <script>window.jQuery || document.write('<script src="../jquery-slim.min.js"><\/script>')</script>
      <script src="../popper.min.js"></script>
      <script src="../../js/bootstrap.min.js"></script>
      <script src="../dashboard/holder.min.js"></script>
      <script> Holder.addTheme('thumb', { bg: '#55595c', fg: '#eceeef', text: 'Thumbnail' }); </script>
  </body>
</html>