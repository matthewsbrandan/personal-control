    <script>
        function validaFormMov(frm){
            retorno = false;
            if($('#movValor').val()==0){
                msg(0,['Preencha o Valor da Movimentação. Obs. valor deve ser Diferente de Zero!']);
            }else if($('#divTransferencia').css("display")=="none") if($('#movDescricao').val().length<3){
                msg(0,['Adicione uma Descrição para a Movimentação, com pelo menos 3 caracteres.']);
            }else{ retorno = true; }else retorno = true;
            if(retorno) frm.submit();
        }
    </script>
    <!--Modal Movimento-->
    <div class="modal fade" tabindex="-1" role="dialog" id="modalMovimento" aria-labelledby="#movReceita">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cadastrar Movimentação</h5>
                    <button type="button" class="close" aria-label="Close" data-dismiss="modal"> <span aria-hidden="true">&times</span> </button>
                </div>
                <div class="modal-body">
                    <!--ALERTAS-->
                    <div>
                        <?php 
                            if((isset($_GET['cat'])&&($_GET['cat']==1))   ||
                               (isset($_GET['cont'])&&($_GET['cont']==1)) ||
                               (isset($_GET['mov'])&&($_GET['mov']==1)))   {
                        ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                          <strong><?php if(isset($_GET['cat'])) echo 'Categoria'; elseif(isset($_GET['cont'])) echo 'Conta'; else echo 'Movimentação'; ?></strong> cadastrada com Sucesso!
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <?php 
                            }else 
                            if((isset($_GET['cat'])&&($_GET['cat']==2))   ||
                               (isset($_GET['cont'])&&($_GET['cont']==2)) ||
                               (isset($_GET['mov'])&&($_GET['mov']==2)))   {
                        ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                          <strong>Erro de Cadastramento</strong> de <?php if(isset($_GET['cat'])) echo 'Categoria'; elseif(isset($_GET['cont'])) echo 'Conta'; else echo 'Movimentação'; ?>!
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <?php } ?>
                    </div>
                    <form method="POST" id="form-mov" action="../back/cadMov.php?movimento" onsubmit="validaFormMov(this); return false;">
                        <!--Tipo de Movimentação-->
                        <div class="btn-group btn-block">
                            <button class="btn btn-outline-light text-success btn-mov active" type="button" id="movReceita" name="movReceita" onclick="tipoMov('Receita');">Receita</button>
                            <button class="btn btn-outline-light text-danger btn-mov" type="button" id="movDespesa" name="movDespesa" onclick="tipoMov('Despesa');">Despesa</button>
                            <button class="btn btn-outline-light text-primary btn-mov" type="button" id="movTransferencia" name="movTransferencia" onclick="tipoMov('Transferencia');">Transferência</button>
                            <div class="btn-group dropleft">
                                <button type="button" class="btn btn-outline-light text-dark px-0 rounded-right" style="max-width: 30px;" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="dropdownMais"><i class="material-icons align-middle">more_vert</i></button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMais">
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalCategoria">Nova Categoria</a>
                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modalConta">Nova Conta</a>
                                    <a class="dropdown-item" href="#" onclick="$('#modalIntable').modal('show');">Inserção em Tabela</a>
                                    <a class="dropdown-item" href="#" onclick="$('#modalCalculadoraAutoClick').click();">Calculadora</a>
                                </div>
                            </div>
                            <input type="hidden" id="movTipo" name="movTipo" value="Receita">
                        </div>
                        <!--Conteudo do Formulário-->
                        <div class="card mt-2 p-2" id="card-mov" style="border-color: rgba(40, 167, 69,.4);">
                            <!--Transferência-->
                            <div class="card mb-2" style="display: none;" id="divTransferencia">
                                <div class="row">
                                    <div class="col d-block text-center text-muted small">De Conta:</div>
                                    <div class="col d-block text-center text-muted small">Para Conta:</div>
                                </div>
                                <div class="row">
                                        <div class="col input-group m-0 pr-1">
                                            <select class="custom-select border-right-0 border-left-0 border-bottom-0" id="movTransfereDe" name="movTransfereDe">
                                            <?php
                                                $sqlConta = "select * from conta where usuario_id='{$_SESSION['pctrl_user']}';";
                                                $dataConta = enviarComand($sqlConta,'bd_pctrl');
                                                while($lConta = $dataConta->fetch_assoc()){
                                            ?>
                                                <option value="<?php echo $lConta['id'];?>"><?php echo $lConta['nome'];?></option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col input-group m-0 pl-1">
                                            <select class="custom-select border-right-0 border-left-0 border-bottom-0" id="movTransferePara" name="movTransferePara">
                                            <?php
                                                $sqlConta = "select * from conta where usuario_id='{$_SESSION['pctrl_user']}';";
                                                $dataConta = enviarComand($sqlConta,'bd_pctrl');
                                                while($lConta = $dataConta->fetch_assoc()){
                                            ?>
                                                <option value="<?php echo $lConta['id'];?>" selected><?php echo $lConta['nome'];?></option>
                                            <?php } ?>
                                            </select>
                                        </div>
                                </div>
                            </div>
                            <!--Dados Movimento Geral-->
                            <div>                            
                                <!--Data-->
                                <div class="input-group mb-2">
                                    <input type="date" class="form-control" id="movData" name="movData" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                                <!--Valor-->
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-transparent" style="font-weight: 500">R$</span>
                                    </div>
                                    <input type="text" size="13" maxlength="13" class="form-control" id="movValor" name="movValor" onkeydown="FormataMoeda(this,10,event)" onkeypress="return maskKeyPress(event)" placeholder="Digite o Valor..." onchange="calcTotalParc();">
                                </div>
                                <!--Descrição-->
                                <div class="input-group mb-2" id="divDescricao">
                                    <input type="text" class="form-control" placeholder="Descrição da Conta..." id="movDescricao" name="movDescricao">
                                </div>
                                <!--Categoria-->
                                <div class="card mb-2" id="divSelectCategoria">
                                    <div class="d-block text-center text-muted small">Categoria:</div>
                                    <div class="input-group">
                                        <select class="custom-select border-top-0 border-right-0 border-left-0 border-bottom-0" id="movCategoria" name="movCategoria">
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
                                <!--Relevancia-->
                                <div class="card mb-2">
                                    <div class="d-block text-center text-muted small">Relevância:</div>
                                    <div class="input-group">
                                        <div class="d-block mx-auto">
                                            <button type="button" class="btn btn-star pt-0 pb-1" id="movRel1" onclick="regulaRel(1,'mov');">&#9733</button>
                                            <button type="button" class="btn btn-star pt-0 pb-1" id="movRel2" onclick="regulaRel(2,'mov');">&#9733</button>
                                            <button type="button" class="btn btn-star pt-0 pb-1" id="movRel3" onclick="regulaRel(3,'mov');">&#9733</button>
                                            <button type="button" class="btn btn-star pt-0 pb-1" id="movRel4" onclick="regulaRel(4,'mov');">&#9733</button>
                                            <button type="button" class="btn btn-star pt-0 pb-1" id="movRel5" onclick="regulaRel(5,'mov');">&#9734</button>
                                        </div>
                                        <input type="hidden" id="movRelevancia" name="movRelevancia" value="4">
                                    </div>
                                </div>
                                <!--Status-->
                                <div>
                                    <button type="button" class="btn btn-danger btn-block btn-sm mb-1" id="btnStatus" name="btnStatus" onclick="alternaStatus();">Status Pendente!</button>
                                    <input type="hidden" id="movStatus" name="movStatus" value=false>
                                    <div class="card my-2 d-none" id="divSelectConta">
                                        <div class="d-block text-center text-muted small">Conta:</div>
                                        <div class="input-group">
                                            <select class="custom-select border-top-0 border-right-0 border-left-0 border-bottom-0" id="movConta" name="movConta">
                                                <?php
                                                    $sqlConta = "select * from conta where usuario_id='{$_SESSION['pctrl_user']}';";
                                                    $dataConta = enviarComand($sqlConta,'bd_pctrl');
                                                    while($lConta = $dataConta->fetch_assoc()){
                                                ?>
                                                <option value="<?php echo $lConta['id'];?>"><?php echo $lConta['nome'];?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <!--Observação-->
                                <div>
                                    <button type="button" class="btn btn-primary btn-block btn-sm" id="btnAddObs" name="btnAddObs" onclick="alternaObs();">Adicionar Observação?</button>
                                    <div class="input-group mb-2" style="display: none;" id="divObs">
                                        <textarea class="form-control" rows="3" id="movObs" name="movObs"></textarea>
                                    </div>
                                </div>
                            </div>
                            <!--Conteudo de Parcelas-->
                            <div class="card mt-2 p-2 d-none" id="card-parc">
                                <!--Rotatividade-->
                                <div class="card mb-2">
                                    <div class="d-block text-center text-muted small">Rotatividade (a cada):</div>
                                    <div class="input-group">
                                        <select class="custom-select border-top-0 border-right-0 border-left-0 border-bottom-0" id="parcRotatividade" name="parcRotatividade">
                                            <option value="Diário">Diário (1 dia)</option>
                                            <option value="Semanal">Semanal (7 dias)</option>
                                            <option value="Quinzenal">Quinzenal (15 dias)</option>
                                            <option value="Mensal" selected>Mensal (1 mês)</option>
                                            <option value="Bimestral">Bimestral (2 meses)</option>
                                            <option value="Trimestral">Trimestral (3 meses)</option>
                                            <option value="Semestral">Semestral (6 meses)</option>
                                            <option value="Anual">Anual (1 ano)</option>
                                        </select>
                                    </div>
                                </div>
                                <!--Quantidade-->
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-transparent" style="font-weight: 500">Quantidade:</span>
                                    </div>
                                    <input type="number" class="form-control" value="2" min="2" max="31" id="parcQtd" name="parcQtd" onchange="calcTotalParc();">
                                </div>
                                <!--Valor-->
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-transparent" style="font-weight: 500">Total (R$)</span>
                                    </div>
                                    <input type="text" size="13" maxlength="13" class="form-control bg-light" id="parcTotal" onkeydown="FormataMoeda(this,10,event)" onkeypress="return maskKeyPress(event)" value="" readonly>
                                </div>
                                <input type="hidden" id="movTemParcela" name="movTemParcela" value="false">
                                <button class="btn btn-sm btn-outline-light text-danger border border-light active p-0" type="button" onclick="alternaParcela()">&times</button>
                            </div>
                        </div>
                        <!--Button Finalizar-->
                        <div class="btn-group btn-block mt-2">
                            <button class="btn btn-outline-light text-success border border-light active" type="submit" id="btnCadastrarMov" name="btnCadastrarMov">Cadastrar</button>
                            <button class="btn btn-outline-light text-warning border border-light" type="button" id="btnParcelarMov" onclick="alternaParcela()">Parcelada</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>