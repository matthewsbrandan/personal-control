<?php
    session_start();
    include('../../conn/function.php');
    $senha = md5($_POST['modalSenha']) ;
    $search = "select * from usuario where email='{$_POST['modalEmail']}' and senha='$senha';";
    $data = enviarComand($search,'bd_pctrl');
    if($result = $data->fetch_assoc()){
        $_SESSION['pctrl_user'] = $result['id'];
        header('Location: ../dashboard/');
    }else header('Location: ../index.php?msg=3');
?>