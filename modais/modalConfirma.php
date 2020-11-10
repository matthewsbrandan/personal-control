    <!--Modal Confirmação-->
    <button type="button" class="d-none" id="modalConfirmaAutoClick" data-toggle="modal" data-target="#modalConfirma"></button>
    <div class="modal fade" tabindex="-1" role="dialog" id="modalConfirma" aria-labelledby="">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tem Certeza?</h5>
                    <button type="button" class="close" aria-label="Close" data-dismiss="modal"> <span aria-hidden="true">&times;</span> </button>
                </div>
                <div class="modal-body p-2">
                    <form id="modalFormConfirma" name="modalFormConfirma" method="POST">
                        <button type="submit" class="btn btn-primary"> Sim </button>
                        <button type="button" class="btn btn-danger" aria-label="Close" data-dismiss="modal"> Não </button>
                        <div class="mt-1 p-1" id="modalCardConfirma">
                            <p class="text-muted pb-0 mb-0 small"></p>
                        </div>
                    </form>
                </div>                
            </div>
        </div>
    </div>