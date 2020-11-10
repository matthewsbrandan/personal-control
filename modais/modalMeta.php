    <!--Modal Meta-->
    <div class="modal fade" tabindex="-1" role="dialog" id="modalMeta" aria-labelledby="#metaValor">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Meta de <?php echo $mesCalendar[isset($_GET['calendarM'])?$_GET['calendarM']:intval(date('m'))]; ?> <i class="material-icons align-middle">show_chart</i></h5>
                    <button type="button" class="close" aria-label="Close" data-dismiss="modal"> <span aria-hidden="true">&times;</span> </button>
                </div>
                <div class="modal-body pt-2">
                    <form method="POST" action="../back/cadMov.php?meta">
                        <div class="d-block text-center mb-2">
                            <small class="p-0 m-0 font-weight-bold">Atual</small>
                            <h4 class="text-warning pt-0 mt-0">
                                <?php
                                    if(!(isset($_GET['calendarM'])&&isset($_GET['calendarY']))) $mesSelect = date('Y-m').'-01';
                                    else $mesSelect = $searchY.'-'.($searchM<10?'0'.$searchM:$searchM).'-01';
                                    $sql= "select if(sum(meta),sum(meta),0.00) meta from caixa where mesano='$mesSelect' and usuario_id={$_SESSION['pctrl_user']} limit 1;";
                                    $resMeta = (enviarComand($sql,'bd_pctrl')->fetch_assoc());
                                    echo 'R$ '.number_format($resMeta['meta'],2,',','.');
                                ?>
                            </h4>
                        </div>
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-transparent" style="font-weight: 500">R$</span>
                            </div>
                            <input type="text" size="13" maxlength="13" class="form-control" id="metaValor" name="metaValor" onkeydown="FormataMoeda(this,10,event)" onkeypress="return maskKeyPress(event)" placeholder="Digite o Valor...">
                            <input type="hidden" id="metaData" name="metaData" value="<?php echo $mesSelect; ?>">
                        </div>
                        <button type="submit" class="btn btn-warning text-light btn-block">Finalizar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>