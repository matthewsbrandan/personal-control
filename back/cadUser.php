<?php
    if(isset($_POST['btnCadastrar'])){        
        include('../../conn/function.php');
        $senha = md5($_POST['cadSenha']);
        $sql="insert usuario(nome,sobrenome,email,celular,senha) values ('{$_POST['cadNome']}','{$_POST['cadSobrenome']}','{$_POST['cadEmail']}','{$_POST['cadCelular']}','$senha');";
        $res = enviarComand($sql,'bd_pctrl');
        if($res){
            $search = "select * from usuario where email='{$_POST['cadEmail']}' and senha='$senha';";
            $data = enviarComand($search,'bd_pctrl');
            if($result = $data->fetch_assoc()){
                $_SESSION['pctrl_user'] = $result['id'];
                header('Location: ../dashboard/');
            }else header('Location: ../index.php?msg=1');
        }else header('Location: ../index.php?msg=1');
    }
?>