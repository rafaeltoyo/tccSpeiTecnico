<!DOCTYPE html>
<?php
include("config.php");
require("veref.php");

@session_start();
@$id_schedule = $_REQUEST['id_schedule'];

if (@$_SESSION['login'] != "") {
    $query = "SELECT * FROM requests
    WHERE login = '{$_SESSION['login']}'
    OR login IN (SELECT login FROM uschedule
      WHERE login LIKE '{$_SESSION['login']}'
      )";
    $result = mysql_query("$query") or die (mysql_error());
    while($coluna = mysql_fetch_assoc($result)){
    if ($coluna['login'] == "") {
        if($_SESSION['lang'] == "pt"){$_SESSION['mensagem'] = "Agenda invalida!";} else { $_SESSION['mensagem'] = "Invalid Schedule!";}
        echo "<script> Redirect('viewSchedule.php') </script>";
        /*
        ob_start();
        header("Location: viewSchedule.php");
        ob_end_flush();
        exit;
        */
    }

}
}
if(@$_REQUEST['button']=="Accept" || @$_REQUEST['button']=="Aceitar"){
    $query = "SELECT requests.* FROM requests INNER JOIN schedule ON requests.id_schedule = schedule.id WHERE requests.id = '{$_POST['id_request']}' AND requests.login = '{$_SESSION['login']}' AND requests.stats = 1";
    $result = mysql_query("$query") or die (mysql_error());
    $coluna = mysql_fetch_assoc($result);
    $id = $coluna['id_schedule'];
    $login_request = $coluna['login'];

    // Verefica se achou o pedido no banco
    if ($login_request == $_SESSION['login']) {
        // Verefica se o membro do pedido já não está na agenda
        $query = "SELECT * FROM uschedule WHERE id_schedule = '$id' AND login = '$login_request' ";
        $result2 = mysql_query("$query") or die (mysql_error());
        $coluna2 = mysql_fetch_assoc($result2);
        $id_request = $coluna2['id_uschedule'];

        if ($id_request == "") {
            $query = "INSERT INTO uschedule(id_schedule,login,rank,follow) VALUES('$id','$login_request',0,0)";
            $result = mysql_query("$query") or die(mysql_error());

            $query = "DELETE FROM requests where login = '$login_request' and id_schedule = '$id'";
            $result = mysql_query("$query") or die(mysql_error());

            if($_SESSION['lang'] == "pt"){$_SESSION['mensagem'] = "Pedido Aceito!";} else {$_SESSION['mensagem'] = "Request accepted!";}
            echo "<script> Redirect('invitations.php') </script>";
            /*
            ob_start();
            header("Location: invitations.php");
            ob_end_flush();
            exit;
            */
        }

        if($_SESSION['lang'] == "pt"){$_SESSION['mensagem'] = "Você já esta nesta agenda!";} else {$_SESSION['mensagem'] = "You already are in this schedule!";}
        echo "<script> Redirect('invitations.php') </script>";
        /*
        ob_start();
        header("Location: invitations.php");
        ob_end_flush();
        exit;
        */
    }

    if($_SESSION['lang'] == "pt"){$_SESSION['mensagem'] = "Pedido Inexistente!";} else {$_SESSION['mensagem'] = "Request doesn't exist!";}
    echo "<script> Redirect('invitations.php') </script>";
    /*
    ob_start();
    header("Location: invitations.php");
    ob_end_flush();
    exit;
    */
}

if(@$_REQUEST['button']=="Refuse" || @$_REQUEST['button']=="Recusar"){
    $query = "SELECT requests.* FROM requests INNER JOIN schedule ON requests.id_schedule = schedule.id WHERE requests.stats = 1";
    $result = mysql_query("$query") or die (mysql_error());
    $coluna = mysql_fetch_assoc($result);
    $id = $coluna['id_schedule'];
    $login_request = $coluna['login'];
    $query = "DELETE FROM requests where login = '$login_request' and id_schedule = '$id'";
    $result = mysql_query("$query") or die(mysql_error());

    if($_SESSION['lang'] == "pt"){$_SESSION['mensagem'] = "Pedido negado!";} else {$_SESSION['mensagem'] = "Request denied!";}
    echo "<script> Redirect('invitations.php') </script>";
    /*
    ob_start();
    header("Location: invitations.php");
    ob_end_flush();
    exit;
    */
}
?>



<html lang="pt-br">
<head>
    <title> <?php echo ($_SESSION['lang'] == 'pt' ? 'Pedidos || PostNotes' : 'Invitations || PostNotes') ?> </title>
    <meta charset="UTF-8" />
    <link rel="stylesheet" type="text/css" href="_css/menu.css">
    <link rel="stylesheet" type="text/css" href="_css/indexstyle.css">
    <link rel="stylesheet" type="text/css" href="_css/member.css"/>
</head>
<body>
<?php
include ("menu.php");
?>

<header id="cabecalho">

</header>

<div id="min_size"><div id="center_box">

        <h1> <?php echo ($_SESSION['lang'] == 'pt' ? 'Seus convites:' : 'Your invitations:') ?> </h1>

        <div id="List"><ul>
            <?php
            $verefica = false;
            $query = "SELECT requests.*,schedule.name AS name FROM requests INNER JOIN schedule ON requests.id_schedule = schedule.id WHERE login = '{$_SESSION['login']}' AND requests.stats = 1";
            $result = mysql_query("$query") or die (mysql_error());
            while ($coluna = mysql_fetch_assoc($result)){
                $verefica = true;

                ?>
                <li id="requests_body">
                    <ul>
                    <form action="#" method="POST" name="form1">
                        <li id="user_name"><?php echo ($_SESSION['lang'] == 'pt' ? 'Você foi convidado para entrar nesta agenda' : 'You were invited to join on this schedule:') ?> "<?php echo $coluna['name'] ?>"</li>
                        <li><input type="submit" name="button" value='<?php echo ($_SESSION['lang'] == 'pt' ? 'Aceitar' : 'Accept') ?>' /></li>
                        <li><input type="submit" name="button" value='<?php echo ($_SESSION['lang'] == 'pt' ? 'Recusar' : 'Refuse') ?>' /></li>
						<input type="hidden" id="id_request" name="id_request" value="<?php echo $coluna['id']; ?>" />
                    </form>
                    </ul>
                </li>
            <?php
            }
            if ( !$verefica ){
                ?>
                <li> <?php echo ($_SESSION['lang'] == 'pt' ? "Você não tem convites" : "You don't have invitations") ?> </li>
            <?php
            }
            ?>
        </ul></div>
    </div></div>
<?php require("footer.php"); ?>
</body>
</html>