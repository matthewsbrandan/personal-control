<style> #modalCalculadora [onclick]:hover{ background: #eeeeef; } </style>
<script>
    function eOperador(p){ return p=="+"||p=="-"||p=="/"||p=="*"; }
    function removeEmpty(arr){ for(i=0;i<arr.length;i++){ if(arr[i]=="") arr.splice(i,1); } return arr; }
    function calcularOperacao(arr,op){
        pos = arrCalc.indexOf(op);
        while(pos>-1){
            resultado = 0;
            if(op=="+") resultado = parseFloat(arrCalc[pos-1])+parseFloat(arrCalc[pos+1]);
            if(op=="-") resultado = parseFloat(arrCalc[pos-1])-parseFloat(arrCalc[pos+1]);
            if(op=="*") resultado = parseFloat(arrCalc[pos-1])*parseFloat(arrCalc[pos+1]);
            if(op=="/") resultado = parseFloat(arrCalc[pos-1])/parseFloat(arrCalc[pos+1]);
            arrCalc[pos-1] = resultado;
            arrCalc.splice(pos,2);
            pos = arrCalc.indexOf(op);
        }
        return arrCalc;
    }
    function deleteLast(){
        v = $('#calcular').val().split('').reverse().join('');
        temp = v.substr(0,3);
        if(temp==" - "||temp==" + "||temp==" * "||temp==" / ") v = (v.substr(3)).split('').reverse().join('');
        else v = (v.substr(1)).split('').reverse().join('');
        $('#calcular').val(v).attr('placeholder','');
    }
    function grifeErro(){
        $('#calcular').addClass('border-danger');
        setTimeout(function(){$('#calcular').removeClass('border-danger');},500);
    }
    function calcular(p){
        if(p=="="){
            if($('#calcular').val().length==0) grifeErro();
            else{       
                valor = $('#calcular').val();
                while(valor.indexOf(",")>-1){ valor = valor.replace(",","."); }
                arrCalc = removeEmpty(valor.split(' '));
                if(arrCalc.length<3) grifeErro();
                else{
                    if(eOperador(arrCalc[arrCalc.length-1])) grifeErro();
                    else{
                        console.log(arrCalc);
                        arrCalc = calcularOperacao(arrCalc,"*");
                        arrCalc = calcularOperacao(arrCalc,"/");
                        arrCalc = calcularOperacao(arrCalc,"+");
                        arrCalc = calcularOperacao(arrCalc,"-");
                        console.log(arrCalc);
                        $('#calcular').val(arrCalc[0].toString().replace(".",","));
                    }
                }
            }
        }
        else if(p=="C") $('#calcular').val('').attr('placeholder','');   
        else if(p=="<-") deleteLast();
        else if(p==","){
            if($('#calcular').val().length==0) $('#calcular').val("0,");
            else{
                arrCalc = removeEmpty($('#calcular').val().split(' '));
                if(eOperador(arrCalc[arrCalc.length-1])) grifeErro();
                else if(arrCalc[arrCalc.length-1].indexOf(",")!=(-1)) grifeErro();
                else $('#calcular').val($('#calcular').val()+",");
            }
        }
        else{
            if($('#calcular').val().length==0&&eOperador(p.trim())&&p.trim()!='-'){ $('#calcular').attr('placeholder','Inválido!'); grifeErro(); }
            else{
                arrCalc = removeEmpty($('#calcular').val().split(' '));
                if(eOperador(p.trim())){
                    if(arrCalc.length>=1){ 
                        if(eOperador(arrCalc[arrCalc.length-1])) grifeErro();
                        else if(arrCalc[arrCalc.length-1].substr(-1)==",") grifeErro();
                        else $('#calcular').val($('#calcular').val()+p);  
                    }else
                    if(p.trim()=="-") $('#calcular').val('-');
                    else msg(0,["Houve um erro não determinado!"]);
                }else $('#calcular').val($('#calcular').val()+p);
            }
        } 
    }
    $(function(){
        $('.btnCalc').addClass('card text-center mx-0 mb-1').attr('onclick','calcular($(this).html())');
        $('.btnIgual').addClass('card text-center mx-0 mb-1').attr('onclick',"calcular('=');");
        $('.btnBack').addClass('card text-center mx-0 mb-1').attr('onclick',"calcular('<-');");
    });
</script>
    <!--Modal Calendar-->
    <button type="button" class="d-none" id="modalCalculadoraAutoClick" data-toggle="modal" data-target="#modalCalculadora"></button>
    <div class="modal fade" tabindex="-1" role="dialog" id="modalCalculadora" aria-labelledby="">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="material-icons align-calendar text-danger">functions</i>
                        Calculadora
                        <i class="material-icons align-middle" onclick="if($(this).hasClass('text-primary')){ $(this).removeClass('text-primary'); $('#calcular').attr('readonly',true); $('#alertKeyBoard').hide(); }else{ $(this).addClass('text-primary'); $('#calcular').attr('readonly',false); $('#alertKeyBoard').show(); }">keyboard</i>
                    </h5>
                    <button type="button" class="close" aria-label="Close" data-dismiss="modal"> <span aria-hidden="true">&times;</span> </button>
                </div>
                <div class="modal-body p-2">
                    <div class="row">
                        <div class="col-12">
                            <div class="alert alert-light border p-1 mb-1 mt-0 alert-dismissible fade show text-center" style="display: none;" role="alert" id="alertKeyBoard">
                                Coloque espaço entre os operadores ( + - * / )
                                <button type="button" class="close py-0 small" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control text-right" id="calcular" style="transition: .6s background;" readonly>
                            </div>
                        </div>
                        <div class="col-4"><div class="btnCalc">7</div></div>
                        <div class="col-4"><div class="btnCalc">8</div></div>
                        <div class="col-4"><div class="btnCalc">9</div></div>
                        <div class="col-4"><div class="btnCalc">4</div></div>
                        <div class="col-4"><div class="btnCalc">5</div></div>
                        <div class="col-4"><div class="btnCalc">6</div></div>
                        <div class="col-4"><div class="btnCalc">1</div></div>
                        <div class="col-4"><div class="btnCalc">2</div></div>
                        <div class="col-4"><div class="btnCalc">3</div></div>
                        <div class="col-4"><div class="btnCalc text-danger font-weight-bold" title="Apagar Tudo">C</div></div>
                        <div class="col-4"><div class="btnCalc">0</div></div>
                        <div class="col-4"><div class="btnCalc">,</div></div>
                    </div>
                    <div class="row">
                        <div class="col-3 pr-1"><div class="btnCalc text-info font-weight-bold" title="Adição"> + </div></div>
                        <div class="col-3 pl-0 pr-1"><div class="btnCalc text-info font-weight-bold" title="Subtração"> - </div></div>
                        <div class="col-3 pl-0 pr-1"><div class="btnCalc text-info font-weight-bold" title="Multiplicação"> * </div></div>
                        <div class="col-3 pl-0"><div class="btnCalc text-info font-weight-bold" title="Divisão"> / </div></div>
                        <div class="col-9 pr-1"><div class="btnIgual text-danger bg-dark font-weight-bold" title="Igual a...">=</div></div>
                        <div class="col-3 pl-0"><div class="btnBack text-danger font-weight-bolder" title="Apagar Último">&larr;</div></div>
                    </div>
                </div>
            </div>
        </div>
    </div>