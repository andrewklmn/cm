<?php

/*
 * Login page
 */
    include 'app/view/html_header.php';
    include 'app/view/html_head_bootstrap.php';
    include 'app/view/remove_special_symbols.php';
   
    // Проверяем целостность системы
    include 'app/controller/common/integrity_check.php';
    
    // generate secret sequence -===============================================
    $_SESSION[$program]['code'] = rand(10000000000, 90000000000);

?>
    <script type="text/javascript" src="js/md5.js"></script>
    <script type="text/javascript">
        function submit_login(elem) {
            var code = '<?php echo $_SESSION[$program]['code']; ?>';
            var pass = $('input#pass')[0];
            pass.value = MD5(MD5(pass.value) + code);
        };
        $(document).ready(function() {
            $('form#form').show();
            $('div#warning').hide();
        });
    </script>
    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          <img style="float: left;margin: 0px;" src="css/img/CPI_logo.jpg"/>
          <span class="brand">
              <!--<img style="margin: 0px;" src="css/img/CPI_logo.jpg"/>-->
              <a style="font-family: sans-serif; font-weight: bold;font-size: 21px;" href="?c=index"><?php echo $program; ?>&#8482;</a>
          </span>
          <form 
                id="form" 
                onsubmit="submit_login(this);" 
                action="index.php" method="POST" 
                class="navbar-form pull-right"
                style="display: none;">
              <input 
                  name='user'
                  class="span2" 
                  onchange="this.value=rs(this.value);"
                  placeholder="<?php echo $_SESSION[$program]['lang']['user']; ?>" 
                  value="<?php 
                        if (isset($_POST['user'])) {
                            echo htmlfix($_POST['user']);
                        };
                  ?>"
                  type="text" 
                  autocomplete="off"/>
              <input id="pass" name='pass' class="span2" placeholder="<?php echo $_SESSION[$program]['lang']['pass']; ?>" type="password" autocomplete="off"/>
              <button type="submit" class="btn btn-primary btn-medium"><?php echo $_SESSION[$program]['lang']['login']; ?></button>
            </form>
        </div>
      </div>
    </div>
    <div class="container">
        <div id="warning" class="alert alert-error">  
                  <a class="close" data-dismiss="alert">×</a>  
                  <strong><?php echo $_SESSION[$program]['lang']['error']; ?>!</strong>
                  <br/>
                      <?php echo $_SESSION[$program]['lang']['turn_javascript_on']; ?>
        </div> 
        <?php  
          include './app/view/error_message.php';
        ?>
    </div>
    </body>
</html>
