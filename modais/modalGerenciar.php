    <script>
        $(function(){
            <?php if(isset($_POST['fmmSql']) && strlen($_POST['fmmSql'])>0){ if(!(strpos($_POST['fmmSql'],'categoria_id')===false)){ ?>
            activeFiltroFMM('fmmCategoria');
            $('#fmmCategoriaSel').val('<?php echo $_POST['fmmCategoriaSel']; ?>');
            <?php } if(!(strpos($_POST['fmmSql'],'data_')===false)){ ?>
            activeFiltroFMM('fmmData');
            $('#fmmData1').val('<?php echo $_POST['fmmData1']; ?>');
            $('#fmmData2').val('<?php echo $_POST['fmmData2']; ?>');
            <?php } if($_POST['fmmSql']=="all"){ ?>
            activeFiltroFMM('fmmAll');
            <?php } } ?>
        });
        function activeFiltroFMM(op){
            if(op=="atualizar"){
                retorno = "";
                if($('#fmmAll').hasClass('active')){
                    retorno = "all";
                }
                else{    
                    if($('#fmmCategoria').hasClass('active')){ retorno = "categoria_id='"+$('#fmmCategoriaSel').val()+"'"; }
                    if($('#fmmData').hasClass('active')){
                        if(retorno.length!=0) retorno += " and ";
                        retorno += "data_>='"+$('#fmmData1').val()+"' and data_<='"+$('#fmmData2').val()+"'";
                    }
                }
                $('#fmmSql').val(retorno);
                $('#fmmPost').submit();
            }
            else{
                if(op=="fmmAll"){
                    if($('#fmmAll').hasClass('active')) $('#fmmAll').removeClass('active');
                    else{
                        $('#fmmAll').addClass('active');
                        if($('#fmmCategoria').hasClass('active')) $('#fmmCategoria').removeClass('active');    
                        if($('#fmmData').hasClass('active')) $('#fmmData').removeClass('active');
                        $('#fmmCategoriaDiv').addClass('d-none');
                        $('#fmmDataDiv').addClass('d-none');
                    }
                }
                else{
                    if($('#fmmAll').hasClass('active')) $('#fmmAll').removeClass('active');
                    if($('#'+op).hasClass('active')) $('#'+op).removeClass('active');
                    else $('#'+op).addClass('active');
                    if($('#'+op+'Div').hasClass('d-none')) $('#'+op+'Div').removeClass('d-none');
                    else $('#'+op+'Div').addClass('d-none');
                }
            }
        }
        function marcarFmm(){
            if($('#fmmMarcar').html()=="Marcar Todos"){
                $('#fmmMarcar').html("Desmarcar Todos");
                $('#fmmMovPost input:checkbox').prop("checked", true);
            } 
            else{
                $('#fmmMarcar').html("Marcar Todos");
                $('#fmmMovPost input:checkbox').prop("checked", false);
            }
        }
    </script>
    <!--Modal Gerenciar--> <button type="button" class="d-none" data-toggle="modal" data-target="#modalGerenciar" id="btnChamaGerenciar"></button>
    <div class="modal fade" tabindex="-1" role="dialog" id="modalGerenciar" aria-labelledby="">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Finalizar Multiplas Movimentações <i class="material-icons align-middle text-warning">queue</i></h5>
                    <button type="button" class="close" aria-label="Close" data-dismiss="modal"> <span aria-hidden="true">&times</span> </button>
                </div>
                <div class="modal-body">
                    <!--Filtros-->
                    <form method="POST" action="index.php?gerenciar=1<?php echo mesGet('&'); ?>#divPendente" name="fmmPost" id="fmmPost">
                        <?php if(isset($_POST['fmmSql']) && strlen($_POST['fmmSql'])>0){ ?>
                        <div class="alert alert-success font-weight-bold p-0 mb-2 text-center" role="alert">Filtrado</div>
                        <?php } ?>
                        <button class="btn btn-sm">Filtra Por:</button>
                        <button type="button" class="btn btn-outline-primary btn-sm" id="fmmCategoria" onclick="activeFiltroFMM(this.id)">Categoria</button>
                        <button type="button" class="btn btn-outline-danger btn-sm" id="fmmData" onclick="activeFiltroFMM(this.id)">Data</button>
                        <button type="button" class="btn btn-outline-dark btn-sm" title="Todas Pendentes" id="fmmAll" onclick="activeFiltroFMM(this.id)">Todas</button>
                        <button type="button" class="btn btn-outline-success btn-sm active" id="fmmAtualiza" onclick="activeFiltroFMM('atualizar')">
                            <i class="material-icons" style="font-size: 11pt; vertical-align: -2px;" title="Atualizar Filtros">loop</i>
                        </button>
                        <span class="material-icons float-right text-info small pointer" title="Quando não há filtros aparecem apenas as Movimentações Pendentes anteriores a data atual!" onclick="msg(0,['Quando não há filtros aparecem apenas as Movimentações Pendentes anteriores a data atual!']);">help_outline</span>
                        <div class="card my-2 d-none" id="fmmCategoriaDiv">
                            <div class="d-block text-center text-muted small">Categoria:</div>
                            <div class="input-group">
                                <select class="custom-select border-top-0 border-right-0 border-left-0 border-bottom-0" id="fmmCategoriaSel" name="fmmCategoriaSel">
                                    <?php
                                        $sqlCategoria = "select * from categoria where usuario_id='{$_SESSION['pctrl_user']}';";
                                        $dataCategoria = enviarComand($sqlCategoria,'bd_pctrl');
                                        while($lCat = $dataCategoria->fetch_assoc()){
                                    ?>
                                    <option value="<?php echo $lCat['id'];?>"><?php echo $lCat['nome'];?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <div class="input-group my-2 d-none" id="fmmDataDiv">
                            <input type="date" class="form-control" id="fmmData1" name="fmmData1" value="<?php echo date('Y-m-d'); ?>">
                            <input type="date" class="form-control" id="fmmData2" name="fmmData2" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <input type="hidden" id="fmmSql" name="fmmSql">
                    </form>
                    <!--Movimentos-->
                    <div class="mt-3" style="max-height: 280px; overflow: auto; ">
                        <form method="POST" id="fmmMovPost" action="../back/cadMov.php?pagarMult<?php echo (isset($_GET['calendarM'])?'&calendarM='.$_GET['calendarM']:'').(isset($_GET['calendarY'])?'&calendarY='.$_GET['calendarY']:''); ?>">
                        <?php
                            if(isset($_POST['fmmSql']) && strlen($_POST['fmmSql'])>0){
                                if($_POST['fmmSql']=="all") $where = "where usuario_id='{$_SESSION['pctrl_user']}' and status=0;"; 
                                else $where = "where usuario_id='{$_SESSION['pctrl_user']}' and status=0 and ".$_POST['fmmSql'].";"; 
                            }
                            else $where = "where usuario_id='{$_SESSION['pctrl_user']}' and status=0 and data_<=now();"; 
                            $entrou = 0;
                            $sql="select id,data_,descricao,valor from movimento ".$where;
                            $fmmData = enviarComand($sql,'bd_pctrl');
                            while($fmmRes = $fmmData->fetch_assoc()){ $entrou++;
                        ?>
                        <div class="card my-1">
                            <div class="card-body bg-light p-2">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-light border-0"><input type="checkbox" id="check<?php echo $entrou; ?>" name="check<?php echo $entrou; ?>"></div>
                                    </div>
                                    <input type="hidden" class="form-control" value="<?php echo $fmmRes['id']; ?>" name="inp<?php echo $entrou; ?>" id="inp<?php echo $entrou; ?>">
                                    <span class="text-info mr-2" title="<?php echo date('d/m/Y', strtotime($fmmRes['data_'])); ?>">(<?php echo date('d/m', strtotime($fmmRes['data_'])); ?>)</span><?php echo $fmmRes['descricao']; ?><span class="<?php echo $fmmRes['valor']>0?'text-success':'text-danger'; ?> ml-2">R$ <?php echo number_format($fmmRes['valor'],2,',','.'); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php } if($entrou==0){ ?>
                        <div class="card my-1">
                            <div class="card-body bg-light p-2 text-center text-info">
                                Não existem Movimentações Pendentes!
                            </div>
                        </div>
                        <?php }else{ ?>
                        <a class="small mt-2 pointer text-center d-block bg-secondary text-light rounded-top" onclick="marcarFmm()" id="fmmMarcar">Marcar Todos</a>
                        <div class="input-group mb-2 bg-danger rounded-bottom">
                            <select class="custom-select mt-2 mx-2 rounded" id="pagarMult" name="pagarMult">
                            <?php
                                $sqlConta = "select * from conta where usuario_id='{$_SESSION['pctrl_user']}';";
                                $dataConta = enviarComand($sqlConta,'bd_pctrl');
                                while($lConta = $dataConta->fetch_assoc()){
                            ?>
                                <option value="<?php echo $lConta['id'];?>"><?php echo $lConta['nome'];?></option>
                            <?php } ?>
                            </select>
                            <button class="btn btn-danger btn-block btn-sm mt-0 rounded-bottom">Finalizar</button>
                        </div>
                        <?php } ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>