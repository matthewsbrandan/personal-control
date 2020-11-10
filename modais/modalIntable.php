<script>
    function altLabel(elem){
        if(elem.val()==""){
            elem.next().hide('slow');
            elem.next().children('label').html("Selecione a Planilha");
        } 
        else{
            if(elem.val().indexOf('\\')==-1) arrV = elem.val().split('/');    
            else arrV = elem.val().split('\\');

            valor = arrV[arrV.length-1];
            elem.next().show('slow');
            elem.next().children('label').html(valor);
        }
    }
</script>

<!-- Modal Regras Função Santander .CSV -->
<div class="modal fade" tabindex="-1" role="dialog" id="modalIntable" aria-labelledby="#">
    <div class="modal-dialog" role="document">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title text-light">Personal Control <i class="material-icons align-calendar">speaker_notes</i></h5>
                <button type="button" class="close text-muted" aria-label="Close" data-dismiss="modal"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body pt-2">
            <form method="POST" action="../intable/" enctype="multipart/form-data">
                <button type="button" class="btn btn-danger btn-block active font-weight-bold"
                    onclick="$(this).next().toggle();">Importar Planilha .CSV</button>
                <div style="display: none;">
                    <ul class="list-group my-3">
                        <li class="list-group-item text-center">
                            <b>Regras:</b>
                        </li>
                        <li class="list-group-item">
                            <b>1.</b> Crie uma planilha no excel com o formato .CSV
                        </li>
                        <li class="list-group-item">
                            <b>2.</b> A planilha deve ter três colunas com <em>Data, Descrição e Valor</em>, nesta exata ordem, com todos campos preenchidos.
                        </li>
                        <li class="list-group-item">
                            <b>3.</b> A data deve ser no padrão brasileiro, usando / e 4 digitos para o ano <span class="text-muted">(dd/mm/aaaa)</span>.
                        </li>
                        <li class="list-group-item">
                            <b>4.</b> Não use '.' para separar casas de milhar no campo valor <span class="text-muted">(<s>1.000,00</s> &rarr; 1000,00)</span>.
                        </li>
                        <li class="list-group-item">
                            <b>5.</b> Clique no botão e selecione a sua Planilha.
                        </li>
                        <li class="list-group-item text-center">
                            <div><b>Exemplo:</b></div>
                            <table class="table table-bordered mt-2">
                                <tr>
                                    <td>22/03/2019</td>
                                    <td>Descrição da Movimentação</td>
                                    <td>2036,50</td>
                                </tr>
                                <tr>
                                    <td>01/04/2020</td>
                                    <td>Descrição da Movimentação</td>
                                    <td>3184,59</td>
                                </tr>
                            </table>
                        </li>
                        <li class="list-group-item text-center">
                            <b>Pronto!</b>
                        </li>
                    </ul>
                    <button type="button" class="btn btn-sm btn-block btn-danger font-weight-bold mt-3"
                        onclick="$('#fileMov').click();">Importar Arquivo</button>
                    <input type="file" accept=".csv" class="d-none" id="fileMov" name="fileMov" onchange="altLabel($(this));">
                    <div style="display: none">
                        <label class="border border-secondary rounded mt-2 mb-1 p-1 d-block text-center text-muted">Selecione a Planilha</label>
                        <button type="submit" class="btn btn-sm btn-block btn-outline-success font-weight-bold mb-3">Finalizar</button>
                    </div>
                </div>
                <button type="button" class="btn btn-info btn-block font-weight-bold mt-2">
                    Digitar Manualmente
                </button>
                <div style="display: none;">
                    <ul class="list-group my-3">
                        <li class="list-group-item text-center">
                            <b>Regras:</b>
                        </li>
                        <li class="list-group-item">
                            <b>1.</b> Crie uma planilha no excel com o formato .CSV
                        </li>
                        <li class="list-group-item">
                            <b>2.</b> A planilha deve ter três colunas com <em>Data, Descrição e Valor</em>, nesta exata ordem, com todos campos preenchidos.
                        </li>
                        <li class="list-group-item">
                            <b>3.</b> A data deve ser no padrão brasileiro, usando / e 4 digitos para o ano <span class="text-muted">(dd/mm/aaaa)</span>.
                        </li>
                        <li class="list-group-item">
                            <b>4.</b> Não use '.' para separar casas de milhar no campo valor <span class="text-muted">(<s>1.000,00</s> &rarr; 1000,00)</span>.
                        </li>
                        <li class="list-group-item text-center">
                            <div><b>Exemplo:</b></div>
                            <div class="text-muted border p-2 rounded">22/03/2019;Descrição da Movimentação;99,99;</div>
                        </li>
                        <li class="list-group-item text-center">
                            <b>Pronto!</b>
                        </li>
                    </ul>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>