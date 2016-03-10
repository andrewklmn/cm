<?php 

/*
 * Database connection to Cashmaster Database
 */
    
    include_once 'do_sql.php';
    include_once 'count_rows_from_sql.php';
    include_once 'get_array_from_sql.php';
    include_once 'get_assoc_array_from_sql.php';
    include_once 'fetch_row_from_sql.php';
    include_once 'fetch_assoc_row_from_sql.php';
    include_once 'fetch_fields_info_from_sql.php';
    include_once 'draw_table_from_sql.php';
    
    // создаём глобальную переменную для работы функций
    
    define( "DB_BASE","cashmaster");
    define( "DB_USER" ,"cashmaster");
    define( "DB_PASS" ,"123456");
    
    $db = mysqli_connect(
            "localhost",
            DB_BASE,
            DB_PASS,
            DB_USER,
            3306
    );
     
    // проверяем нет ли ошибки подключения
    if (mysqli_connect_errno()) {
        printf("<br>Connection error: %s\n", mysqli_connect_error());
        exit();
    }
    
    do_sql("SET CHARACTER SET utf8;");

?>
