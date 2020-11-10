<script>
    function submitFormModalMult(valor,caminho){
        v = "<input type='hidden' name='editMult' value='"+valor+"'>";
        $('#formModalMult').append(v);
        $('#formModalMult').attr('action',caminho);
        $('#formModalMult').submit();
    }
</script>
    <!--Modal Multiplas--> <button type="button" class="d-none" data-toggle="modal" data-target="#modalMult" id="btnChamaMult"></button>
    <div class="modal fade" tabindex="-1" role="dialog" id="modalMult" aria-labelledby="">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Descrição da <?php echo isset($_GET['categ'])?'Categoria':'Parcela';?></h5>
                    <button type="button" class="close" aria-label="Close" data-dismiss="modal"> <span aria-hidden="true">&times</span> </button>
                </div>
                <div class="modal-body">
                <form method="post" id="formModalMult"></form>
                <?php
                //PARCELA
                if(isset($_GET['parc'])){
                    $sql = "select p.quantidade, p.total, m.valor, m.tipo, m.descricao, c.nome, p.rotatividade, m.relevancia, m.parcela, m.status, m.data_, m.obs, m.id from movimento m inner join parcela p on m.parcela_id=p.id inner join categoria c on c.id=m.categoria_id where m.parcela_id={$_GET['parc']} and m.usuario_id={$_SESSION['pctrl_user']} order by parcela;";
                    $dataMult = enviarComand($sql,'bd_pctrl');
                    $entrou = 0; $transf = false; $relevancia = 0;
                    while($resMult = $dataMult->fetch_assoc()){ $entrou++;
                       if($entrou==1){ if($resMult['quantidade']==2&&$resMult['total']==0){ $transf = true; }
                ?>           
                <!--Detalhes Gerais-->
                <div class="card p-0 m-0 mb-1">
                    <div class="card-header p-0 m-0">
                        <h5 class="card-title p-0 m-0 text-center <?php if($transf){ echo "text-primary"; }else echo $resMult['valor']>0?'text-success':'text-danger'; ?>"><?php if($transf){ echo "Tranferência"; }else echo $resMult['tipo']; ?></h5>
                    </div>
                    <div class="card-body p-2 pb-0 mb-0">
                        <?php if(!$transf){ ?>
                        <!--Descrição-->
                        <div class="row">
                            <div class="col pl-4">Descrição:</div>
                            <div class="col pr-4 text-right text-muted"><?php echo $resMult['descricao']; ?></div>
                        </div>
                        <!--Categoria-->
                        <div class="row">
                            <div class="col pl-4">Categoria:</div>
                            <div class="col pr-4 text-right text-muted"><?php echo $resMult['nome']; ?></div>
                        </div>
                        <!--Rotatividade-->
                        <div class="row">
                            <div class="col pl-4">Rotatividade:</div>
                            <div class="col pr-4 text-right text-muted"><?php echo $resMult['rotatividade']; ?></div>
                        </div>
                        <!--Quantidade-->
                        <div class="row">
                            <div class="col pl-4">Quantidade:</div>
                            <div class="col pr-4 text-right text-muted" id="multQtdParcela"><?php echo $resMult['quantidade']; ?></div>
                        </div>
                        <?php } ?>
                        <!--Relevancia-->
                        <div class="d-block mx-auto bg-dark rounded text-center mt-2">
                            <button type="button" class="btn btn-star-sm pt-0 pb-1" id="mparcRel1" onclick="regulaRel(1,'mparc');">&#9734</button>
                            <button type="button" class="btn btn-star-sm pt-0 pb-1" id="mparcRel2" onclick="regulaRel(2,'mparc');">&#9734</button>
                            <button type="button" class="btn btn-star-sm pt-0 pb-1" id="mparcRel3" onclick="regulaRel(3,'mparc');">&#9734</button>
                            <button type="button" class="btn btn-star-sm pt-0 pb-1" id="mparcRel4" onclick="regulaRel(4,'mparc');">&#9734</button>
                            <button type="button" class="btn btn-star-sm pt-0 pb-1" id="mparcRel5" onclick="regulaRel(5,'mparc');">&#9734</button>
                        </div>
                        <hr class="m-0 p-0"/>
                        <?php if(!$transf){ ?>
                        <span class="text-muted text-center d-block px-2">R$ <?php echo number_format($resMult['total'],2,',','.'); ?></span>
                        <?php } ?>
                    </div>
                </div>
                <div style="max-height: 308px; overflow: auto;">
                    <?php } ?>
                    <!--Conteúdo Parcela-->
                    <div class="card p-3 mb-1">
                        <!--Movimentação Generalizada-->
                        <div class="row">
                            <div class="col <?php echo $resMult['valor']>0?'text-success':'text-danger'; ?>">R$ <?php echo number_format($resMult['valor'],2,',','.'); ?> </div>
                            <div class="col"> <?php echo $transf?$resMult['descricao']:$resMult['parcela']; if($resMult['status']){ ?>
                                <i class="material-icons align-bottom <?php echo $resMult['valor']>0?'text-success':'text-danger'; ?>">check_circle</i>
                                <?php } $relevancia += $resMult['relevancia']; ?>
                            </div>
                            <i class="material-icons" data-toggle="collapse" data-target="#multCollapse<?php echo $entrou; ?>" aria-expanded="false" aria-controls="multCollapse<?php echo $entrou; ?>" id="btnMultCollapse<?php echo $entrou; ?>" onclick="if($('#btnMultCollapse<?php echo $entrou; ?>').html()=='chevron_right') $('#btnMultCollapse<?php echo $entrou; ?>').html('chevron_left'); else $('#btnMultCollapse<?php echo $entrou; ?>').html('chevron_right'); ">chevron_right</i>
                        </div>
                        <!--Detalhes-->
                        <div class="bg-light border rounded p-2 mt-2 collapse" id="multCollapse<?php echo $entrou; ?>">
                            <p class="mb-1 small border rounded p-1 text-center font-weight-bold" title="Descrição"><?php echo $resMult['descricao']; ?></p>
                            <?php if(strlen($resMult['parcela'])>0){ ?>
                            <p class="mb-1 small border rounded p-1 text-center" title="Data"><b class="pr-2">Data:</b><?php echo date('d/m/Y',strtotime($resMult['data_'])); ?></p>
                            <?php }if(strlen($resMult['obs'])>0){ ?>
                            <p class="mb-1 small border rounded p-1" title="Observação"><b>Observação</b><br/><?php echo $resMult['obs']; ?></p>
                            <?php } ?>
                            <div class="d-block mx-auto text-center" title="Relevância">
                                <?php for($cr=1;$cr<=5;$cr++){ ?>
                                <span class="btn-star-sm pt-0 pb-1"><?php echo $resMult['relevancia']>=$cr?'&#9733':'&#9734'; ?></span>
                                <?php } ?>
                            </div>
                            <div class="btn-group mt-2 btn-block">
                                <button class="btn btn-danger btn-sm mx-1 rounded px-0 ml-auto" type="button" title="Apagar" style="max-width: 50px;" onclick="deleteFinal(<?php echo $resMult['id'].',true'; ?>);"><i class="material-icons align-bottom">delete_forever</i></button>
                                <button class="btn btn-info btn-sm rounded" type="button" data-toggle="modal" data-target="#modalPagar" onclick="formatPg(0,'Detalhe')"><?php echo ($resMult['status']?"Remover":"Realizar").($resMult['tipo']=="Receita"?" Recebimento":" Pagamento"); ?></button>
                                <button class="btn btn-dark btn-sm mx-1 rounded px-0 mr-auto" type="button" title="Editar" style="max-width: 50px;" onclick="submitFormModalMult(<?php echo $resMult['id']; ?>,'index.php#divParcela');"><i class="material-icons align-bottom">edit</i></button>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <input type="hidden" id="multRelevanciaP" value="<?php echo $relevancia; ?>"> <?php if($entrou!=0){ ?>
                </div>
                <div class="btn-group mt-2 btn-block">
                    <button class="btn btn-danger btn-sm mx-1 rounded px-0 ml-auto" type="button" title="Apagar" onclick="msg(0,['Em Desenvolvimento']);"><i class="material-icons align-bottom">delete_forever</i></button>
                    <button class="btn btn-dark btn-sm mx-1 rounded px-0 mr-auto" type="button" title="Editar" onclick="msg(0,['Em Desenvolvimento']);"><i class="material-icons align-bottom">edit</i></button>
                </div>    
                <?php } }
                //CATEGORIA
                if(isset($_GET['categ'])){
                    $sql = "select c.nome,m.data_,m.valor,m.relevancia,m.descricao,m.status,m.parcela,m.obs,m.id,m.tipo from movimento m inner join categoria c on m.categoria_id=c.id where m.categoria_id={$_GET['categ']} and m.usuario_id={$_SESSION['pctrl_user']} order by data_;";
                    $dataMult = enviarComand($sql,'bd_pctrl');
                    $entrou = 0; $transf = false; $totalR = 0; $totalD = 0; $relevancia = 0;
                    while($resMult = $dataMult->fetch_assoc()){ $entrou++;
                       if($entrou==1){
                ?>           
                <!--Detalhes Gerais-->
                <div class="card p-0 m-0 mb-1" id="datalhesMultCat">
                    <div class="card-header p-0 m-0">
                        <h5 class="card-title p-0 m-0 text-center"><?php echo $resMult['nome']; ?></h5>
                    </div>
                    <div class="card-body p-2 pb-0 mb-0">
                        <!--Quantidade-->
                        <div class="row" id="multCatQtd">
                            <div class="col pl-4">Quantidade:</div>
                            <div class="col pr-4 text-right text-muted"></div>
                        </div>
                        <!--Relevancia-->
                        <div class="d-block mx-auto bg-dark rounded text-center mt-2">
                            <p class="text-center text-light m-0 p-0">Média de Relevância</p>
                            <button type="button" class="btn btn-star-sm pt-0 pb-1" id="mcatRel1" onclick="regulaRel(1,'mcat');">&#9734</button>
                            <button type="button" class="btn btn-star-sm pt-0 pb-1" id="mcatRel2" onclick="regulaRel(2,'mcat');">&#9734</button>
                            <button type="button" class="btn btn-star-sm pt-0 pb-1" id="mcatRel3" onclick="regulaRel(3,'mcat');">&#9734</button>
                            <button type="button" class="btn btn-star-sm pt-0 pb-1" id="mcatRel4" onclick="regulaRel(4,'mcat');">&#9734</button>
                            <button type="button" class="btn btn-star-sm pt-0 pb-1" id="mcatRel5" onclick="regulaRel(5,'mcat');">&#9734</button>
                        </div>
                        <hr class="m-0 p-0"/>
                        <div class="text-muted text-center d-block px-2" id="multCatAcumulado">
                            <span class="text-success pr-2 border-right">R$ <?php echo '0,00'; ?></span>
                            <span class="text-danger pl-2 border-left">R$ <?php echo '0,00'; ?></span>
                        </div>
                    </div>
                </div>
                <div style="max-height: 308px; overflow: auto;">
                <?php } ?>
                    <!--Conteúdo da Categoria-->
                    <div class="card p-3 mb-1 card-mult-cat">
                        <!--Movimentação Geralizada-->
                        <div class="row">
                            <div class="col"><?php echo date('d/m/Y',strtotime($resMult['data_'])); ?> </div>
                            <div class="col <?php echo $resMult['valor']>0?'text-success':'text-danger'; ?>">
                                <?php
                                   echo 'R$ '.number_format($resMult['valor'],2,',','.');
                                   if($resMult['valor']>0) $totalR += $resMult['valor'];
                                   else                    $totalD += $resMult['valor'];
                                   $relevancia += $resMult['relevancia'];
                                ?>
                            </div>
                            <div class="col text-truncate" title="<?php echo $resMult['descricao']; ?>">
                                <?php if($resMult['status']){ ?>
                                <i class="material-icons align-bottom text-primary">check_circle</i>
                                <?php } echo $resMult['descricao']; ?>
                            </div>
                            <i class="material-icons" data-toggle="collapse" data-target="#multCollapse<?php echo $entrou; ?>" aria-expanded="false" aria-controls="multCollapse<?php echo $entrou; ?>" id="btnMultCollapse<?php echo $entrou; ?>" onclick="if($('#btnMultCollapse<?php echo $entrou; ?>').html()=='chevron_right') $('#btnMultCollapse<?php echo $entrou; ?>').html('chevron_left'); else $('#btnMultCollapse<?php echo $entrou; ?>').html('chevron_right'); ">chevron_right</i>
                        </div>
                        <!--Detalhes-->
                        <div class="bg-light border rounded p-2 mt-2 collapse" id="multCollapse<?php echo $entrou; ?>">
                            <p class="mb-1 small border rounded p-1 text-center font-weight-bold" title="Descrição"><?php echo $resMult['descricao']; ?></p>
                            <?php if(strlen($resMult['parcela'])>0){ ?>
                            <p class="mb-1 small border rounded p-1 text-center" title="Parcela"><b class="pr-2">Nº Parcela:</b><?php echo $resMult['parcela']; ?></p>
                            <?php }if(strlen($resMult['obs'])>0){ ?>
                            <p class="mb-1 small border rounded p-1" title="Observação"><b>Observação</b><br/><?php echo $resMult['obs']; ?></p>
                            <?php } ?>
                            <div class="d-block mx-auto text-center" title="Relevância">
                                <?php for($cr=1;$cr<=5;$cr++){ ?>
                                <span class="btn-star-sm pt-0 pb-1"><?php echo $resMult['relevancia']>=$cr?'&#9733':'&#9734'; ?></span>
                                <?php } ?>
                            </div>
                            <div class="btn-group mt-2 btn-block">
                                <button class="btn btn-danger btn-sm mx-1 rounded px-0 ml-auto" type="button" title="Apagar" style="max-width: 50px;" onclick="deleteFinal(<?php echo $resMult['id'].','.(strlen($resMult['parcela'])>0?true:false); ?>);"><i class="material-icons align-bottom">delete_forever</i></button>
                                <button class="btn btn-info btn-sm rounded" type="button" data-toggle="modal" data-target="#modalPagar" onclick="formatPg(0,'Detalhe')"><?php echo ($resMult['status']?"Remover":"Realizar").($resMult['tipo']=="Receita"?" Recebimento":" Pagamento"); ?></button>
                                <button class="btn btn-dark btn-sm mx-1 rounded px-0 mr-auto" type="button" title="Editar" style="max-width: 50px;" onclick="submitFormModalMult(<?php echo $resMult['id']; ?>,'index.php#divCategoria');"><i class="material-icons align-bottom">edit</i></button>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <input type="hidden" id="multTotalR" value="<?php echo $totalR; ?>">
                    <input type="hidden" id="multTotalD" value="<?php echo $totalD; ?>">
                    <input type="hidden" id="multRelevancia" value="<?php echo $relevancia; ?>">
                    <?php if($entrou==0){ echo "<p class='text-center'>Sem Movimentações Cadastradas nessa Categoria</p>"; }else{ ?>
                </div>
                <?php }} ?>
                </div>
            </div>
        </div>
    </div>