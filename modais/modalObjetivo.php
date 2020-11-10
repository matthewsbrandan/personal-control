    <!--Modal Objetivo-->
    <div class="modal fade" tabindex="-1" role="dialog" id="modalObjetivo" aria-labelledby="#objNome">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Objetivo</h5>
                    <button type="button" class="close" aria-label="Close" data-dismiss="modal"> <span aria-hidden="true">&times</span> </button>
                </div>
                <div class="modal-body">
                    <!--ALERTAS-->
                    <div>
                        <?php 
                            if(isset($_GET['obj'])&&($_GET['obj']==1)){
                        ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                          <strong>Objetivo</strong> adicionado com Sucesso!
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <?php 
                            }else 
                            if(isset($_GET['obj'])&&($_GET['obj']==2)){
                        ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                          <strong>Erro de Cadastramento</strong> de Objetivo!
                          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        </div>
                        <?php } ?>
                    </div>
                    <form method="POST" action="../back/cadMov.php?objetivo">
                        <button type="button" class="btn btn-primary btn-block">Novo Objetivo</button>
                        <!--Conteudo do Formulário-->
                        <div class="card mt-2 p-2" id="card-obj" style="border-color: rgba(40, 167, 69,.4);">
                            <!--Nome-->
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" placeholder="Nome do Objetivo..." id="objNome" name="objNome">
                            </div>
                            <!--Valor-->
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <span class="input-group-text bg-transparent" style="font-weight: 500">R$</span>
                                </div>
                                <input type="text" size="13" maxlength="13" class="form-control" id="objValor" name="objValor" onkeydown="FormataMoeda(this,10,event)" onkeypress="return maskKeyPress(event)" placeholder="Digite o Valor...">
                            </div>
                            <!--Relevancia-->
                            <div class="card mb-2">
                                <div class="d-block text-center text-muted small">Relevância:</div>
                                <div class="input-group">
                                    <div class="d-block mx-auto">
                                        <button type="button" class="btn btn-star pt-0 pb-1" id="objRel1" onclick="regulaRel(1,'obj');">&#9733</button>
                                        <button type="button" class="btn btn-star pt-0 pb-1" id="objRel2" onclick="regulaRel(2,'obj');">&#9733</button>
                                        <button type="button" class="btn btn-star pt-0 pb-1" id="objRel3" onclick="regulaRel(3,'obj');">&#9733</button>
                                        <button type="button" class="btn btn-star pt-0 pb-1" id="objRel4" onclick="regulaRel(4,'obj');">&#9733</button>
                                        <button type="button" class="btn btn-star pt-0 pb-1" id="objRel5" onclick="regulaRel(5,'obj');">&#9734</button>
                                    </div>
                                    <input type="hidden" id="objRelevancia" name="objRelevancia" value="4">
                                </div>
                            </div>
                        </div>
                        <!--Button Finalizar-->
                        <div class="btn-group btn-block mt-2">
                            <button class="btn btn-outline-light text-success border border-light active" type="submit" id="btnCadastrarObj" name="btnCadastrarObj">Adicionar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>