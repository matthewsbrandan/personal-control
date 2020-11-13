<?php session_start(); ?>
<!doctype html>
<html lang="pt-br">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../img/arrow-icon.jpg" type="image">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <title>PCtrl - Personal Control</title>
    <!-- Bootstrap core CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/scroll.css" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link href="cover.css" rel="stylesheet">
    <style>
    </style>
    <script src="../jquery/jquery.js"></script>
    <script>
        $(function(){
           <?php if(isset($_GET['cadastrar'])){ ?> alterDiv('Cadastrar');                      <?php }else ?> 
           <?php if(isset($_GET['sobre']))    { ?> alterDiv('Sobre');                          <?php }     ?> 
           <?php if(isset($_GET['msg']))      { ?> mostraMsg(<?php echo $_GET['msg']; ?>);     <?php }     ?> 
           <?php if(isset($_GET['mtworld']))  { ?> mostraMsg(<?php echo "'mtworld'"; ?>); <?php }     ?>
        });
        function msg(p,arr){ 
            if(arr[p]){ $('#modalMsgBody').html(arr[p]); $('#modalMsgAutoClick').click(); }
            $('#modalMsg .modal-content').addClass('bg-dark');
            $('#modalMsgBody').removeClass('text-muted');
            $('#modalMsgBody a').addClass('text-warning');
        }
        function mostraMsg(p){
            if(p=="mtworld"){
                content = "<div class='bg-light border border-secondary px-1 pb-1 pt-0 mb-2 rounded d-flex justify-content-center text-dark flex-column' style='opacity:.6'>";
                    content += "<small class='border-bottom border-secondary mb-1' style='font-size: 7pt; opacity: .85;'>Vincular com MatthewsWorld</small>";
                    content += "<span class='material-icons'>ac_unit</span>";
                content += "</div>";
                $('#modalLog .modal-body').prepend(content);
                $('#modalLog .modal-footer').prepend("<button type='button' class='btn btn-outline-light btn-block' onclick=\"alterDiv('Cadastrar');\" data-dismiss='modal'>Cadastrar-se</button><br/>");
                $('#btnEntrarControle').click();
            }
            else{
                frase = "";
                switch(p){
                    case 1:
                        frase = "Erro de Cadastro!<br/>Tente novamente, ou acesse <a href='https://www.matthewsworld.me/index.php?contato' target='_blank'>matthewsworld.me/index.php?contato</a> e registre o problema de cadastro.";
                        break;
                    case 2: frase = "<strong>Login Expirou!</strong><br/>Entre novamente para acessar a sua conta."; break;
                    case 3: frase = "<strong>Email ou Senha Incorretos!</strong><br/>Tente logar novamente."; break;
                }
                if(p==1){
                    msg(0,new Array(frase));
                }else if(p==2 || p==3){
                    alerta = "<div class='alert alert-danger alert-dismissible fade show text-left' style='text-shadow: none;' role='alert'>"+frase+"<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
                    $('#modalLog .modal-body').prepend(alerta);
                    $('#btnEntrarControle').click();
                }
            }
        }
        function alterDiv(p){
            switch(p){
                case 'Entrar':
                    $('#divEntrar').show('slow');
                    $('#divCadastrar').hide(); $('#divSobre').hide();
                    break;
                case 'Cadastrar':
                    $('#divCadastrar').show('slow');
                    $('#divEntrar').hide(); $('#divSobre').hide();
                    break;
                case 'Sobre':
                    $('#divSobre').show('slow');
                    $('#divEntrar').hide(); $('#divCadastrar').hide();
                    break;
            }
            $('.active').removeClass('active');
            $('#btn'+p).addClass('active');
        }
    </script>
  </head>

  <body class="text-center">
    <!-- Conteúdo -->
    <div class="cover-container d-flex h-100 p-3 mx-auto flex-column">
      <!-- Header -->
      <header class="masthead mb-auto">
        <div class="inner">
          <h3 class="masthead-brand">
            PCtrl $
            <?php if(isset($_GET['mtworld'])){ ?>
            <div class="d-inline-block border px-1 rounded bg-light text-dark" onclick="location.href='https://www.matthewsworld.me'">
                <span class="material-icons">ac_unit</span>
            </div>
            <?php } ?>
          </h3>
          <nav class="nav nav-masthead justify-content-center">
            <a onclick="alterDiv('Entrar')"    id="btnEntrar"    class="nav-link active" href="#">Entrar</a>
            <a onclick="alterDiv('Cadastrar')" id="btnCadastrar" class="nav-link"        href="#">Cadastrar</a>
            <a onclick="alterDiv('Sobre')"     id="btnSobre"     class="nav-link"        href="#">Sobre</a>
          </nav>
        </div>
      </header>
      <!-- Body -->
      <main role="main" class="inner cover">
        <div id="divEntrar">
            <h1 class="cover-heading">Personal Control</h1>
            <p class="lead">Seu <em>SGFP</em>, <strong>S</strong>istema de <strong>G</strong>erenciamento de <strong>F</strong>inanças <strong>P</strong>essoais. Aqui você controla suas finanças de forma nítida e organizada, o que te ajuda a descobrir suas fraquezas e oportunidades, que são onde você deve ter cautela ou investir.</p>
            <p class="lead">
              <a href="#" class="btn btn-lg btn-secondary" data-toggle="modal" data-target="#modalLog" id="btnEntrarControle">Entrar no Controle</a>
            </p>
        </div>
        <!-- Adicionar Formulário de Cadastro -->
        <div id="divCadastrar" style="display:none;">
            <h1 class="cover-heading">Cadastrar-se</h1>
            <p class="lead">Preencha os campos para se cadastrar no Personal Control, e tenha o controle sobre suas finanças.<br></p>
            <form method="POST" action="back/cadUser.php<?php if(isset($_GET['mtworld'])) echo "?mtworld";?>">
                <!--Nome-->
                <div class="input-group mb-3">
                    <input 
                        class="form-control" 
                        type="text" 
                        id="cadNome" name="cadNome" 
                        placeholder="Digite seu Primeiro Nome..." 
                        <?php if(isset($_SESSION['user_mtworld_nome'])){
                            $autoCompleteName = $_SESSION['user_mtworld_nome'];
                            if(strpos($_SESSION['user_mtworld_nome'],' ')>0)
                            { $autoCompleteName = substr($_SESSION['user_mtworld_nome'],0,strpos($_SESSION['user_mtworld_nome'],' ')); }
                            echo "value='$autoCompleteName'";
                        } ?>
                    required>
                </div>
                <!--Sobrenome-->
                <div class="input-group mb-3">
                    <input 
                        class="form-control" 
                        type="text" 
                        id="cadSobrenome" name="cadSobrenome" 
                        placeholder="Digite seu Último Nome..." 
                        <?php if(isset($_SESSION['user_mtworld_nome'])){
                            $autoCompleteSurname = "";
                            if(strpos($_SESSION['user_mtworld_nome'],' ')>0)
                            { $autoCompleteSurname = substr($_SESSION['user_mtworld_nome'],strpos($_SESSION['user_mtworld_nome'],' ')+1); }
                            echo "value='$autoCompleteSurname'";
                        } ?>
                    required>
                </div>
                <!--Email-->
                <div class="input-group mb-3">
                    <input 
                        class="form-control" 
                        type="email" 
                        id="cadEmail" name="cadEmail" 
                        placeholder="Digite seu E-mail..."
                        <?php if(isset($_SESSION['user_mtworld_email'])) echo "value='{$_SESSION['user_mtworld_email']}'";?>
                    required>
                </div>
                <!--Celular-->
                <div class="input-group mb-3">
                    <input class="form-control" type="tel" id="cadCelular" name="cadCelular" placeholder="(99)99999-9999" required>
                </div>
                <!--Senha-->
                <div class="input-group mb-3">
                    <input class="form-control" type="password" id="cadSenha" name="cadSenha" placeholder="Digite sua Senha..." required>
                </div>
                <!--Confirmar Senha-->
                <div class="input-group mb-3">
                    <input class="form-control" type="password" id="confSenha" placeholder="Confirme sua Senha..." required>
                </div>
                <p class="lead">
                  <button type="submit" class="btn btn-lg btn-secondary" id="btnCadastrar" name="btnCadastrar">Cadastrar</button>
                </p>
            </form>
        </div>
        <!-- Adicionar conteúdo sobre mim -->
        <div id="divSobre" style="display:none;">
            <h1 class="cover-heading">Sobre</h1>
            <p class="lead">Desenvolvido por mim, Mateus Brandão, Técnico em Administração de Empresas e Técnico em Informática, juntando o conhecimento financeiro com a criatividade para criar a ferramenta correta e mais eficiente possível para administrar suas finanças, de forma <strong>Gratuita</strong>.</p>
            <p class="lead">
              <a href="https://www.matthewsworld.me" target="_blank" class="btn btn-lg btn-secondary">+ Sobre o Desenvolvedor { }</a>
            </p>
        </div>
      </main>
      <!-- Footer -->
      <footer class="mastfoot mt-auto">
        <div class="inner">
          <p>Desenvolvido por <a href="../">Mateus Brandão</a>.</p>
        </div>
      </footer>
    </div>
    
    <!-- Modal -->
    <div class="modal fade" id="modalLog" tabindex="-1" role="dialog" aria-hidden="true" aria-labelledby="">
        <div class="modal-dialog" role="document">
            <div class="modal-content bg-dark">
                <form method="POST" action="back/logUser.php<?php if(isset($_GET['mtworld'])) echo '?mtworld';?>">
                <div class="modal-header">
                    <h5 class="modal-title">PCtrl - Entrar</h5>
                    <button type="button" class="close text-light" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <label for="email" class="h4 pb-1" style="font-weight: 200;">Login</label>
                    <div class="form-group">
                        <input 
                            class="form-control"
                            type="email" 
                            id="modalEmail" name="modalEmail" 
                            placeholder="Digite seu E-mail..."
                            <?php if(isset($_SESSION['user_mtworld_email'])) echo "value='{$_SESSION['user_mtworld_email']}'";?>    
                        required>
                    </div>
                    <div class="form-group mb-1">
                        <input class="form-control" type="password" id="modalSenha" name="modalSenha" placeholder="Digite sua Senha..." required>
                    </div>
                    <a class="text-muted small" style="text-align: left;" href="#">Esqueci minha Senha...</a>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger btn-block" id="btnLogar" name="btnLogar">Entrar</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <?php include('modais/modalMsg.php');   /*Modal Confirmação*/ ?>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="jquery-slim.min.js"><\/script>')</script>
    <script src="popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
  </body>
</html>
