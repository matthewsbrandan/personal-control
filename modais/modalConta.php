    <!--Modal Conta-->
    <div class="modal fade" tabindex="-1" role="dialog" id="modalConta" aria-labelledby="#contaNome">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Adicionar Nova Conta</h5>
                    <button type="button" class="close" aria-label="Close" data-dismiss="modal"> <span aria-hidden="true">&times;</span> </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="../back/cadMov.php?conta">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="contaNome" name="contaNome" placeholder="Digite o Nome da Nova Conta..." required>
                        </div>
                        <button type="submit" class="btn btn-outline-light btn-block text-primary active">Adicionar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>