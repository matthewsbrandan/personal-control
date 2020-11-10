    <!--Modal Preference-->
    <div class="modal fade" tabindex="-1" role="dialog" id="modalPreference" aria-labelledby="">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="material-icons align-calendar">fingerprint</i>
                        Sua Identidade
                    </h5>
                    <button type="button" class="close" aria-label="Close" data-dismiss="modal"> <span aria-hidden="true">&times;</span> </button>
                </div>
                <div class="modal-body p-2">
                    <div class="card text-center">
                      <div class="card-header">
                        Seu Perfil
                      </div>
                      <div class="card-body">
                        <?php
                          $sql="select * from usuario where id='{$_SESSION['pctrl_user']}';";
                          $data = enviarComand($sql,'bd_pctrl');
                          $res = $data->fetch_assoc();
                        ?>
                        <h5 class="card-title"><?php echo $res['nome'].' '.$res['sobrenome']; ?></h5>
                        <p class="card-text pb-0 mb-0"><?php echo 'E-mail: '.$res['email']; ?></p>
                        <p class="card-text"><?php echo 'Celular: '.$res['celular']; ?></p>
                        <a href="#" class="btn btn-primary">Editar</a>
                      </div>
                      <div class="card-footer text-muted">
                        PCtrl
                      </div>
                    </div>
                </div>
                <div class="modal-footer p-3 px-4">
                <a href="#" class="btn btn-sm btn-block btn-danger font-weight-bold" data-toggle="modal" data-target="#modalConfirma" onclick="direcionaConfirmacao('reset')">RESET</a>
                </div>
            </div>
        </div>
    </div>