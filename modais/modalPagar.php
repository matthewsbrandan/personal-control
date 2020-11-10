    <!--Modal Pagar-->
    <div class="modal fade" tabindex="-1" role="dialog" id="modalPagar" aria-labelledby="#pagarConta">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pagar/Receber</h5>
                    <button type="button" class="close" aria-label="Close" data-dismiss="modal"> <span aria-hidden="true">&times;</span> </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="../back/cadMov.php?pagar<?php echo (isset($_GET['calendarM'])?'&calendarM='.$_GET['calendarM']:'').(isset($_GET['calendarY'])?'&calendarY='.$_GET['calendarY']:''); ?>">
                        <div class="d-block text-center mb-2">
                            <h5 class="text-danger" id="pagarValor">R$ 0,00</h5>
                        </div>
                        <input type="hidden" id="pagarId" name="pagarId">
                        <input type="hidden" id="pagarDiv" name="pagarDiv" value="divPendente">
                        <div class="input-group mb-2">
                            <select class="custom-select" id="pagarConta" name="pagarConta">
                            <?php
                                $sqlConta = "select * from conta where usuario_id='{$_SESSION['pctrl_user']}';";
                                $dataConta = enviarComand($sqlConta,'bd_pctrl');
                                while($lConta = $dataConta->fetch_assoc()){
                            ?>
                                <option value="<?php echo $lConta['id'];?>"><?php echo $lConta['nome'];?></option>
                            <?php } ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-info btn-block">Finalizar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>