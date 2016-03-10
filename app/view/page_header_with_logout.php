<?php

/*
 * Login page
 */
    include 'app/view/html_header.php';
    include 'app/view/html_head_bootstrap.php';
    include 'app/model/auth/less_then_three_days.php';
    include 'app/view/remove_special_symbols.php';

    if (!isset($data['title'])) {
        $data['title'] = '';
    }
?>
<style>
    @media print
    {    
        .no-print, .no-print *
        {
            display: none !important;
        }
    }
    div#cm_version {
        position: absolute; 
        left: 1px; 
        top: 50px; 
        padding: 10px;
        corner-radius: 2px;
        border: 1px solid bisque;
        text-align: center;
        background-color:seashell;
        font-size: 10pt;
        color: blue;
    }
</style>
    <div class="navbar navbar-inverse navbar-fixed-top no-print">
      <div class="navbar-inner">
        <div class="container" style="position: relative;">
        <img id="cpi_logo" style="float: left; margin: 0px;" src="css/img/CPI_logo.jpg"/>
        <div id="cm_version" style="display: none;">
            CashMaster by CPI<br/>Version 1.01.03
        </div>
          <span class="brand">
              <!--<img style="margin: 0px;" src="css/img/CPI_logo.jpg"/>-->
              <a style="font-family: sans-serif; font-weight: bold;font-size: 21px;" href="?c=index">
                  <?php echo $program; ?>&#8482;</a>
              <?php echo '&nbsp;&nbsp;&nbsp;&nbsp;<font style="font-size: 16px;color:lightblue;">',$data['title'],'</font>';?>
          </span>
          <div class="pull-right">
          <span class="brand" style="font-size: 16px;"><?php 
                echo $_SESSION[$program]['user_post'].': '.$_SESSION[$program]['user_fio']; ?>
          </span>
            <?php
                if (isset($menu[$_SESSION[$program]['user_role_id']])) {
                    ?>
                        <div class="btn-group" style='padding-right: 5px;'>
                            <a class="btn dropdown-toggle btn-success" data-toggle="dropdown" href="#">
                            <?php echo htmlfix($_SESSION[$program]['lang']['go_to']) ?>
                            <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" onclick="set_wait();">
                                <?php 
                                    foreach ($menu[$_SESSION[$program]['user_role_id']] as $value) {
                                        $t = explode('/', $c);
                                        if (('?c='.$t[count($t)-1])<>($value[0])) {
                                            echo '<li><a href="',
                                                    htmlfix($value[0]),'">',
                                                    htmlfix($value[1]),'</a></li>';
                                        };
                                    };
                                ?>
                            </ul>
                        </div>
                    <?php 
                };
              ?>
          <a href="?c=logout"class="btn btn-primary btn-medium">
              <?php echo $_SESSION[$program]['lang']['logout']; ?>
          </a>
          </div>
        </div>
      </div>
    </div>
<div class="container">
    <?php 
        if (less_then_three_days($_SESSION[$program]['UserConfiguration']['UserId'])) {
            $data['danger_header'] = 'Внимание';
            $data['danger_text'] = 'Пароль действителен до: '
                    .get_password_exired_date($_SESSION[$program]['UserConfiguration']['UserId'])
                    .'. Необходимо сменить пароль.';
            include 'app/view/danger_message.php';
        };
    ?>
</div>
<script>
var cpilogo = document.getElementById('cpi_logo');
var cmversion = document.getElementById('cm_version');
cpilogo.oncontextmenu = function() {
    cmversion.style.display = (cmversion.style.display == 'none')?'':'none';
    return false;
};
document.onclick = function() {
    if (cmversion.style.display == '') {
        cmversion.style.display = 'none';
        return false;
    }
}
</script>