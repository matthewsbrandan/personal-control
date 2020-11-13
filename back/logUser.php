<?php
    session_start();
    include('../../conn/function.php');
    $senha = md5($_POST['modalSenha']) ;
    $search = "select * from usuario where email='{$_POST['modalEmail']}' and senha='$senha';";
    $data = enviarComand($search,'bd_pctrl');
    if($result = $data->fetch_assoc()){
        $_SESSION['pctrl_user'] = $result['id'];
        if(isset($_GET['mtworld'])&&isset($_SESSION['user_mtworld'])){
            $pctrl_id = 3;
            $sql = "select id from user_sites where usuario_id='{$_SESSION['user_mtworld']}' and sites_id='$pctrl_id';";
            $r = enviarComand($sql,'bd_mtworld');
            $data = $r->fetch_assoc();
            if(isset($data['id'])){
                $sql = "update user_sites set status='ativo', login='{$_POST['modalEmail']}', senha='$senha' where id='{$data['id']}';";
                if(enviarComand($sql,'bd_mtworld')) header('Location: ../dashboard/index.php?vinculado=1');
                else header('Location: ../dashboard/index.php?vinculado=0');
            }else header('Location: ../dashboard/index.php?vinculado=0');
        }else header('Location: ../dashboard/');
    }else header('Location: ../index.php?msg=3');
?>