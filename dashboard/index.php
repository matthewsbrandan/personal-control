<?php
    session_start();
    include('../../conn/function.php');
    if(!(isset($_SESSION['user_mtworld'])&&$_SESSION['user_mtworld']>0)){
        if(isset($_COOKIE['mtworldPass'])&&isset($_COOKIE['mtworldKey'])){
          $sql="select * from usuario where email='{$_COOKIE['mtworldPass']}' and senha='{$_COOKIE['mtworldKey']}';";
          if($linha = (enviarComand($sql,'bd_mtworld'))->fetch_assoc()){
            $_SESSION['user_mtworld'] = $linha['id'];
            $_SESSION['user_mtworld_nome'] = $linha['nome'];
            $_SESSION['user_mtworld_email'] = $linha['email'];
          } 
        }
    }
    if(!(isset($_SESSION['pctrl_user']))){ header('Location: ../index.php?msg=2'); }else{
        $sql = "select * from usuario where id={$_SESSION['pctrl_user']}";
        $data = enviarComand($sql,'bd_pctrl');
        $res = $data->fetch_assoc();
        if($res){ $_SESSION['nome'] = $res['nome']; $_SESSION['sobrenome'] = $res['sobrenome']; }
    }
    $mesCalendar = array(null,'Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');
    function mesCalc($p){
        $retorno = '';
        if($p==(-1)){
            if(isset($_GET['calendarM'])){
                if($_GET['calendarM']<=1)
                    $retorno = 'calendarM=12&calendarY='.(isset($_GET['calendarY'])?($_GET['calendarY']-1):(date('Y')-1));
                else
                    $retorno = 'calendarM='.($_GET['calendarM']-1).'&calendarY='.(isset($_GET['calendarY'])?$_GET['calendarY']:date('Y'));
            }else{   
                if(intval(date('m'))<=1)
                    $retorno = 'calendarM=12&calendarY='.(isset($_GET['calendarY'])?($_GET['calendarY']-1):(date('Y')-1));
                else
                    $retorno = 'calendarM='.(intval(date('m'))-1).'&calendarY='.(isset($_GET['calendarY'])?($_GET['calendarY']):(date('Y')));
            }
        }
        else if($p==(1)){
            if(isset($_GET['calendarM'])){
                if($_GET['calendarM']>=12)
                    $retorno = 'calendarM=1&calendarY='.(isset($_GET['calendarY'])?($_GET['calendarY']+1):(date('Y')+1));
                else
                    $retorno = 'calendarM='.($_GET['calendarM']+1).'&calendarY='.(isset($_GET['calendarY'])?($_GET['calendarY']):(date('Y')));
            }else{   
                if(intval(date('m'))>=12)
                    $retorno = 'calendarM=1&calendarY='.(isset($_GET['calendarY'])?($_GET['calendarY']+1):(date('Y')+1));
                else
                    $retorno = 'calendarM='.(intval(date('m'))+1).'&calendarY='.(isset($_GET['calendarY'])?($_GET['calendarY']):(date('Y')));
            }
        }
        return $retorno;
    }
    function mesGet($caracter){
        if(isset($_GET['exp'])){ $retorno = $caracter.'exp'; $caracter = "&"; }else{ $retorno = ""; }
        if(!(isset($_GET['calendarM'])||isset($_GET['calendarY']))) $caracter = '';
        $retorno.= $caracter.(isset($_GET['calendarM'])?'calendarM='.$_GET['calendarM']:'').(isset($_GET['calendarY'])?'&calendarY='.$_GET['calendarY']:''); 
        return $retorno;
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
    <title>PCtrl - Painel de Controle</title>
    <!-- Bootstrap core CSS -->
    <link href="../../css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="product.css" rel="stylesheet">
    <style>
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
        .align-calendar{ vertical-align: -3.5px; }
        .pointer{ cursor: pointer; }
        [onclick],[data-toggle='modal'],[href]{ cursor: pointer; }
        .modal ::-webkit-scrollbar { width: 5px; background: transparent; border-radius: 5px; }
        .modal ::-webkit-scrollbar-track { background:  transparent; border-radius: 5px; } /* Track */
        .modal ::-webkit-scrollbar-thumb { background: #aaa; border-radius: 5px; } /* Handle */
        .modal ::-webkit-scrollbar-thumb:hover { background: #999; } /* Handle on hover */    
    </style>
    <script src="../../jquery/jquery.js"></script>
    <script src="../back/mascara.js"></script>
    <!--Arrays - Msg | Functions-->
    <script>
        var arrCategoriaMsg = new Array("","","","Não é possível cadastrar uma Categoria com este nome!<br><em>Outros, Transferência e Margem de Erro</em> já são categorias padrões do PCtrl, então não podem ser nem adicionadas novamente, nem excluídas.","Preencha o nome da Categoria!");
        var arrReset = new Array('Usuário Reiniciado!','Não foi possível restaurar seu Usuário','Apenas os Objetivos foram Apagados','Objetivos e Movimentações Apagados.','Objetivos, Movimentações e Parcelas Apagadas');
        var arrApagar = new Array('Deletado com Sucesso!','Erro ao Excluir Movimentação!');
        var arrUpdate = new Array('Movimentação Atualizada com Sucesso!','Houve um erro ao alterar a data!','Houve um erro ao alterar a Movimentação!');
        var arrMeta = new Array('Meta Alterada com Sucesso!','Houve um erro ao Alterar a Meta!');
        var arrFmm = new Array('Movimentações Finalizadas com Sucesso!','Nenhuma Movimentação Selecionada!','Selecione a conta que realizara as Movimentações!','Houve um erro ao realizar o Pagamento!');
        var arrFgc = new Array("Não foi possível transferir as Categorias, tente novamente!","Categoria Deletada com Sucesso!","Houve um erro ao tentar Excluir esta Categoria!","Categoria Alterada com Sucesso!","Houve um erro ao tentar Alterar esta Categoria");
        $(function(){
            <?php if(isset($_GET['reset'])){ ?> msg(<?php echo $_GET['reset']; ?>,arrReset); <?php } ?>
            <?php if(isset($_GET['cat'])&&$_GET['cat']>2){ ?> msg(<?php echo $_GET['cat']; ?>,arrCategoriaMsg); <?php } ?>
            <?php if(isset($_GET['apagar'])){ ?> msg(<?php echo $_GET['apagar']; ?>,arrApagar); <?php } ?>
            <?php if(isset($_GET['update'])){ ?> msg(<?php echo $_GET['update']; ?>,arrUpdate); <?php } ?>
            <?php if(isset($_GET['meta'])){ ?> msg(<?php echo $_GET['meta']; ?>,arrMeta); <?php } ?>
            <?php if(isset($_GET['errFmm'])){ ?> msg(<?php echo $_GET['errFmm']; ?>,arrFmm); <?php } ?>
            <?php if(isset($_GET['fgcErr'])){ ?> msg(<?php echo $_GET['fgcErr']; ?>,arrFgc); <?php } ?>
            <?php if(isset($_GET['errMult'])){ ?> msg(0,[<?php if($_GET['errMult']==0)
                { echo "'Houve um erro ao recalcular Saldo e Fechamento de Caixa!'"; }else if($_GET['errMult']==-1)
                { echo "'Houve um erro, dados não foram enviados no padrão solicitado!'"; }else
                { echo "'<span class=\'text-danger font-weight-bold\'>{$_GET['errMult']}</span> ";
                  if($_GET['errMult']==1) echo "Movimentação não pode ser realizada!'";
                  else echo "Movimentações não puderam ser realizadas!'";
                }?>]); <?php } ?>
            $('#modalIntable').modal('show');
        });
        function msg(p,arr){
            if(arr[p]){
                $('#modalMsgBody').html(arr[p]);
                $('#modalMsgAutoClick').click();
            }
        }
    </script>
    <script>
        $(function(){
            <?php if((isset($_GET['cat'])&&$_GET['cat']<3)||isset($_GET['cont'])||isset($_GET['mov'])){ ?>
                $('#aMovimento').click(); <?php } ?> 
            <?php if(isset($_GET['obj'])){ ?> $('#aObjetivo').click(); <?php } ?>
            <?php if(isset($_GET['gerenciar'])){ ?> $('#btnChamaGerenciar').click(); <?php } ?>
            <?php if(isset($_GET['parc'])||isset($_GET['categ'])){ ?>
                $('#btnChamaMult').click(); formatModalMult('<?php echo isset($_GET['parc'])?'Parcela':'Categoria'; ?>');
            <?php } ?>
            <?php if(isset($_GET['exp'])){ echo " expand(); "; } ?>
            <?php if(isset($_GET['gerenciarCategoria'])){ echo " $('#btnGerenciarCategoria').click(); "; } ?>
            tableOmite("Pendente");
            tableOmite("Movimento");
            tableOmite("Categoria");
            tableOmite("Objetivo");
            tableOmite("Parcela");
        });
        function formatMoney(v){
            return parseFloat(v).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        }
        function tipoMov(p){
            $(".btn-mov").removeClass("active");
            $("#mov"+p).addClass("active");
            $("#movTipo").val(p);
            $("#divTransferencia").hide();
            $("#divDescricao").show();
            $("#divSelectCategoria").show();
            $("#btnParcelarMov").show();
            $("#btnStatus").removeClass('btn-danger'); alternaStatus();
            switch(p){
                case "Receita": $("#card-mov").css("border-color","rgba(40, 167, 69,.4)"); break;
                case "Despesa": $("#card-mov").css("border-color","rgba(220, 53, 69,.4)"); break;
                case "Transferencia":
                    $("#card-mov").css("border-color","rgba(0, 123, 255,.4)");
                    $("#divTransferencia").show('slow');
                    $("#divDescricao").hide();
                    $("#divSelectCategoria").hide();
                    $("#btnParcelarMov").hide();
                    $("#movDescricao").html('Transferência');
                    alternaStatus();
                    if(!$('#card-parc').hasClass('d-none')) $('#card-parc').addClass('d-none');
                    break;
            }
        }
        function alternaParcela(){
            if($('#card-parc').hasClass('d-none')){ 
                $('#card-parc').removeClass('d-none');
                $('#movTemParcela').val('true');
                $('#form-mov').attr('action','../back/cadMov.php?parcela');
                calcTotalParc();
            } 
            else{
                $('#card-parc').addClass('d-none');
                $('#movTemParcela').val('false');
                $('#form-mov').attr('action','../back/cadMov.php?movimento');
            } 
            $('#btnParcelarMov').toggle('slow');
        }
        function calcTotalParc(){
            if($('#movTemParcela').val()=="true"){
                v = (($('#movValor').val().replace('.','')).replace(',','.'))*$('#parcQtd').val(); 
                $('#parcTotal').val((v.toFixed(2)).replace('.',','));
            }
        }
        function alternaObs(){ $('#divObs').toggle('slow'); }
        function alternaStatus(){
            if($('#btnStatus').hasClass('btn-danger')){
                $('#movStatus').val(true);
                $('#btnStatus').removeClass('btn-danger');
                $('#btnStatus').addClass('btn-success');
                $('#btnStatus').html('Efetivar Automaticamente!');
                if($('#divTransferencia').css("display")=="none")
                { if($('#divSelectConta').hasClass('d-none')) $('#divSelectConta').removeClass('d-none'); }
            }else{
                $('#movStatus').val(false);
                $('#btnStatus').removeClass('btn-success');
                $('#btnStatus').addClass('btn-danger');
                $('#btnStatus').html('Status Pendente!');
                if(!$('#divSelectConta').hasClass('d-none')) $('#divSelectConta').addClass('d-none');
            }
        }
        function regulaRel(p,acao){
            $('#'+acao+'Relevancia').val(p);
            switch(p){
                case 1: $('#'+acao+'Rel1').html('&#9733'); $('#'+acao+'Rel2').html('&#9734'); $('#'+acao+'Rel3').html('&#9734'); $('#'+acao+'Rel4').html('&#9734'); $('#'+acao+'Rel5').html('&#9734'); break;
                case 2: $('#'+acao+'Rel1').html('&#9733'); $('#'+acao+'Rel2').html('&#9733'); $('#'+acao+'Rel3').html('&#9734'); $('#'+acao+'Rel4').html('&#9734'); $('#'+acao+'Rel5').html('&#9734'); break;
                case 3: $('#'+acao+'Rel1').html('&#9733'); $('#'+acao+'Rel2').html('&#9733'); $('#'+acao+'Rel3').html('&#9733'); $('#'+acao+'Rel4').html('&#9734'); $('#'+acao+'Rel5').html('&#9734'); break;                    
                case 4: $('#'+acao+'Rel1').html('&#9733'); $('#'+acao+'Rel2').html('&#9733'); $('#'+acao+'Rel3').html('&#9733'); $('#'+acao+'Rel4').html('&#9733'); $('#'+acao+'Rel5').html('&#9734'); break;
                case 5: $('#'+acao+'Rel1').html('&#9733'); $('#'+acao+'Rel2').html('&#9733'); $('#'+acao+'Rel3').html('&#9733'); $('#'+acao+'Rel4').html('&#9733'); $('#'+acao+'Rel5').html('&#9733'); break;
            }
        }
        function altSaldo(){
            $('#contasSaldo p').addClass('d-none');
            v = $('#selectConta').val();
            $('#saldo'+v).removeClass('d-none');
        }
        function allWrap(expande){
            if(expande){ $('.expandir-row-0').addClass('d-none d-md-none');   $('.recolher-row-0').removeClass('d-none d-md-none'); }
            else       { $('.expandir-row-0').removeClass('d-none d-md-none');$('.recolher-row-0').addClass('d-none d-md-none');    }
            altWrap(1,expande);
            altWrap(2,expande);
            altWrap(3,expande);
            altWrap(4,expande);
        }
        function altWrap(p,expande){
            if(expande){
                $('#divRow'+p).removeClass();
                $('#divRow'+p+'a').addClass("d-md-flex flex-md-equal w-100 my-md-3 pl-md-3");
                $('#divRow'+p+'b').addClass("d-md-flex flex-md-equal w-100 my-md-3 pl-md-3");
                $('.expandir-row-'+p).addClass('d-none'); $('.expandir-row-'+p).removeClass('d-md-inline-block');
                $('.recolher-row-'+p).removeClass('d-none d-md-none');
            }else{
                $('#divRow'+p+'a').removeClass();
                $('#divRow'+p+'b').removeClass();
                $('#divRow'+p).addClass('d-md-flex flex-md-equal w-100 my-md-3 pl-md-3');
                $('.expandir-row-'+p).removeClass('d-none d-md-none');
                $('.recolher-row-'+p).addClass('d-none d-md-none');
            }
        }
        function tableIntercala(nav,i){
            v = $('#div'+nav+' table tbody tr').length;
            $('#div'+nav+' table tbody tr').hide();
            for(cont=((i*4)-3);cont<=(i*4);cont++){ $('#div'+nav+' table tbody tr:nth-child('+cont+')').show(); }
            $('#nav'+nav+' li').removeClass('active');
            $('#nav'+nav+'-'+i).addClass('active');
        }
        function tableOmite(p){            
            v = $('#div'+p+' table tbody tr').length;
            if(v>4){
                for(i=5;i<=v;i++){
                    $('#div'+p+' table tbody tr:nth-child('+i+')').hide();
                }
                if(v>8){
                    qtd = Math.ceil(v/4);
                    if(qtd>2){
                        for(i=3;i<=qtd;i++){
                            ap = "<li class='page-item text-primary' id='nav"+p+"-"+i+"' onclick=\"tableIntercala('"+p+"',"+i+")\"><a class='page-link'>"+i+"</a></li>";
                            $('#nav'+p+' ul').append(ap);
                        }
                    }
                }
                $('#nav'+p).removeClass('d-none');
                $('#nav'+p+' ul').append("<li class='page-item disabled' id='nav"+p+"Qtd'><a class='page-link bg-transparent border-0' href='#'> (4 - "+v+") </a></li>");
            }
            
        }
        function formatModalMult(p){
            if(p=="Categoria"){
                $('#multCatQtd .text-muted').html($('.card-mult-cat').length);
                $('#multCatAcumulado span:first-child').html(formatMoney($('#multTotalR').val()));
                $('#multCatAcumulado span:last-child').html(formatMoney($('#multTotalD').val()));
                regulaRel(Math.round($('#multRelevancia').val()/$('.card-mult-cat').length),'mcat');
            }else
            if(p=="Parcela"){
                regulaRel(Math.round($('#multRelevanciaP').val()/$('#multQtdParcela').html()),'mparc');
            }
        }
        function revela(p){
            if($('#'+p).is(":checked")) $('.'+p).removeClass('d-none');
            else $('.'+p).addClass('d-none');
        }
        function checkRevela(p){
            if(p=="pend" || p=="mov"){
                var arr = [p+'-tipo',p+'-cat',p+'-parc',p+'-rel'];
                for(var i=0;i<arr.length;i++){
                    $('#'+arr[i]).attr("checked",$('#'+p+'-all').is(":checked"));
                    revela(arr[i]);
                }
            }
        }
        function expand(){
            allWrap(true);
            var arr = ['pend-tipo','pend-cat','pend-parc','pend-rel','mov-tipo','mov-cat','mov-parc','mov-rel'];
            for(var i=0;i<arr.length;i++){
                $('#'+arr[i]).attr("checked",true);
                revela(arr[i]);
            }
            
        }
        function formatDetalhe(c,p){
            local = '#div'+p+' tbody tr:nth-child('+c+')';
            /*Id*/ $('#dtId').val($('#id'+p+c).val());
            /*Data*/ $('#dtData').val($(local+' .td-data').attr('title'));
            /*Tipo*/
            $('#btnDtTipo').removeClass('btn-success btn-danger'); $('#dtTipo').val($(local+' .td-tipo').html().trim());
            $('#btnDtTipo').html($(local+' .td-tipo').html()).addClass($(local+' .td-tipo').html()=='Receita'?'btn-success':'btn-danger');
            /*Valor*/ $('#dtValor').val($(local+' .td-valor').html());
            /*Descricao*/ 
            $('#dtDescricao').val($(local+' .td-descricao').attr('title'));
            $('#modalDetalhe .modal-title').html("Detalhe > "+$(local+' .td-descricao').attr('title'));
            /*Categoria*/ $("#dtCategoria").val($('#dtCategoria option:contains("'+$(local+' .td-categoria').attr('title')+'")').val());
            /*Parcela*/
            if(($(local+' .td-parcela').html()).indexOf('-')==(-1)){
                $('#dtParcela').html($(local+' .td-parcela').html());
                $('#divDtParcela').removeClass('d-none');
            }
            else $('#divDtParcela').addClass('d-none');
            /*Relevancia*/ regulaRel(parseInt($(local+' .td-relevancia').attr('title').substr(11)),'dt');                                  
            /*Obs*/
            if(!(!$(local+' .td-obs').html()||$(local+' .td-obs').html()=="null")){
                $('#divDtObs').removeClass('d-none');
                $('#dtObs').val($(local+' .td-obs').html());
            }else $('#divDtObs').addClass('d-none');
            /*Status*/ $('#btnDtEfetuar').html($(local+' .td-tipo').html()=='Receita'?'Receber':'Pagar');
            if(p=='Pendente') $('#dtStatus').val('Pendente');
            else $('#dtStatus').val($(local+' .td-descricao').html().includes("money_off")?'Finalizado':'Pendente');
            if($('#dtStatus').val()=="Finalizado") $('#btnDtEfetuar').addClass('d-none');
            else $('#btnDtEfetuar').removeClass('d-none');
        }
        function formatPg(c,p){
            if(p=="Detalhe"){
                valor = $('#dtValor').val();
                id="#dtId";
                $('#pagarDiv').val('divMovimento');
            }
            else{ valor = $('#div'+p+' tbody tr:nth-child('+c+') .td-valor').html(); id='#idPendente'+c; }
            $('#modalPagar .modal-title').html((valor.replace('.','')).replace(',','.')>0?'Receber':'Pagar');
            $('#pagarValor').removeClass('text-danger text-success');
            $('#pagarValor').html(valor).addClass('text-'+((valor.replace('.','')).replace(',','.')>0?'success':'danger'));
            $('#pagarId').val($(id).val());
        }
        function direcionaConfirmacao(p){
            if(p=='reset'){
                $('#modalFormConfirma').attr('action','../back/cadMov.php?'+p);
                $('#modalCardConfirma p').html('Todos os Objetivos, Movimentações e Fechamentos de Caixas serão excluidos. Categorias e Contas são excluidos apenas manualmente.');
            }
        }
        function reload(){ location.reload(); }
    </script>
  </head>
  <body>
    <!--Header Pag-->
    <nav class="site-header sticky-top py-1">
      <div class="container d-flex flex-column flex-md-row justify-content-between">
        <a class="py-2 d-none d-md-inline-block" href="#" id="aMovimento" data-toggle="modal" data-target="#modalMovimento">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="d-block mx-auto"><circle cx="12" cy="12" r="10"></circle><line x1="14.31" y1="8" x2="20.05" y2="17.94"></line><line x1="9.69" y1="8" x2="21.17" y2="8"></line><line x1="7.38" y1="12" x2="13.12" y2="2.06"></line><line x1="9.69" y1="16" x2="3.95" y2="6.06"></line><line x1="14.31" y1="16" x2="2.83" y2="16"></line><line x1="16.62" y1="12" x2="10.88" y2="21.94"></line></svg>
        </a>
        <a class="py-2 d-none d-md-inline-block" href="index.php<?php echo mesGet('?'); ?>#divPendente">Pendências</a>
        <a class="py-2 d-none d-md-inline-block" href="index.php<?php echo mesGet('?'); ?>#divCaixa">Caixa</a>
        <a class="py-2 d-none d-md-inline-block" href="index.php<?php echo mesGet('?'); ?>#divMovimento">Movimentação</a>
        <div class="dropdown">
            <a class="py-2 d-none d-md-inline-block dropdown-toggle" href="#" id="ddGrupo" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Grupos</a>
            <div class="dropdown-menu" aria-labeledby="ddGrupo">
                <a class="dropdown-item" href="index.php<?php echo mesGet('?'); ?>#divCategoria">Categorias</a>
                <a class="dropdown-item" href="index.php<?php echo mesGet('?'); ?>#divParcela">Parcelas</a>
                <hr class="my-2"/>
                <a class="dropdown-item" href="index.php<?php echo mesGet('?'); ?>#divObjetivo">Objetivos</a>
            </div>
        </div>
        <div class="dropdown">
            <a class="py-2 d-none d-md-inline-block dropdown-toggle" href="#" id="ddGrafico" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Gráficos</a>
            <div class="dropdown-menu" aria-labeledby="ddGrafico">
                <a class="dropdown-item" href="index.php<?php echo mesGet('?'); ?>#divChartCircle">Pizza</a>
                <a class="dropdown-item" href="index.php<?php echo mesGet('?'); ?>#divChartBar">Barra</a>
            </div>
        </div>
        <a class="py-2 d-none d-md-inline-block" href="#" onclick="$('#modalCalculadoraAutoClick').click();">Calculadora</a>
        <a class="py-2 d-none d-md-inline-block" href="../">Sair</a>
        <?php if(isset($_SESSION['user_mtworld'])&&$_SESSION['user_mtworld']>0){ ?>
        <a class="py-2 d-none d-md-inline-block" style="opacity: .9" id="aMatthNavigate" onclick="$('#matthNavigate').modal('show');" href="#">
            <span class="material-icons align-middle">ac_unit</span>
        </a>
        <?php } ?>
        <button class="btn text-light d-inline-block d-md-none" type="button" data-toggle="collapse" data-target="#collapseOpc" aria-expanded="false" aria-controls="collapseOpc"><i class="material-icons align-middle">expand_more</i></button>
        <!--Responsive-Collapse-->
        <div class="collapse" id="collapseOpc">
            <div class="card card-body text-center mb-3">
                <a class="py-2" href="#" id="aMovimento" data-toggle="modal" data-target="#modalMovimento">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="d-block mx-auto"><circle cx="12" cy="12" r="10"></circle><line x1="14.31" y1="8" x2="20.05" y2="17.94"></line><line x1="9.69" y1="8" x2="21.17" y2="8"></line><line x1="7.38" y1="12" x2="13.12" y2="2.06"></line><line x1="9.69" y1="16" x2="3.95" y2="6.06"></line><line x1="14.31" y1="16" x2="2.83" y2="16"></line><line x1="16.62" y1="12" x2="10.88" y2="21.94"></line></svg>
                </a>
                <a class="py-2" href="index.php<?php echo mesGet('?'); ?>#divPendente">Pendências</a>
                <a class="py-2" href="index.php<?php echo mesGet('?'); ?>#divCaixa">Caixa</a>
                <a class="py-2" href="index.php<?php echo mesGet('?'); ?>#divMovimento">Movimentação</a>
                <div class="dropdown py-2">
                    <a class="py-2 dropdown-toggle" href="#" id="ddGrupo" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Grupos</a>
                    <div class="dropdown-menu" aria-labeledby="ddGrupo">
                        <a class="dropdown-item" href="index.php<?php echo mesGet('?'); ?>#divCategoria">Categorias</a>
                        <a class="dropdown-item" href="index.php<?php echo mesGet('?'); ?>#divParcela">Parcelas</a>
                        <hr class="my-2"/>
                        <a class="dropdown-item" href="index.php<?php echo mesGet('?'); ?>#divObjetivo">Objetivos</a>
                    </div>
                </div>
                <div class="dropdown py-2">
                    <a class="py-2 dropdown-toggle" href="#" id="ddGrafico" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Gráficos</a>
                    <div class="dropdown-menu" aria-labeledby="ddGrafico">
                        <a class="dropdown-item" href="index.php<?php echo mesGet('?'); ?>#divChartCircle">Pizza</a>
                        <a class="dropdown-item" href="index.php<?php echo mesGet('?'); ?>#divChartBar">Barra</a>
                    </div>
                </div>
                <a class="py-2" href="#" onclick="$('#modalCalculadoraAutoClick').click();">Calculadora</a>
                <a class="py-2" href="../">Sair</a>
                <?php if(isset($_SESSION['user_mtworld'])&&$_SESSION['user_mtworld']>0){ ?>
                <a class="py-2" style="opacity: .9" id="aMatthNavigate" onclick="$('#matthNavigate').modal('show');" href="#">
                    <span class="material-icons align-middle">ac_unit</span>
                </a>
                <?php } ?>
            </div>
        </div>
      </div>
    </nav>
    <!--Saldo-->
    <div class="position-relative overflow-hidden p-3 p-md-5 m-md-3 text-center bg-light">
      <div class="col-md-5 p-lg-5 mx-auto my-5">
        <h1 class="display-4 font-weight-normal">Personal Control</h1>
        <!--CARD SALDO-->
        <div class="card bg-transparent bx-shadow p-2 pt-3 pb-1 mb-3" id="contasSaldo">
            <div class="input-group">
                <select class="custom-select bg-transparent bx-shadow" onchange="altSaldo()" id="selectConta">
                    <?php
                        $sql = "select * from conta where usuario_id='{$_SESSION['pctrl_user']}';";
                        $dataC = enviarComand($sql,'bd_pctrl');
                        $entrou = 0;
                        while($resC = $dataC->fetch_assoc()){ $entrou++;
                    ?>
                    <option value=<?php echo $entrou; ?>>Saldo <?php echo $resC['nome']; ?></option>
                    <?php } ?>
                    <option value=<?php echo $entrou+1; ?> selected>Total</option>
                </select>
                <div class="input-group-append m-0 p-0">
                    <button type="button" class="btn btn-outline-secondary bx-shadow p-0 rounded-right active" style="max-width: 60px;" data-toggle="modal" data-target="#modalConta"><i class="material-icons align-middle">add</i></button>
                </div>
            </div>
            <?php
                $sql = "select * from conta where usuario_id='{$_SESSION['pctrl_user']}';";
                $dataR = enviarComand($sql,'bd_pctrl');
                $entrou = 0;
                $saldoTotal = 0;
                while($resR = $dataR->fetch_assoc()){ $entrou++; $saldoTotal+=$resR['saldo'];
            ?>
            <p class="text-success display-4 mb-1 d-none" id="saldo<?php echo $entrou; ?>"><?php echo "R$ ".number_format(($resR['saldo']?$resR['saldo']:'0.00'),2,',','.'); ?></p>
            <?php } ?>
            <p class="text-success display-4 mb-1" id="saldo<?php echo $entrou+1; ?>"><?php echo "R$ ".number_format($saldoTotal,2,',','.'); ?></p>
        </div>    
        <!--PREFERENCES-->
        <div class="btn-group">
            <a class="btn btn-outline-secondary" href="#" data-target="#modalPreference" data-toggle="modal">Preferências</a>
            <div class="btn-group dropright m-0 p-0 d-none d-md-inline-block">
                <button type="button" class="btn btn-outline-secondary px-0 rounded-right" style="max-width: 30px;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="dropdownSaldo"><i class="material-icons align-middle">more_vert</i></button>
                <div class="dropdown-menu" aria-labelledby="dropdownSaldo">
                    <a class="dropdown-item text-warning text-center expandir-row-0" href="index.php?exp<?php echo mesGet('&'); ?>">Expandir <i class="material-icons align-middle girar90">unfold_more</i></a>
                    <a class="dropdown-item text-warning recolher-row-0 d-none" href="index.php">Recolher <i class="material-icons align-middle girar90">unfold_less</i></a>
                </div>
            </div>
        </div>
      </div>
      <div class="product-device box-shadow d-none d-md-block"></div>
      <div class="product-device product-device-2 box-shadow d-none d-md-block"></div>
    </div>
    <!--1 Row - Pendentes e Caixa -->
    <div class="d-md-flex flex-md-equal w-100 my-md-3 pl-md-3" id="divRow1">
      <!--Pêndencias-->
      <div id="divRow1a">
      <div class="bg-dark mr-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center text-white overflow-hidden" id="divPendente">
        <!--Header-->
        <div class="my-3 py-3">
          <h2 class="display-5">
            Movimentações Pendentes
            <div class="btn-group dropleft">
                <button type="button" class="btn text-light px-0 rounded-right" style="max-width: 30px;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="dropdownPendente"><i class="material-icons align-middle">more_vert</i></button>
                <div class="dropdown-menu" aria-labelledby="dropdownPendente">
                    <a class="dropdown-item text-warning text-center expandir-row-1 d-none d-md-inline-block" onclick="altWrap(1,true);">Expandir <i class="material-icons align-middle girar90">unfold_more</i></a>
                    <a class="dropdown-item text-warning text-center recolher-row-1 d-none" onclick="altWrap(1,false);">Recolher <i class="material-icons align-middle girar90">unfold_less</i></a>
                    <a class="dropdown-item text-info text-center" href="imp.php" target="_blank">Relatório <i class="material-icons align-middle">system_update_alt</i></a>
                    <a class="dropdown-item text-success text-center" href="index.php?gerenciar=1<?php echo mesGet('&'); ?>#divPendente">Finalizar <i class="material-icons align-middle">queue</i></a>
                    <div class="text-center d-block bg-secondary text-light border-top"><small>Filtro</small></div>
                    <div class="font-weight-normal pt-2 px-2 text-muted">
                        <input type="checkbox" id="pend-tipo" onclick="revela('pend-tipo');"><label class="pl-2">Tipo</label><br/>
                        <input type="checkbox" id="pend-cat" onclick="revela('pend-cat');"><label class="pl-2">Categoria</label><br/>
                        <input type="checkbox" id="pend-parc" onclick="revela('pend-parc');"><label class="pl-2">Parcela</label><br/>
                        <input type="checkbox" id="pend-rel" onclick="revela('pend-rel');"><label class="pl-2">Relevância</label><br/>
                        <hr class="p-1 m-0"/>
                        <input type="checkbox" id="pend-all" onclick="checkRevela('pend');"><label class="pl-2">Todos</label><br/>
                    </div>
                </div>
            </div>
          </h2>
          <p class="lead">Pendências de <a href="#" class="text-danger font-weight-bolder text-decoration" data-toggle="modal" data-target="#modalCalendar"><?php echo isset($_GET['calendarM'])?$mesCalendar[$_GET['calendarM']]:$mesCalendar[intval(date('m'))];?>/<?php echo isset($_GET['calendarY'])?$_GET['calendarY']:date('Y');?></a>.</p>
        </div>
        <!--Body-->
        <div class="bg-light box-shadow mx-auto" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;">
            <div class="container-fluid p-0 pt-1">
                <div class="table-responsive">
                <table class="table table-hover mb-1">
                    <thead>
                        <tr>
                            <th scope="col" class="border-top-0">Dia</th>
                            <th scope="col" class="border-top-0 d-none pend-tipo">Tipo</th>
                            <th scope="col" class="text-nowrap border-top-0">R$</th>
                            <th scope="col" class="border-top-0">Descrição</th>
                            <th scope="col" class="border-top-0 d-none pend-cat">Categoria</th>
                            <th scope="col" class="border-top-0 d-none pend-parc">Parcela</th>
                            <th scope="col" class="border-top-0 d-none pend-rel">Relevância</th>
                            <th scope="col" class="border-top-0">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            //Nomear especificamente cada coluna do banco que será utilizada
                            $searchM = isset($_GET['calendarM'])?$_GET['calendarM']:'month(now())';
                            $searchY = isset($_GET['calendarY'])?$_GET['calendarY']:'year(now())';
                            $sql="select m.id,data_,tipo,valor,descricao,c.nome,parcela,relevancia,obs from movimento m inner join categoria c on m.categoria_id=c.id where m.usuario_id='{$_SESSION['pctrl_user']}' and month(data_)=$searchM and year(data_)=$searchY and status=false order by data_;";
                            $dataPende = enviarComand($sql,'bd_pctrl'); $entrou = 0;
                            while($linePende=$dataPende->fetch_assoc()){ $entrou ++;
                        ?>
                        <tr>
                            <input type="hidden" id="idPendente<?php echo $entrou; ?>" value="<?php echo $linePende['id']; ?>">
                            <td class="td-data text-nowrap my-0" title="<?php echo $linePende['data_']; ?>"><?php echo date('d',strtotime($linePende['data_'])); ?></td>
                            <td class="td-tipo text-nowrap my-0 d-none pend-tipo" title="<?php echo $linePende['tipo']=='Receita'?'Entrada':'Saída'; ?>"><?php echo $linePende['tipo']; ?></td>
                            <td class="td-valor text-nowrap my-0" title="R$ <?php echo number_format($linePende['valor'],2,',','.'); ?>"><?php echo number_format($linePende['valor'],2,',','.'); ?></td>
                            <td class="td-descricao text-truncate my-0" title="<?php echo $linePende['descricao']; ?>">
                                <?php echo $linePende['descricao']; ?>
                            </td>
                            <td class="td-categoria text-truncate my-0 d-none pend-cat" title="<?php echo $linePende['nome']; ?>"><?php echo $linePende['nome']; ?></td>
                            <td class="td-parcela text-truncate my-0 d-none pend-parc" title="Número da Parcela">
                                <?php echo $linePende['parcela']?$linePende['parcela']:'-'; ?>
                            </td>
                            <td class="td-relevancia text-truncate my-0 d-none pend-rel" title="Relevância <?php echo $linePende['relevancia']; ?>">
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
                            <td class="d-none"><?php echo $linePende['obs']; ?></td>
                            <!--Action-->
                            <td class="my-0 py-0">
                                <div class="btn-group pt-2">
                                    <button type="button" class="btn p-0 mt-0 pt-1" data-toggle="modal" data-target="#modalDetalhe" onclick="formatDetalhe(<?php echo $entrou; ?>,'Pendente')" title="Detalhes da Movimentação">
                                        <i class="material-icons text-primary">loupe</i>
                                    </button>
                                    <button type="button" class="btn p-0 mt-0 pt-1" title="Finalizar a Movimentação" data-toggle="modal" data-target="#modalPagar" onclick="formatPg(<?php echo $entrou; ?>,'Pendente')">
                                        <i class="material-icons text-<?php echo $linePende['valor']<0?'danger':'success'; ?>">monetization_on</i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php } if($entrou==0) { ?>
                        <tr>
                            <td colspan="4" class="py-5">
                                <div class="pt-2 pb-5">
                                    Sem Movimentações Pendentes
                                    <button type="button" class="btn btn-primary btn-block my-2 btn-sm" data-toggle="modal" data-target="#modalMovimento">
                                        <i class="material-icons align-middle">add</i>
                                    </button>
                                    <small>Adicione novas Movimentações clicando no botão acima.</small>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <!--Paginação-->
                <nav class="mt-2 d-none" aria-label="Page navigation example" id="navPendente">
                    <ul class="pagination pagination-sm justify-content-center">
                        <li class="page-item  text-primary active" id="navPendente-1"  onclick="tableIntercala('Pendente',1)"><a class="page-link">1</a></li>
                        <li class="page-item text-primary" id="navPendente-2" onclick="tableIntercala('Pendente',2)"><a class="page-link">2</a></li>
                    </ul>
                </nav>
                </div>
            </div>
        </div>
      </div>
      </div>
      <!--Caixa-->
      <div id="divRow1b">
      <div class="bg-light mr-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden" id="divCaixa">
        <!--Header-->
        <div class="my-3 p-3">
            <h2 class="display-5">
                Caixa - <?php echo (isset($_GET['calendarM'])?$mesCalendar[$_GET['calendarM']]:$mesCalendar[intval(date('m'))]).'/'.(isset($_GET['calendarY'])?$_GET['calendarY']:date('Y')); ?>
                <div class="btn-group dropleft">
                    <button type="button" class="btn px-0 rounded-right" style="max-width: 30px;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="dropdownCaixa"><i class="material-icons align-middle">more_vert</i></button>
                    <div class="dropdown-menu" aria-labelledby="dropdownCaixa">
                        <a class="dropdown-item text-warning text-center expandir-row-1 d-none d-md-inline-block" onclick="altWrap(1,true);">Expandir <i class="material-icons align-middle girar90">unfold_more</i></a>
                        <a class="dropdown-item text-warning recolher-row-1 d-none" onclick="altWrap(1,false);">Recolher <i class="material-icons align-middle girar90">unfold_less</i></a>
                        <a class="dropdown-item" data-toggle="modal" data-target="#modalMeta">Alterar Meta <i class="material-icons align-middle">show_chart</i></a>                        
                        <div class="text-center d-block bg-secondary text-light border-top"><small>Saldo em Conta</small></div>
                        <div class="font-weight-normal pt-2 px-2 text-muted">
                            <?php
                                $sql = "select nome,saldo from conta where usuario_id={$_SESSION['pctrl_user']};";
                                $data = enviarComand($sql,'bd_pctrl');
                                while($resSaldo = $data->fetch_assoc()){
                            ?>
                            <p><?php echo $resSaldo['nome']; ?><span class="float-right <?php echo $resSaldo['saldo']>=0?'text-success':'text-danger'; ?>"><?php echo $resSaldo['saldo']?$resSaldo['saldo']:'0,00'; ?></span></p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </h2>
            <p class="lead">
                <a href="index.php?<?php echo (isset($_GET['exp'])?'exp&':'').mesCalc(-1);?>#divCaixa"><i class="material-icons align-middle">arrow_left</i></a>
                Fechamento do mês. <a href="#" data-toggle="modal" data-target="#modalCalendar"><i class="material-icons align-middle text-dark">event_available</i></a>
                <a href="index.php?<?php echo (isset($_GET['exp'])?'exp&':'').mesCalc(1);?>#divCaixa"><i class="material-icons align-middle">arrow_right</i></a>
            </p>
        </div>
        <!--Body-->
        <div class="bg-dark box-shadow mx-auto" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;">
            <div class="container-fluid p-0 pt-1">
                <div class="table-responsive">
                <table class="table table-dark table-hover" style="border-top-left-radius: 21px; border-top-right-radius: 21px;">
                    <thead>
                        <tr>
                            <th scope="col" class="border-top-0 text-left">Fechamento</th>
                            <th scope="col" class="text-nowrap border-top-0">R$</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $cxSearchM = isset($_GET['calendarM'])?$_GET['calendarM']:'month(now())';
                            $cxSearchY = isset($_GET['calendarY'])?$_GET['calendarY']:'year(now())';
                            if(!(isset($_GET['calendarM'])&&isset($_GET['calendarY']))) $mesSelect = date('Y-m').'-01';
                            else $mesSelect = $cxSearchY.'-'.($cxSearchM<10?'0'.$cxSearchM:$cxSearchM).'-01';
                            $sql = "select * from caixa where usuario_id='{$_SESSION['pctrl_user']}' and mesano<='$mesSelect' order by mesano desc limit 1;";
                            $dataCaixa = enviarComand($sql,'bd_pctrl');
                            $lineCaixa = $dataCaixa->fetch_assoc();
                        ?>
                        <tr><th scope="col" class="text-nowrap text-left">Inicial</th>
                            <td class="text-nowrap">
                                <?php
                                    $testeMesCaixa = $lineCaixa['mesano'] == $mesSelect;
                                    if($testeMesCaixa) echo number_format($lineCaixa['inicial']?$lineCaixa['inicial']:'0.00',2,',','.');
                                    else echo number_format($lineCaixa['final']?$lineCaixa['final']:'0.00',2,',','.');
                                ?>
                            </td>
                        </tr>
                        <tr><th scope="col" class="text-nowrap text-left">Inicial Parcial</th>
                            <td class="text-nowrap">
                                <?php 
                                    if($testeMesCaixa) echo number_format($lineCaixa['inicial_parcial']?$lineCaixa['inicial_parcial']:'0.00',2,',','.');
                                    else echo number_format($lineCaixa['final_parcial']?$lineCaixa['final_parcial']:'0.00',2,',','.');
                                ?>
                            </td>
                        </tr>
                        <tr><th scope="col" class="text-nowrap text-left">Final</th>
                            <td class="text-nowrap"><?php echo number_format($lineCaixa['final']?$lineCaixa['final']:'0.00',2,',','.'); ?></td></tr>
                        <tr><th scope="col" class="text-nowrap text-left">Final Parcial</th>
                            <td class="text-nowrap"><?php echo number_format($lineCaixa['final_parcial']?$lineCaixa['final_parcial']:'0.00',2,',','.'); ?></td></tr>
                        <tr><th scope="col" class="text-nowrap text-left text-warning">Meta</th>
                            <td class="text-nowrap"><a data-toggle="modal" data-target="#modalMeta" class="text-warning" title="Alterar"><?php echo number_format($lineCaixa['meta']?$lineCaixa['meta']:'0.00',2,',','.'); ?></a></td></tr>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
      </div>
      </div>
    </div>
    <!--2 Row - Movimentação e Categoria -->
    <div class="d-md-flex flex-md-equal w-100 my-md-3 pl-md-3" id="divRow2">
      <!--Movimentação-->
      <div id="divRow2a">
      <div class="bg-light mr-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden" id="divMovimento">
        <!--Header-->
        <div class="my-3 p-3">
            <h2 class="display-5">
                Movimentações - <?php echo (isset($_GET['calendarM'])?$mesCalendar[$_GET['calendarM']]:$mesCalendar[intval(date('m'))]).'/'.(isset($_GET['calendarY'])?$_GET['calendarY']:date('Y')); ?>
                <div class="btn-group dropleft">
                    <button type="button" class="btn px-0 rounded-right" style="max-width: 30px;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="dropdownMovimento"><i class="material-icons align-middle">more_vert</i></button>
                    <div class="dropdown-menu" aria-labelledby="dropdownMovimento">
                        <a class="dropdown-item text-warning text-center expandir-row-2 d-none d-md-inline-block" onclick="altWrap(2,true);">Expandir <i class="material-icons align-middle girar90">unfold_more</i></a>
                        <a class="dropdown-item text-warning recolher-row-2 d-none" onclick="altWrap(2,false);">Recolher <i class="material-icons align-middle girar90">unfold_less</i></a>
                        <a class="dropdown-item text-info" href="imp.php" target="_blank">&nbsp Relatório <i class="material-icons align-middle">system_update_alt</i></a>
                        <a class="dropdown-item text-success text-center" href="index.php?gerenciar=1<?php echo mesGet('&'); ?>#divMovimento">Finalizar <i class="material-icons align-middle">queue</i></a>
                        <div class="text-center d-block bg-secondary text-light border-top">
                        <small>Filtro</small>
                        </div>
                        <div class="font-weight-normal pt-2 px-2 text-muted">
                            <input type="checkbox" id="mov-tipo" onclick="revela('mov-tipo');"><label class="pl-2">Tipo</label><br/>
                            <input type="checkbox" id="mov-cat" onclick="revela('mov-cat');"><label class="pl-2">Categoria</label><br/>
                            <input type="checkbox" id="mov-parc" onclick="revela('mov-parc');"><label class="pl-2">Parcela</label><br/>
                            <input type="checkbox" id="mov-rel" onclick="revela('mov-rel');"><label class="pl-2">Relevância</label><br/>
                            <hr class="p-1 m-0"/>
                            <input type="checkbox" id="mov-all" onclick="checkRevela('mov');"><label class="pl-2">Todos</label><br/>
                        </div>
                    </div>
                </div>
            </h2>
            <p class="lead">
                <a href="index.php?<?php echo (isset($_GET['exp'])?'exp&':'').mesCalc(-1);?>#divMovimento"><i class="material-icons align-middle">arrow_left</i></a>
                Fechamento do mês. <a href="#" data-toggle="modal" data-target="#modalCalendar"><i class="material-icons align-middle text-dark">event_available</i></a>
                <a href="index.php?<?php echo (isset($_GET['exp'])?'exp&':'').mesCalc(1);?>#divMovimento"><i class="material-icons align-middle">arrow_right</i></a>
            </p>
        </div>
        <!--Body-->
        <div class="bg-dark box-shadow mx-auto" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;">
            <div class="container-fluid p-0 pt-1">
                <div class="table-responsive">
                <table class="table table-dark table-hover mb-1 bg-transparent" style="border-top-left-radius: 21px; border-top-right-radius: 21px;">
                    <thead>
                        <tr>
                            <th scope="col" class="border-top-0">Dia</th>
                            <th scope="col" class="border-top-0 d-none mov-tipo">Tipo</th>
                            <th scope="col" class="text-nowrap border-top-0">R$</th>
                            <th scope="col" class="border-top-0">Descrição</th>
                            <th scope="col" class="border-top-0 d-none mov-cat">Categoria</th>
                            <th scope="col" class="border-top-0 d-none mov-parc">Parcela</th>
                            <th scope="col" class="border-top-0 d-none mov-rel">Relevância</th>
                            <th scope="col" class="border-top-0">+</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $mvSearchM = isset($_GET['calendarM'])?$_GET['calendarM']:'month(now())';
                            $mvSearchY = isset($_GET['calendarY'])?$_GET['calendarY']:'year(now())';
                            $sql="select m.id,data_,tipo,valor,descricao,status,c.nome,parcela,relevancia,obs from movimento m inner join categoria c on m.categoria_id=c.id where m.usuario_id='{$_SESSION['pctrl_user']}' and month(data_)=$mvSearchM and year(data_)=$mvSearchY order by data_;";
                            $dataPende = enviarComand($sql,'bd_pctrl'); $entrou = 0;
                            while($linePende=$dataPende->fetch_assoc()){ $entrou++;
                        ?>
                        <tr>
                            <input type="hidden" id="idMovimento<?php echo $entrou; ?>" value="<?php echo $linePende['id']; ?>">
                            <td class="td-data text-nowrap my-0" title="<?php echo $linePende['data_']; ?>"><?php echo date('d',strtotime($linePende['data_'])); ?></td>
                            <td class="td-tipo text-nowrap my-0 d-none mov-tipo" title="<?php echo $linePende['tipo']=='Receita'?'Entrada':'Saída'; ?>"><?php echo $linePende['tipo']; ?></td>
                            <td class="td-valor text-nowrap my-0" title="R$ <?php echo number_format($linePende['valor'],2,',','.'); ?>"><?php echo number_format($linePende['valor'],2,',','.'); ?></td>
                            <td class="td-descricao text-truncate my-0" title="<?php echo $linePende['descricao']; ?>">
                                <?php if($linePende['status']){ ?>
                                    <i class="material-icons align-top text-warning" title="Efetuado">money_off</i>
                                <?php } echo $linePende['descricao']; ?>
                            </td>
                            <td class="td-categoria text-truncate my-0 d-none mov-cat" title="<?php echo $linePende['nome']; ?>"><?php echo $linePende['nome']; ?></td>
                            <td class="td-parcela text-truncate my-0 d-none mov-parc" title="Número da Parcela">
                                <?php echo $linePende['parcela']?$linePende['parcela']:'-'; ?>
                            </td>
                            <td class="td-relevancia text-truncate my-0 d-none mov-rel" title="Relevância <?php echo $linePende['relevancia']; ?>">
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
                            <td class="td-obs d-none"><?php echo $linePende['obs']; ?></td>
                            <td class="my-0 py-0">
                                <div class="btn-group pt-2">
                                    <button type="button" class="btn p-0 mt-0 pt-1" data-toggle="modal" data-target="#modalDetalhe" onclick="formatDetalhe(<?php echo $entrou; ?>,'Movimento')" title="Detalhes da Movimentação">
                                        <i class="material-icons text-info">loupe</i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php } if($entrou==0) { ?>
                        <tr>
                            <td colspan="4" class="py-5">
                                <div class="pt-2 pb-5">
                                    Sem Movimentações Pendentes
                                    <button type="button" class="btn btn-primary btn-block my-2 btn-sm" data-toggle="modal" data-target="#modalMovimento">
                                        <i class="material-icons align-middle">add</i>
                                    </button>
                                    <small>Adicione novas Movimentações clicando no botão acima.</small>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <!--Paginação-->
                <nav class="mt-2 d-none" aria-label="Page navigation example" id="navMovimento">
                    <ul class="pagination pagination-sm justify-content-center">
                        <li class="page-item  text-primary active" id="navMovimento-1"  onclick="tableIntercala('Movimento',1)"><a class="page-link">1</a></li>
                        <li class="page-item text-primary" id="navMovimento-2" onclick="tableIntercala('Movimento',2)"><a class="page-link">2</a></li>
                    </ul>
                </nav>
                </div>
            </div>
        </div>
      </div>
      </div>
      <!--Categoria-->
      <div id="divRow2b">
      <div class="bg-primary mr-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center text-white overflow-hidden" id="divCategoria">
        <!--Header-->
        <div class="my-3 py-3">
          <h2 class="display-5">
              Agrupado por Categoria
              <div class="btn-group dropleft">
                    <button type="button" class="btn text-light px-0 rounded-right" style="max-width: 30px;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="dropdownCategoria"><i class="material-icons align-middle">more_vert</i></button>
                    <div class="dropdown-menu" aria-labelledby="dropdownCategoria">
                        <a class="dropdown-item text-warning text-center expandir-row-2  d-none d-md-inline-block" onclick="altWrap(2,true);">Expandir <i class="material-icons align-middle girar90">unfold_more</i></a>
                        <a class="dropdown-item text-warning recolher-row-2 d-none" onclick="altWrap(2,false);">Recolher <i class="material-icons align-middle girar90">unfold_less</i></a>
                        <a class="dropdown-item" data-toggle="modal" data-target="#modalCategoria">Nova Categoria<i class="material-icons align-middle m-1">add_circle</i></a>
                        <a class="dropdown-item" onclick="msg(0,['Embreve - Intervalo de Categorias.<br/><small class=\'text-muted\'>Mostrará o valor das Categorias num determinado periodo de Tempo</small>']);">Intervalo<i class="material-icons align-middle m-1">shutter_speed</i></a>
                        <a class="dropdown-item" href="index.php?gerenciarCategoria#divCategoria">Gerenciamento<i class="material-icons align-middle m-1">tune</i></a>
                    </div>
                </div>
          </h2>
          <p class="lead">Selecione a Categoria que deseja consultar.</p>
        </div>
        <div class="bg-light box-shadow mx-auto" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;">
            <div class="container-fluid p-0 pt-1">
                <div class="table-responsive">
                <table class="table table-hover mb-1">
                    <thead>
                        <tr>
                            <th scope="col" class="border-top-0">Categoria</th>
                            <th scope="col" class="text-nowrap border-top-0">R$</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $sql = "select * from categoria where usuario_id='{$_SESSION['pctrl_user']}';";
                            $dataTblCat = enviarComand($sql,'bd_pctrl'); $entrou = false;
                            while($resTblCat = $dataTblCat->fetch_assoc()){ $entrou = true;
                        ?>
                        <tr>
                            <?php
                                $sql = "select if(sum(valor),sum(valor),0.00) valor from movimento where categoria_id='{$resTblCat['id']}' and usuario_id='{$_SESSION['pctrl_user']}';";
                                $dataValorCat = enviarComand($sql,'bd_pctrl');
                                $resValorCat = $dataValorCat->fetch_assoc();
                            ?>
                            <td class="text-nowrap my-0" title="Categoria">
                                <a class="text-<?php echo $resValorCat['valor']>=0?'info':'danger'; ?>" href="index.php?categ=<?php echo $resTblCat['id']; ?>#divCategoria"><?php echo $resTblCat['nome']; ?></a>
                            </td>
                            <td class="text-nowrap my-0" title="Valor Acumulado"><?php echo number_format($resValorCat['valor'],2,',','.'); ?></td>
                        </tr>
                        <?php } if(!$entrou) { ?>
                        <tr>
                            <td colspan="4" class="py-5">
                                <div class="pt-2 pb-5">
                                    Sem Categorias Cadastradas
                                    <button type="button" class="btn btn-primary btn-block my-2 btn-sm" data-toggle="modal" data-target="#modalCategoria">
                                        <i class="material-icons align-middle">add</i>
                                    </button>
                                    <small>Adicione novas Categorias clicando no botão acima.</small>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <!--Paginação-->
                <nav class="mt-2 d-none" aria-label="Page navigation example" id="navCategoria">
                    <ul class="pagination pagination-sm justify-content-center">
                        <li class="page-item  text-primary active" id="navCategoria-1"  onclick="tableIntercala('Categoria',1)"><a class="page-link">1</a></li>
                        <li class="page-item text-primary" id="navCategoria-2" onclick="tableIntercala('Categoria',2)"><a class="page-link">2</a></li>
                    </ul>
                </nav>
                </div>
            </div>
        </div>
      </div>
      </div>
    </div>
    <!--3 Row - Parcela e Objetivos -->
    <div class="d-md-flex flex-md-equal w-100 my-md-3 pl-md-3" id="divRow3">
      <!--Parcela-->
      <div id="divRow3a">
      <div class="bg-primary mr-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center text-white overflow-hidden" id="divParcela">
        <!--Header-->
        <div class="my-3 p-3">
          <h2 class="display-5">
              Agrupado por Parcela
              <div class="btn-group dropleft">
                    <button type="button" class="btn text-light px-0 rounded-right" style="max-width: 30px;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="dropdownGrupo"><i class="material-icons align-middle">more_vert</i></button>
                    <div class="dropdown-menu" aria-labelledby="dropdownGrupo">
                        <a class="dropdown-item text-warning text-center expandir-row-3 d-none d-md-inline-block" onclick="altWrap(3,true);">Expandir <i class="material-icons align-middle girar90">unfold_more</i></a>
                        <a class="dropdown-item text-warning recolher-row-3 d-none" onclick="altWrap(3,false);">Recolher <i class="material-icons align-middle girar90">unfold_less</i></a>
                        <a class="dropdown-item" onclick="">Transferências <i class="material-icons align-middle">swap_horizontal_circle</i></a>
                        <div class="text-center d-block bg-secondary text-light border-top">
                        <small>Filtro</small>
                        </div>
                        <div class="font-weight-normal pt-2 px-2 text-muted">
                            <input type="checkbox" checked><label class="pl-2">Não Finalizadas</label><br/>
                            <input type="checkbox"><label class="pl-2">Finalizadas</label><br/>
                            <input type="checkbox"><label class="pl-2">Rotatividade</label><br/>
                            <input type="checkbox"><label class="pl-2">Total</label><br/>
                        </div>
                    </div>
              </div>
          </h2>
          <p class="lead">Selecione a Parcela que deseja consultar.</p>
        </div>
        <div class="bg-white box-shadow mx-auto" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;">
            <div class="container-fluid p-0 pt-1">
                <div class="table-responsive">
                <table class="table table-hover mb-1">
                    <thead>
                        <tr>
                            <th scope="col" class="text-nowrap border-top-0">Descrição da Parcela</th>
                            <th scope="col" class="border-top-0">Qtd.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $sql = "select * from movimento where parcela_id!=0 and usuario_id={$_SESSION['pctrl_user']} order by parcela_id";
                            $distinct = 0;
                            $dataTblParcela = enviarComand($sql,'bd_pctrl'); $entrou = false;
                            while($resTblParcela = $dataTblParcela->fetch_assoc()){
                                $entrou = true;
                                if($distinct!=$resTblParcela['parcela_id']){
                                    $distinct=$resTblParcela['parcela_id'];
                        ?>
                        <tr>
                            <td class="text-nowrap my-0" title="Dia"><a class="text-dark" href="index.php?parc=<?php echo $resTblParcela['parcela_id']; ?>#divParcela"><?php echo $resTblParcela['descricao']; ?></a></td>
                            <td class="text-nowrap my-0" title="Valor"><?php echo substr(strstr($resTblParcela['parcela'],'/'),1); ?></td>
                        </tr>
                        <?php } } if(!$entrou) { ?>
                        <tr>
                            <td colspan="4" class="py-5">
                                <div class="pt-2 pb-5">
                                    Sem Parcelas Cadastradas
                                    <button type="button" class="btn btn-primary btn-block my-2 btn-sm" data-toggle="modal" data-target="#modalMovimento">
                                        <i class="material-icons align-middle">add</i>
                                    </button>
                                    <small>Adicione novas Parcelas clicando no botão acima.</small>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <!--Paginação-->
                <nav class="mt-2 d-none" aria-label="Page navigation example" id="navParcela">
                    <ul class="pagination pagination-sm justify-content-center">
                        <li class="page-item  text-primary active" id="navParcela-1"  onclick="tableIntercala('Parcela',1)"><a class="page-link">1</a></li>
                        <li class="page-item text-primary" id="navParcela-2" onclick="tableIntercala('Parcela',2)"><a class="page-link">2</a></li>
                    </ul>
                </nav>
                </div>
            </div>
        </div>
      </div>
      </div>
      <!--Objetivos-->
      <div id="divRow3b">
      <div class="bg-light mr-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden" id="divObjetivo">
        <!--Header-->
        <div class="my-3 py-3">
          <h2 class="display-5">
              Objetivos !
              <div class="btn-group dropleft">
                    <button type="button" class="btn px-0 rounded-right" style="max-width: 30px;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="dropdownObjetivo"><i class="material-icons align-middle">more_vert</i></button>
                    <div class="dropdown-menu" aria-labelledby="dropdownObjetivo">
                        <a class="dropdown-item text-warning text-center expandir-row-3 d-none d-md-inline-block" onclick="altWrap(3,true);">Expandir <i class="material-icons align-middle girar90">unfold_more</i></a>
                        <a class="dropdown-item text-warning recolher-row-3 d-none" onclick="altWrap(3,false);">Recolher <i class="material-icons align-middle girar90">unfold_less</i></a>
                        <a class="dropdown-item" data-toggle="modal" data-target="#modalObjetivo" id="aObjetivo">Add Objetivo<i class="material-icons align-middle m-1">add_circle</i></a>
                        <div class="text-center d-block bg-secondary text-light border-top">
                        <small>Filtro</small>
                        </div>
                        <div class="font-weight-normal pt-2 px-2 text-muted">
                            <input type="checkbox" checked><label class="pl-2">Não Realizados</label><br/>
                            <input type="checkbox"><label class="pl-2">Realizados</label><br/>
                        </div>
                    </div>
              </div>
          </h2>
          <p class="lead">Adicione e administre os seus Objetivos.</p>
        </div>
        <div class="bg-white box-shadow mx-auto" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;">
            <div class="container-fluid p-0 pt-1">
                <div class="table-responsive">
                <table class="table table-hover mb-1">
                    <thead>
                        <tr>
                            <th scope="col" class="border-top-0">Descrição</th>
                            <th scope="col" class="text-nowrap border-top-0">R$</th>
                            <th scope="col" class="border-top-0">Relevância</th>
                            <th scope="col" class="border-top-0">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $sql = "select * from objetivo where usuario_id='{$_SESSION['pctrl_user']}' and status=false order by relevancia desc;";
                            $dataTblObj = enviarComand($sql,'bd_pctrl'); $entrou = false;
                            while($resTblObj = $dataTblObj->fetch_assoc()){ $entrou = true;
                        ?>
                        <tr>
                            <td class="text-truncate my-0" title="Descrição completa no title"><?php echo $resTblObj['nome']; ?></td>
                            <td class="text-nowrap my-0" title="Valor"><?php echo number_format($resTblObj['valor'],2,',','.'); ?></td>
                            <td class="text-nowrap my-0 text-warning" title="Dia"> <?php echo $resTblObj['relevancia']>=1?'&#9733;':'&#9734;'; echo $resTblObj['relevancia']>=2?'&#9733;':'&#9734;'; echo $resTblObj['relevancia']>=3?'&#9733;':'&#9734;'; echo $resTblObj['relevancia']>=4?'&#9733;':'&#9734;'; echo $resTblObj['relevancia']==5?'&#9733;':'&#9734;'; ?> </td>
                            <td class="my-0 py-0">
                                <div class="btn-group pt-2">
                                    <button type="button" class="btn p-0 mt-0 pt-1" title="Detalhes da Movimentação">
                                        <i class="material-icons text-primary">loupe</i>
                                    </button>
                                    <button type="button" class="btn p-0 mt-0 pt-1" title="Finalizar a Movimentação">
                                        <i class="material-icons text-info">check</i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php } if(!$entrou) { ?>
                        <tr>
                            <td colspan="4" class="py-5">
                                <div class="pt-2 pb-5">
                                    Sem Objetivos Cadastrados
                                    <button type="button" class="btn btn-primary btn-block my-2 btn-sm" data-toggle="modal" data-target="#modalObjetivo">
                                        <i class="material-icons align-middle">add</i>
                                    </button>
                                    <small>Adicione novos Objetivos clicando no botão acima.</small>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <!--Paginação-->
                <nav class="mt-2 d-none" aria-label="Page navigation example" id="navObjetivo">
                    <ul class="pagination pagination-sm justify-content-center">
                        <li class="page-item  text-primary active" id="navObjetivo-1"  onclick="tableIntercala('Objetivo',1)"><a class="page-link">1</a></li>
                        <li class="page-item text-primary" id="navObjetivo-2" onclick="tableIntercala('Objetivo',2)"><a class="page-link">2</a></li>
                    </ul>
                </nav>
                </div>
            </div>
        </div>
      </div>
      </div>
    </div>
    <!--4 Row - Gráficos -->
    <div class="d-md-flex flex-md-equal w-100 my-md-3 pl-md-3" id="divRow4">
      <!--Gráfico-->
      <div id="divRow4a">
      <div class="bg-light mr-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden" id="divChartCircle">
        <div class="my-3 p-3">
          <h2 class="display-5">
              Gráfico de Pizza
              <div class="btn-group dropleft">
                    <button type="button" class="btn px-0 rounded-right" style="max-width: 30px;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="dropdownCircle"><i class="material-icons align-middle">more_vert</i></button>
                    <div class="dropdown-menu" aria-labelledby="dropdownCircle">
                        <a class="dropdown-item text-warning text-center expandir-row-4 d-none d-md-inline-block" onclick="altWrap(4,true);">Expandir <i class="material-icons align-middle girar90">unfold_more</i></a>
                        <a class="dropdown-item text-warning recolher-row-4 d-none" onclick="altWrap(4,false);">Recolher <i class="material-icons align-middle girar90">unfold_less</i></a>
                        <div class="text-center d-block bg-secondary text-light border-top">
                        <small>Filtro</small>
                        </div>
                        <div class="font-weight-normal pt-2 px-2 text-muted">
                            <input type="checkbox"><label class="pl-2">Tipo</label><br/>
                            <input type="checkbox"><label class="pl-2">Categoria</label><br/>
                            <input type="checkbox"><label class="pl-2">Parcela</label><br/>
                            <input type="checkbox"><label class="pl-2">Relevância</label><br/>
                            <input type="checkbox"><label class="pl-2">Observações</label><br/>
                        </div>
                    </div>
              </div>
          </h2>
          <p class="lead">And an even wittier subheading.</p>
        </div>
        <div class="bg-white box-shadow mx-auto" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;">
            <img src="../img/chart-circle.png" class="mt-5" width="220">
        </div>
      </div>
      </div>
      <!--Gráfico-->
      <div id="divRow4b">
      <div class="bg-light mr-md-3 pt-3 px-3 pt-md-5 px-md-5 text-center overflow-hidden" id="divChartBar">
        <div class="my-3 py-3">
          <h2 class="display-5">
              Gráfico de Barra
              <div class="btn-group dropleft">
                    <button type="button" class="btn px-0 rounded-right" style="max-width: 30px;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="dropdownBar"><i class="material-icons align-middle">more_vert</i></button>
                    <div class="dropdown-menu" aria-labelledby="dropdownBar">
                        <a class="dropdown-item text-warning text-center expandir-row-4 d-none d-md-inline-block" onclick="altWrap(4,true);">Expandir <i class="material-icons align-middle girar90">unfold_more</i></a>
                        <a class="dropdown-item text-warning recolher-row-4 d-none" onclick="altWrap(4,false);">Recolher <i class="material-icons align-middle girar90">unfold_less</i></a>
                        <div class="text-center d-block bg-secondary text-light border-top">
                        <small>Filtro</small>
                        </div>
                        <div class="font-weight-normal pt-2 px-2 text-muted">
                            <input type="checkbox"><label class="pl-2">Tipo</label><br/>
                            <input type="checkbox"><label class="pl-2">Categoria</label><br/>
                            <input type="checkbox"><label class="pl-2">Parcela</label><br/>
                            <input type="checkbox"><label class="pl-2">Relevância</label><br/>
                            <input type="checkbox"><label class="pl-2">Observações</label><br/>
                        </div>
                    </div>
              </div>
          </h2>
          <p class="lead">And an even wittier subheading.</p>
        </div>
        <div class="bg-white box-shadow mx-auto" style="width: 80%; height: 300px; border-radius: 21px 21px 0 0;">
            <img src="../img/chart-bar.png" class="mt-5" width="420">
        </div>
      </div>
      </div>
    </div>

    <footer class="container py-5">
      <div class="row">
        <div class="col-12 col-md">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="d-block mb-2"><circle cx="12" cy="12" r="10"></circle><line x1="14.31" y1="8" x2="20.05" y2="17.94"></line><line x1="9.69" y1="8" x2="21.17" y2="8"></line><line x1="7.38" y1="12" x2="13.12" y2="2.06"></line><line x1="9.69" y1="16" x2="3.95" y2="6.06"></line><line x1="14.31" y1="16" x2="2.83" y2="16"></line><line x1="16.62" y1="12" x2="10.88" y2="21.94"></line></svg>
          <small class="d-block mb-3 text-muted">&copy; 2017-2018</small>
        </div>
        <div class="col-6 col-md">
          <h5>Features</h5>
          <ul class="list-unstyled text-small">
            <li><a class="text-muted" href="#">Cool stuff</a></li>
            <li><a class="text-muted" href="#">Random feature</a></li>
            <li><a class="text-muted" href="#">Team feature</a></li>
            <li><a class="text-muted" href="#">Stuff for developers</a></li>
            <li><a class="text-muted" href="#">Another one</a></li>
            <li><a class="text-muted" href="#">Last time</a></li>
          </ul>
        </div>
        <div class="col-6 col-md">
          <h5>Resources</h5>
          <ul class="list-unstyled text-small">
            <li><a class="text-muted" href="#">Resource</a></li>
            <li><a class="text-muted" href="#">Resource name</a></li>
            <li><a class="text-muted" href="#">Another resource</a></li>
            <li><a class="text-muted" href="#">Final resource</a></li>
          </ul>
        </div>
        <div class="col-6 col-md">
          <h5>Resources</h5>
          <ul class="list-unstyled text-small">
            <li><a class="text-muted" href="#">Business</a></li>
            <li><a class="text-muted" href="#">Education</a></li>
            <li><a class="text-muted" href="#">Government</a></li>
            <li><a class="text-muted" href="#">Gaming</a></li>
          </ul>
        </div>
        <div class="col-6 col-md">
          <h5>About</h5>
          <ul class="list-unstyled text-small">
            <li><a class="text-muted" href="#">Team</a></li>
            <li><a class="text-muted" href="#">Locations</a></li>
            <li><a class="text-muted" href="#">Privacy</a></li>
            <li><a class="text-muted" href="#">Terms</a></li>
          </ul>
        </div>
      </div>
    </footer>
    
    <?php
      /* MODAIS */
      include('../modais/modalMovimento.php');          //Modal Movimento
      include('../modais/modalCalendar.php');           //Modal Calendário
      include('../modais/modalCalculadora.php');           //Modal Calculadora
      include('../modais/modalCategoria.php');          //Modal Categoria
      include('../modais/modalConta.php');              //Modal Conta
      include('../modais/modalDetalhe.php');            //Modal Detalhes
      include('../modais/modalMeta.php');               //Modal Meta
      include('../modais/modalMult.php');               //Modal Multiplas
      include('../modais/modalObjetivo.php');           //Modal Objetivo
      include('../modais/modalGerenciar.php');          //Modal Objetivo
      include('../modais/modalGerenciarCategoria.php'); //Modal Gerenciar Categoria
      include('../modais/modalPagar.php');              //Modal Pagar
      include('../modais/modalPreference.php');         //Modal Preferências
      include('../modais/modalIntable.php');            //Modal Inserção em Tabela
      include('../modais/modalConfirma.php');           //Modal Confirmação
      include('../modais/modalMsg.php');                //Modal Mensagem
      include('../../function/ctrlm.php');
      include('../../function/mnav.php');
      include('../../function/arty.php');
      include('../../function/wmatth.php');
    ?>
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
