<script>
    function editeFormGerenciarCategoria(id,nome){
        $('#fgcNome').val(nome);
        $('#divFgcNome').removeClass('d-none');
        $('#ulFgc').addClass('d-none');
        $('#fgcId').val(id);
        $('#fgcAcao').val('editar');
    }
    function finalizaFormGerenciarCategoria(id,extra,acao){
        if(acao=="editar"){
            if($('#fgcNome').val().length>0){
                $('#formGerenciarCategoria').submit();
            }else msg(0,['Preencha o Campo!']);
        }else
        if(acao=="deletar"){
            $('#fgcId').val(id);
            $('#fgcAcao').val(acao);
            if(extra==0){
                $('#formGerenciarCategoria').submit();
            }else{
                $('#alertGerenciarCategoria').show();
                $('.fgcSubstituir').removeClass('d-none');
                $('.fgcOuther').addClass('d-none');
                $('#liFgc'+id).addClass('d-none');
            }
        }else
        if(acao=="substitui"){ 
            $('#fgcAcao').val(acao);
            $('#fgcSubstitui').val(id);
            $('#formGerenciarCategoria').submit();
        }
    }
</script>
    <!--Modal Categoria--> <button type="button" class="d-none" data-toggle="modal" data-target="#modalGerenciarCategoria" id="btnGerenciarCategoria"></button>
    <div class="modal fade" tabindex="-1" role="dialog" id="modalGerenciarCategoria" aria-labelledby="#catNome">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Gerenciamento de Categoria</h5>
                    <button type="button" class="close" aria-label="Close" data-dismiss="modal"> <span aria-hidden="true">&times;</span> </button>
                </div>
                <div class="modal-body" style="max-height: 480px; overflow: auto;">
                    <form method="POST" action="../back/cadMov.php?gerenciarCategoria" id="formGerenciarCategoria">
                        <div class="alert alert-danger small collapse" role="alert" id="alertGerenciarCategoria">
                            Existem Movimentações ligadas a está Categoria, para excluí-la selecione outra categoria para receber essas Movimentações, ou <a class="alert-link pointer" onclick="reload();">clique aqui</a> para Cancelar.
                        </div>
                        <ul class="list-group px-2" id="ulFgc">
                    <?php
                        $sql = "select id,nome from categoria where usuario_id='{$_SESSION['pctrl_user']}';";
                        $data = enviarComand($sql,'bd_pctrl');
                        while($res = $data->fetch_assoc()){
                            $qtd = 0;
                            $qtd = enviarComand("select count(*) qtd from movimento where categoria_id='{$res['id']}';",'bd_pctrl')->fetch_assoc()['qtd'];
                    ?>
                        <li class="list-group-item text-truncate py-2" id="liFgc<?php echo $res['id']; ?>">
                            <div class="row">
                                <div class="col-8">
                                    <span class="badge badge-info" title="Quantidade de Movimentações">
                                    <?php echo $qtd; ?>
                                    </span>
                                    <?php echo $res['nome'];?>
                                </div>
                                <div class="col-4 text-right">
                                <?php if($res['nome']!="Transferências"&&$res['nome']!="Outros"&&$res['nome']!="Margem de Erro") { ?>
                                    <span class="material-icons text-primary bg-light rounded border fgcOuther" title="Editar Categoria" onclick="editeFormGerenciarCategoria(<?php echo $res['id'].",'".$res['nome']; ?>');">create</span>
                                    <span class="material-icons bg-danger text-light rounded fgcOuther" title="Deletar Categoria" onclick="finalizaFormGerenciarCategoria(<?php echo $res['id'].",".$qtd; ?>,'deletar');">delete</span>
                                <?php }else{ ?>
                                    <span class="material-icons text-light bg-secondary rounded fgcOuther" title="Não Podem ser Alteradas" onclick="msg(0,['Não podem ser alteradas, pois são geradas automaticamente!']);">block</span>
                                <?php } ?>
                                    <span class="material-icons bg-success rounded text-light d-none fgcSubstituir" title="Substituir" onclick="finalizaFormGerenciarCategoria(<?php echo $res['id'].",null"; ?>,'substitui');">compare_arrows</span>
                                </div>
                            </div>
                        </li>
                    <?php } ?>
                        </ul>
                        <div class="d-none" id="divFgcNome">
                            <div class="input-group">
                                <input type="text" class="form-control" id="fgcNome" name="fgcNome">
                                <div class="input-group-append rounded">
                                    <span class="input-group-text bg-primary text-light font-weight-bold" onclick="finalizaFormGerenciarCategoria(null,null,'editar');">OK</span>
                                </div>
                            </div>
                            <div class="d-block text-center  mt-2">
                            <a class="btn btn-sm btn-danger text-light  mx-auto" onclick="reload()">Cancelar</a>
                            </div>
                        </div>
                        <input type="hidden" id="fgcAcao" name="fgcAcao">
                        <input type="hidden" id="fgcId" name="fgcId">
                        <input type="hidden" id="fgcSubstitui" name="fgcSubstitui">
                    </form>
                </div>
            </div>
        </div>
    </div>