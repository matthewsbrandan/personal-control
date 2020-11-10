<?php
    session_start();
    include('../../conn/function.php');
    if(!(isset($_SESSION['pctrl_user']))){ header('Location: ../index.php?msg=2'); }else{
        $sql = "select * from usuario where id={$_SESSION['pctrl_user']}";
        $data = enviarComand($sql,'bd_pctrl');
        $res = $data->fetch_assoc();
        if($res){ $_SESSION['nome'] = $res['nome']; $_SESSION['sobrenome'] = $res['sobrenome']; }
    }
    $mesCalendar = array(null,'Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
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
    <title>PCtrl - 
        <?php
            if(isset($_GET['calendarM'])&&isset($_GET['calendarY'])) echo substr($mesCalendar[$_GET['calendarM']],0,3).'/'.$_GET['calendarY'];
            else echo substr($mesCalendar[intval(date('m'))],0,3).'/'.date('Y');
        ?>
    
    </title>
    <!-- Bootstrap core CSS -->
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="product.css" rel="stylesheet">
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
        $(function(){
           <?php if(!isset($_GET['calendarM'])||!isset($_GET['calendarY'])){ echo "altCalendar(); "; }; ?>
        });
        function altCalendar(){ $('#modalCalendarAutoClick').click(); }
        function altTable(){
            if($('#tblAgrupada').hasClass('d-none')){
                $('#movGroup').html('Agrupadas por Categoria');
                $('#tblAgrupada').removeClass('d-none');
                $('#tblGeral').addClass('d-none');
            }
            else{
                $('#movGroup').html('Movimentações');
                $('#tblAgrupada').addClass('d-none');
                $('#tblGeral').removeClass('d-none');
            }
        }
    </script>
  </head>
  <body>
      <a href="./" class="p-1 px-3 text-dark ml-3 mt-0 position-absolute border rounded btn-back">
        <span class="material-icons align-middle">arrow_back</span>  
      </a>
      <section class="mb-5">
        <!--Body-->
        <div class="mx-auto text-center" style="width: 80%;">
            <h2 class="display-5 pb-0 mb-0 cursor" onclick="altCalendar()">
                <?php
                    if(isset($_GET['calendarM'])&&isset($_GET['calendarY'])) echo $mesCalendar[$_GET['calendarM']].'/'.$_GET['calendarY'];
                    else echo $mesCalendar[intval(date('m'))].'/'.date('Y');
                ?>
            </h2>
            <p class="lead pt-0 mt-0 cursor" id="movGroup" onclick="altTable();">Movimentações</p>
        </div>
        <div class="bg-light box-shadow mx-auto" style="width: 80%; border-radius: 21px 21px 0 0;">
            <div class="container-fluid p-0 pt-1">
                <div class="table-responsive">
                <!--Tabela Geral-->
                <table class="table table-hover mb-1" id="tblGeral">
                    <thead>
                        <?php
                            $searchM = isset($_GET['calendarM'])?$_GET['calendarM']:'month(now())';
                            $searchY = isset($_GET['calendarY'])?$_GET['calendarY']:'year(now())';
                            if(!(isset($_GET['calendarM'])&&isset($_GET['calendarY']))) $mesSelect = date('Y-m').'-01';
                            else $mesSelect = $searchY.'-'.($searchM<10?'0'.$searchM:$searchM).'-01';
                            $sql = "select * from caixa where usuario_id='{$_SESSION['pctrl_user']}' and mesano<='$mesSelect' order by mesano desc limit 1;";
                            $dataFecha = enviarComand($sql,'bd_pctrl');
                            $resFecha = $dataFecha->fetch_assoc();
                            $testeMesCaixa = $resFecha['mesano'] == $mesSelect;
                        ?>
                        <tr class="pt-0 mt-0">
                            <th colspan="3" scope="col" class="border-top-0">
                                FECHAMENTO <i class="material-icons align-fecha">slow_motion_video</i>
                            </th>
                            <th class="border-top-0">Inicial Parcial:</th>
                                <th class="border-top-0 font-weight-normal" title="R$ <?php if($testeMesCaixa) echo number_format($resFecha['inicial_parcial'],2,',','.'); else echo number_format($resFecha['final_parcial'],2,',','.'); ?>">
                                    <?php
                                        if($testeMesCaixa) echo number_format($resFecha['inicial_parcial'],2,',','.');
                                        else echo number_format($resFecha['final_parcial'],2,',','.');
                                    ?>
                                </th>
                            <th class="border-top-0 text-right">Inicial:</th>
                                <th colspan="2" class="border-top-0 font-weight-normal text-right pr-5" title="R$ <?php if($testeMesCaixa) echo number_format($resFecha['inicial'],2,',','.'); else echo number_format($resFecha['final'],2,',','.'); ?>">
                                    <?php
                                        if($testeMesCaixa) echo number_format($resFecha['inicial'],2,',','.');
                                        else echo number_format($resFecha['final'],2,',','.');
                                    ?>
                                </th>
                        </tr>
                        <tr>
                            <th scope="col">Dia</th>
                            <th scope="col">Tipo</th>
                            <th scope="col" class="text-nowrap text-center">R$</th>
                            <th scope="col">Descrição</th>
                            <th scope="col">Categoria</th>
                            <th scope="col" class="text-center">Parcela</th>
                            <th scope="col" class="text-center">Relevância</th>
                            <th scope="col" class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            //Nomear especificamente cada coluna do banco que será utilizada
                            $sql="select m.id,data_,tipo,valor,descricao,c.nome,parcela,relevancia,obs,m.status from movimento m inner join categoria c on m.categoria_id=c.id where m.usuario_id='{$_SESSION['pctrl_user']}' and month(data_)=$searchM and year(data_)=$searchY order by data_;";
                            $dataPende = enviarComand($sql,'bd_pctrl'); $entrou = 0;
                            while($linePende=$dataPende->fetch_assoc()){ $entrou ++;
                        ?>
                        <tr>
                            <td class="td-data text-nowrap my-0" title="<?php echo date('d/m/Y',strtotime($linePende['data_'])); ?>"><?php echo date('d',strtotime($linePende['data_'])); ?></td>
                            <td class="td-tipo text-nowrap my-0" title="<?php echo $linePende['tipo']=='Receita'?'Entrada':'Saída'; ?>">
                                <?php echo $linePende['tipo']; ?>
                            </td>
                            <td class="td-valor text-nowrap my-0 text-center" title="R$ <?php echo number_format($linePende['valor'],2,',','.'); ?>"><?php echo number_format($linePende['valor'],2,',','.'); ?></td>
                            <td class="td-descricao text-truncate my-0" title="<?php echo $linePende['descricao']; ?>">
                                <?php echo $linePende['descricao']; ?>
                            </td>
                            <td class="td-categoria text-truncate my-0" title="<?php echo $linePende['nome']; ?>"><?php echo $linePende['nome']; ?></td>
                            <td class="td-parcela text-truncate my-0 text-center" title="<?php echo $linePende['parcela']?$linePende['parcela']:'Não é Parcelado'; ?>">
                                <?php echo $linePende['parcela']?$linePende['parcela']:'-'; ?>
                            </td>
                            <td class="td-relevancia text-truncate my-0" title="Relevância <?php echo $linePende['relevancia']; ?>">
                                <div class="d-block mx-auto rounded text-center text-warning">
                                    <?php
                                        echo $linePende['relevancia']>=1?"&#9733":"&#9734";
                                        echo $linePende['relevancia']>=2?"&#9733":"&#9734";
                                        echo $linePende['relevancia']>=3?"&#9733":"&#9734";
                                        echo $linePende['relevancia']>=4?"&#9733":"&#9734";
                                        echo $linePende['relevancia']>=5?"&#9733":"&#9734";
                                    ?>                   
                                </div>
                            </td>
                            <td class="td-status text-center">
                                <i class="material-icons <?php if($linePende['status']) echo $linePende['tipo']=='Receita'?'text-success':'text-danger'; ?>" title="<?php echo $linePende['status']?'Efetuado':'Pendente';?>">
                                    <?php echo $linePende['status']?'money_off':'more_horiz'; ?>
                                </i>
                            </td>
                        </tr>
                        <?php } if($entrou==0) { ?>
                        <tr>
                            <td colspan="8" class="py-5">
                                <div class="pt-2 pb-5 text-center text-muted">
                                    Sem Movimento neste Mês<br/>
                                    <small>Adicione novas Movimentações clicando no botão acima.</small>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <th colspan="3">FECHAMENTO <i class="material-icons align-fecha">slow_motion_video</i></th>
                            <th>Final Parcial:</th>
                                <td title="R$ <?php echo number_format($resFecha['final_parcial'],2,',','.'); ?>">
                                    <?php echo number_format($resFecha['final_parcial'],2,',','.'); ?>
                                </td>
                            <th class="text-right">Final:</th>
                                <td colspan="2" class="text-right pr-5" title="R$ <?php echo number_format($resFecha['final'],2,',','.'); ?>">
                                    <?php echo number_format($resFecha['final'],2,',','.'); ?>
                                </td>
                        </tr>
                    </tbody>
                </table>
                <!--Tabela Agrupada-->
                <table class="table table-dark table-hover mb-1 d-none rounded-top" id="tblAgrupada">
                    <thead>
                        <?php
                            $searchM = isset($_GET['calendarM'])?$_GET['calendarM']:'month(now())';
                            $searchY = isset($_GET['calendarY'])?$_GET['calendarY']:'year(now())';
                            if(!(isset($_GET['calendarM'])&&isset($_GET['calendarY']))) $mesSelect = date('Y-m').'-01';
                            else $mesSelect = $searchY.'-'.($searchM<10?'0'.$searchM:$searchM).'-01';
                            $sql = "select * from caixa where usuario_id='{$_SESSION['pctrl_user']}' and mesano<='$mesSelect' order by mesano desc limit 1;";
                            $dataFecha = enviarComand($sql,'bd_pctrl');
                            $resFecha = $dataFecha->fetch_assoc();
                            $testeMesCaixa = $resFecha['mesano'] == $mesSelect;
                        ?>
                        <!--Fechamento-->
                        <tr class="pt-0 mt-0">
                            <th scope="col" class="border-top-0">
                                FECHAMENTO <i class="material-icons align-fecha">slow_motion_video</i>
                            </th>
                            <th class="border-top-0">Inicial Parcial:
                                <span class="pl-3 font-weight-normal" title="R$ <?php if($testeMesCaixa) echo number_format($resFecha['inicial_parcial'],2,',','.'); else echo number_format($resFecha['final_parcial'],2,',','.'); ?>">
                                    <?php
                                        if($testeMesCaixa) echo number_format($resFecha['inicial_parcial'],2,',','.');
                                        else echo number_format($resFecha['final_parcial'],2,',','.');
                                    ?>
                                </span>
                            </th>
                            <th class="border-top-0 text-right">Inicial:
                                <span colspan="2" class="pl-3 font-weight-normal text-right pr-3" title="R$ <?php if($testeMesCaixa) echo number_format($resFecha['inicial'],2,',','.'); else echo number_format($resFecha['final'],2,',','.'); ?>">
                                    <?php
                                        if($testeMesCaixa) echo number_format($resFecha['inicial'],2,',','.');
                                        else echo number_format($resFecha['final'],2,',','.');
                                    ?>
                                </span>
                            </th>
                        </tr>
                        <!--Header-->
                        <tr>
                            <th scope="col">Categoria</th>                            
                            <th scope="col">Relevância</th>
                            <th scope="col" class="text-nowrap text-right"><span class="pr-3">R$(Reais)</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            //Nomear especificamente cada coluna do banco que será utilizada
                            $sql="select c.nome nome,round(avg(relevancia)) relevancia,sum(valor) valor from movimento m inner join categoria c on m.categoria_id=c.id where m.usuario_id='{$_SESSION['pctrl_user']}' and month(data_)=$searchM and year(data_)=$searchY and tipo='Receita' group by categoria_id;";
                            $dataPende = enviarComand($sql,'bd_pctrl'); $entrou = 0;
                            while($linePende=$dataPende->fetch_assoc()){ $entrou ++;
                        ?>
                        <tr>
                            <td class="td-categoria text-truncate my-0" title="<?php echo $linePende['nome']; ?>"><?php echo $linePende['nome']; ?></td>
                            <td class="td-relevancia text-truncate my-0" title="Relevância <?php echo $linePende['relevancia']; ?>">
                                <div class="d-block mx-auto rounded text-warning">
                                    <?php
                                        echo $linePende['relevancia']>=1?"&#9733":"&#9734";
                                        echo $linePende['relevancia']>=2?"&#9733":"&#9734";
                                        echo $linePende['relevancia']>=3?"&#9733":"&#9734";
                                        echo $linePende['relevancia']>=4?"&#9733":"&#9734";
                                        echo $linePende['relevancia']>=5?"&#9733":"&#9734";
                                    ?>                   
                                </div>
                            </td>
                            <td class="td-valor text-nowrap my-0 text-right" title="R$ <?php echo number_format($linePende['valor'],2,',','.'); ?>"><span class="pr-3"><?php echo number_format($linePende['valor'],2,',','.'); ?></span></td>
                        </tr>
                        <?php } if($entrou==0) { ?>
                        <tr>
                            <td colspan="3" class="py-5">
                                <div class="pt-2 pb-5 text-center text-muted">Sem Receitas neste Mês</div>
                            </td>
                        </tr>
                        <?php } ?>
                        <!--Agrupado p/ Receita-->
                        <tr>
                            <th>Receita <span class="font-weight-normal">(Agrupado por Categoria)</span></th>
                            <th>Pendente:
                                <span class="pl-3 font-weight-normal">
                                <?php
                                    $sql="select sum(valor) valor from movimento where usuario_id='{$_SESSION['pctrl_user']}' and month(data_)=$searchM and year(data_)=$searchY and tipo='Receita' and status=0;";
                                    echo number_format(((enviarComand($sql,'bd_pctrl'))->fetch_assoc())['valor'],2,',','.');
                                ?>
                                </span>
                            </th>
                            <th class="text-right">Total:
                                <span class="pl-3 font-weight-normal pr-3">
                                <?php
                                    $sql="select sum(valor) valor from movimento where usuario_id='{$_SESSION['pctrl_user']}' and month(data_)=$searchM and year(data_)=$searchY and tipo='Receita';";
                                    echo number_format(((enviarComand($sql,'bd_pctrl'))->fetch_assoc())['valor'],2,',','.');
                                ?>
                                </span>
                            </th>
                        </tr>
                        <?php
                            //Nomear especificamente cada coluna do banco que será utilizada
                            $sql="select c.nome nome,round(avg(relevancia)) relevancia,sum(valor) valor from movimento m inner join categoria c on m.categoria_id=c.id where m.usuario_id='{$_SESSION['pctrl_user']}' and month(data_)=$searchM and year(data_)=$searchY and tipo='Despesa' group by categoria_id;";
                            $dataPende = enviarComand($sql,'bd_pctrl'); $entrou = 0;
                            while($linePende=$dataPende->fetch_assoc()){ $entrou ++;
                        ?>
                        <tr>
                            <td class="td-categoria text-truncate my-0" title="<?php echo $linePende['nome']; ?>"><?php echo $linePende['nome']; ?></td>
                            <td class="td-relevancia text-truncate my-0" title="Relevância <?php echo $linePende['relevancia']; ?>">
                                <div class="d-block mx-auto rounded text-warning">
                                    <?php
                                        echo $linePende['relevancia']>=1?"&#9733":"&#9734";
                                        echo $linePende['relevancia']>=2?"&#9733":"&#9734";
                                        echo $linePende['relevancia']>=3?"&#9733":"&#9734";
                                        echo $linePende['relevancia']>=4?"&#9733":"&#9734";
                                        echo $linePende['relevancia']>=5?"&#9733":"&#9734";
                                    ?>                   
                                </div>
                            </td>
                            <td class="td-valor text-nowrap my-0 text-right" title="R$ <?php echo number_format($linePende['valor'],2,',','.'); ?>"><span class="pr-3"><?php echo number_format($linePende['valor'],2,',','.'); ?></span></td>
                        </tr>
                        <?php } if($entrou==0) { ?>
                        <tr>
                            <td colspan="3" class="py-5">
                                <div class="pt-2 pb-5 text-center text-muted">Sem Receitas neste Mês</div>
                            </td>
                        </tr>
                        <?php } ?>
                        <!--Agrupado p/ Despesa-->
                        <tr>
                            <th>Despesa <span class="font-weight-normal">(Agrupado por Categoria)</span></th>
                            <th>Pendente:
                                <span class="pl-3 font-weight-normal">
                                    <?php
                                    $sql="select sum(valor) valor from movimento where usuario_id='{$_SESSION['pctrl_user']}' and month(data_)=$searchM and year(data_)=$searchY and tipo='Despesa' and status=0";
                                    echo number_format(((enviarComand($sql,'bd_pctrl'))->fetch_assoc())['valor'],2,',','.');
                                ?>
                                </span>
                            </th>
                            <th class="text-right">Total:
                                <span class="pl-3 font-weight-normal pr-3">
                                <?php
                                    $sql="select sum(valor) valor from movimento where usuario_id='{$_SESSION['pctrl_user']}' and month(data_)=$searchM and year(data_)=$searchY and tipo='Despesa'";
                                    echo number_format(((enviarComand($sql,'bd_pctrl'))->fetch_assoc())['valor'],2,',','.');
                                ?>
                                </span>
                            </th>
                        </tr>
                        <!--Fechamento-->
                        <tr>
                            <th>FECHAMENTO <i class="material-icons align-fecha">slow_motion_video</i></th>
                            <th>Final Parcial:
                                <span title="R$ <?php echo number_format($resFecha['final_parcial'],2,',','.'); ?>" class="pl-3 font-weight-normal">
                                    <?php echo number_format($resFecha['final_parcial'],2,',','.'); ?>
                                </span>
                            </th>
                            <th class="text-right">Final:
                                <span colspan="2" class="pl-3 font-weight-normal text-right pr-3" title="R$ <?php echo number_format($resFecha['final'],2,',','.'); ?>">
                                    <?php echo number_format($resFecha['final'],2,',','.'); ?>
                                </span>
                            </th>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
          </div>
        <?php 
            include('../modais/modalCalendar.php');
            include('../../function/ctrlm.php');
            include('../../function/mnav.php');
            include('../../function/arty.php');
            include('../../function/wmatth.php');
        ?>
      </section>
      <!-- Bootstrap core JavaScript ================================================== -->
      <!-- Placed at the end of the document so the pages load faster -->
      <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
      <script>window.jQuery || document.write('<script src="../jquery-slim.min.js"><\/script>')</script>
      <script src="../popper.min.js"></script>
      <script src="../../js/bootstrap.min.js"></script>
      <script src="holder.min.js"></script>
      <script> Holder.addTheme('thumb', { bg: '#55595c', fg: '#eceeef', text: 'Thumbnail' }); </script>
  </body>
</html>