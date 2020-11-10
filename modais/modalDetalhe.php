    <script>
        function deleteFinal(id,parcelado){
            if(parcelado){
                $('#modalFormConfirma').prepend('<p>Está movimentação é parcelada. Deletar apenas uma irá mudar a estrutura das parcelas.</p>');
            }
            $('#modalConfirmaAutoClick').click();
            $('#modalFormConfirma').attr('action','../back/cadMov.php?apagar='+id);
        }
        function deleteMov(){
            if($('#dtId').val()>0 && $('#dtId').val()!=null){
                if($('#divDtParcela').hasClass('d-none')) deleteFinal($('#dtId').val(),false);
                else deleteFinal($('#dtId').val(),true);
            }
            else msg(0,['Houve um problema com os detalhes desta movimentação. Tente novamente ou registre o problema para o Desenvolvedor.']);
        }
        function formatEdit(){
            $('#btnDtDelete,#btnDtEfetuar,#btnDtEdit').addClass('d-none');
            $('#btnDtEditCancel,#btnDtEditSalve').removeClass('d-none');
            //Ações que Parceladas Não Executam
            if($('#divDtParcela').hasClass('d-none')){
                formatEditTipo();
                $('#dtData').removeAttr('readonly').removeClass('bg-light');
                $('#dtValor').removeAttr('readonly').removeClass('bg-light');
                $('#dtCategoria').removeAttr('readonly').removeClass('bg-light');
            }
            else{
                $('#dtAlertParcela').removeClass('d-none');
                $('#divDtParcela').addClass('d-none');
            }
            $('#dtDescricao').removeAttr('readonly').removeClass('bg-light'); $('#dtDescricao').closest('div').removeClass('d-none');
            $('#dtObs').removeAttr('readonly').removeClass('bg-light'); $('#divDtObs').removeClass('d-none');
            if($('#dtStatus').val()=='Finalizado'){
                $('#btnDtStatus').removeClass('text-danger text-success');
                $('#btnDtStatus').removeClass('d-none').html('Status: '+$('#dtStatus').val()).addClass($('#dtStatus').val()=='Pendente'?'text-danger':'text-success');
                $('#dtCloseModal').addClass('d-none');
            }else{ $('#dtStatus').val(''); }
        }
        function formatEditTipo(){
            temp = $('#dtTipo').val();
            $('#btnDtTipo').removeClass('btn-danger btn-success');
            $('#btnDtTipo').html(temp=='Receita'?'Receita':'Despesa').addClass(temp=='Receita'?'btn-success':'btn-danger');
            $('#dtDdTipo').removeClass('text-danger text-success');
            $('#dtDdTipo').html(temp=='Receita'?'Despesa':'Receita').addClass(temp=='Receita'?'text-danger':'text-success');
            $('#divDtDdTipo').removeClass('d-none');
        }
        function altMovDetalhe(p){
            switch(p){
                case 'tipo':
                    $('#dtTipo').val($('#dtDdTipo').html());
                    formatEditTipo();
                    break;
            }
            
        }
        function altStatusDt(){
            if($('#dtStatus').val()=='Pendente') $('#dtStatus').val('Finalizado');            
            else $('#dtStatus').val('Pendente');
            $('#btnDtStatus').removeClass('text-danger text-success');
            $('#btnDtStatus').html('Status: '+$('#dtStatus').val()).addClass($('#dtStatus').val()=='Pendente'?'text-danger':'text-success');
        }
        function formatDetalheMult(pid,pdata,ptipo,pvalor,pdescricao,pcategoria,pparcela,pnumParc,prelevancia,pobs,pstatus){
            /*Id*/ $('#dtId').val(pid);
            /*Data*/ $('#dtData').val(pdata);
            /*Tipo*/
            $('#btnDtTipo').removeClass('btn-success btn-danger'); $('#dtTipo').val(ptipo);
            $('#btnDtTipo').html(ptipo).addClass(ptipo=='Receita'?'btn-success':'btn-danger');
            /*Valor*/ $('#dtValor').val(pvalor);
            /*Descricao*/ 
            $('#dtDescricao').val(pdescricao);
            $('#modalDetalhe .modal-title').html("Detalhe > "+pdescricao);
            /*Categoria*/ $("#dtCategoria").val(pcategoria);
            /*Parcela*/
            if(pparcela){
                $('#dtParcela').html(pnumParc);
                $('#divDtParcela').removeClass('d-none');
            }
            else $('#divDtParcela').addClass('d-none');
            /*Relevancia*/ regulaRel(prelevancia,'dt');                                  
            /*Obs*/
            if(pobs.length>0){
                $('#divDtObs').removeClass('d-none');
                $('#dtObs').val(pobs);
            }else $('#divDtObs').addClass('d-none');
            /*Status*/ $('#btnDtEfetuar').html(ptipo=='Receita'?'Receber':'Pagar');
            if(pstatus){
                $('#dtStatus').val('Finalizado');
                $('#btnDtEfetuar').addClass('d-none');
            } 
            else{
                $('#dtStatus').val('Pendente');
                $('#btnDtEfetuar').removeClass('d-none');
            }
        }
        $(function(){
        <?php
        if(isset($_POST['editMult'])){
            $sql="select * from movimento where usuario_id='{$_SESSION['pctrl_user']}' and id='{$_POST['editMult']}';";
            if($r = enviarComand($sql,'bd_pctrl')->fetch_assoc()){
        ?>
            formatDetalheMult(<?php echo $r['id'].",'".$r['data_']."','".$r['tipo']."','".$r['valor']."','".$r['descricao']."',".$r['categoria_id'].",".($r['parcela_id']?$r['parcela_id']:'null').",'".$r['parcela']."',".$r['relevancia'].",'".$r['obs']."',".$r['status']; ?>);
//            formatEdit();
            $('#btnChamaDetalhe').click();
        <?php }else{ echo " msg(0,['Houve um erro, a música não foi selecionada corretamente.']); "; }} ?> 
        });
    </script>
    <!--Modal Detalhes--> <button type="button" class="d-none" data-toggle="modal" data-target="#modalDetalhe" id="btnChamaDetalhe"></button>
    <div class="modal fade" tabindex="-1" role="dialog" id="modalDetalhe" aria-labelledby="">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalhes</h5>
                    <button type="button" class="close" aria-label="Close" data-dismiss="modal" id="dtCloseModal"> <span aria-hidden="true">&times</span> </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="../back/cadMov.php?update" id="dtForm">
                        <!--Tipo de Movimentação-->
                        <div class="btn-group btn-block">
                            <button class="btn active rounded" type="button" id="btnDtTipo" name="btnDtTipo"></button>
                            <!--DropDown Tipo-->
                            <div class="dropdown d-none" id="divDtDdTipo">
                                <button class="btn btn-light dropdown-toggle" type="button" id="btnDtDdTipo" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" onclick="altMovDetalhe('tipo')" id="dtDdTipo"></a>
                                </div>
                            </div>
                            <input type="hidden" id="dtTipo" name="dtTipo" value="">
                        </div>
                        <!--Conteudo do Formulário-->
                        <div class="card mt-2 p-2">
                            <!--Alerta sobre Restrições de Parcelas-->
                            <div class="alert alert-danger alert-dismissible d-none" role="alert" id="dtAlertParcela">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                Funcionalidades como, alterar Valor, alterar Data e Categoria de contas Parceladas, só estão disponíveis na área de Parcelas. <a href="#" class="text-decoration text-danger active font-weight-bold" title="Ir para a área de Parcelas">Redirecionar</a>
                            </div>
                            <input type="hidden" id="dtId" name="dtId">
                            <!--Data-->
                            <div class="input-group mb-2">
                                <input type="date" class="form-control bg-light" id="dtData" name="dtData" readonly>
                            </div>
                            <!--Valor-->
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-transparent" style="font-weight: 500">R$</span>
                                </div>
                                <input type="text" size="13" maxlength="13" class="form-control bg-light" id="dtValor" name="dtValor" onkeydown="FormataMoeda(this,10,event)" onkeypress="return maskKeyPress(event)" onchange="calcTotalParc();" readonly>
                            </div>
                            <!--Descrição-->
                            <div class="input-group mb-2 d-none">
                                <input type="text" class="form-control bg-light" id="dtDescricao" name="dtDescricao" readonly>
                            </div>
                            <!--Categoria-->
                            <div class="card mb-2">
                                <div class="d-block text-center text-muted small">Categoria:</div>
                                <div class="input-group">
                                    <select class="custom-select border-top-0 border-right-0 border-left-0 border-bottom-0" id="dtCategoria" name="dtCategoria">
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
                            <!--Parcela-->
                            <div class="card mb-2" id="divDtParcela">
                                <div class="d-block text-center text-muted small">Parcela:</div>
                                <div class="rounded text-center border-top" style="background: rgba(210,210,220,.2);" id="dtParcela"></div>
                            </div>
                            <!--Observação-->
                            <div class="card mb-2 d-none" id="divDtObs">
                                <div class="d-block text-center text-muted small">Obs:</div>
                                <div class="input-group">
                                    <textarea class="form-control bg-ligth" rows="3" id="dtObs" name="dtObs" readonly></textarea>
                                </div>
                            </div>
                            <!--Relevancia-->
                            <div class="card mb-2">
                                <div class="d-block text-center text-muted small">Relevância:</div>
                                <div class="input-group">
                                    <div class="d-block mx-auto">
                                        <button type="button" class="btn btn-star pt-0 pb-1" id="dtRel1" onclick="regulaRel(1,'dt');">&#9733</button>
                                        <button type="button" class="btn btn-star pt-0 pb-1" id="dtRel2" onclick="regulaRel(2,'dt');">&#9733</button>
                                        <button type="button" class="btn btn-star pt-0 pb-1" id="dtRel3" onclick="regulaRel(3,'dt');">&#9733</button>
                                        <button type="button" class="btn btn-star pt-0 pb-1" id="dtRel4" onclick="regulaRel(4,'dt');">&#9733</button>
                                        <button type="button" class="btn btn-star pt-0 pb-1" id="dtRel5" onclick="regulaRel(5,'dt');">&#9734</button>
                                    </div>
                                    <input type="hidden" id="dtRelevancia" name="dtRelevancia">
                                </div>
                            </div>
                            <button type="button" class="btn btn-block btn-light active font-weight-bold btn-sm d-none" id="btnDtStatus" onclick="altStatusDt()"></button>
                            <input type="hidden" id="dtStatus" name="dtStatus">
                        </div>
                        <!--Button Finalizar-->
                        <div class="btn-group mt-2 btn-block">
                            <button class="btn btn-danger mx-1 rounded px-0 ml-auto" type="button" id="btnDtDelete" name="btnDtDelete" title="Apagar" style="max-width: 50px;" onclick="deleteMov()"><i class="material-icons align-bottom">delete_forever</i></button>
                            <button class="btn btn-info rounded" type="button" data-toggle="modal" data-target="#modalPagar" onclick="formatPg(0,'Detalhe')" id="btnDtEfetuar" name="btnDtEfetuar" style="max-width: 150px;"></button>
                            <button class="btn btn-dark mx-1 rounded px-0 mr-auto" type="button" id="btnDtEdit" name="btnDtEdit" title="Editar" style="max-width: 50px;" onclick="formatEdit()"><i class="material-icons align-bottom">edit</i></button>
                            <button class="btn btn-danger mx-1 font-weight-bolder rounded d-none" type="button" id="btnDtEditCancel" name="btnDtEditCancel" style="max-width: 50px;" title="Cancelar Edição" onclick="reload()">&larr;</button>
                            <button class="btn btn-light mx-1 text-success font-weight-bold rounded d-none" type="submit" id="btnDtEditSalve" name="btnDtEditSalve" title="Salvar Edição">Salvar Edição</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>