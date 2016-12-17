<!DOCTYPE html>
<?php 
    session_start() ;     
    if(!$_SESSION){
        $header = header('location: ../'); ;
    }
?>
<!--
Desarrollado por : Jaime Francisco Altamirano Bustamante.
Noviembre 2016
-->
<html lang="en">
    <head>
        <title>Hotel Millahue</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <link href="../Css/bootstrap-3.3.6/css/bootstrap.css" rel="stylesheet" type="text/css"/>
        <link href="../Css/NavMenu.css" rel="stylesheet" type="text/css"/>
        <link href="../Css/Application.css" rel="stylesheet" type="text/css"/>
        
        <script src="../Scripts/jquery/jquery.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                $('.content').load('Habitaciones.php');
                $('.sidenav-menu-item').click(function(e){
                    e.preventDefault();
                    window.sharedVariable = null;
                    $.ajax(
                    {
                        type: "get",
                        url: $(this).attr('href'),
                        cache: false,
                        statusCode: {
                            404: function ()
                               {
                                  alert('Página No Disponible');                                  
                               }
                           },
                        async: true
                    });
                    $('.content').load($(this).attr('href'));
                });
                OpenCloseSideNav();
            });
            function OpenCloseSideNav(){
                switch($('#AppSidenav').data()['xp']){
                    case 'close':
                        $('#AppSidenav').css('width', '250px');
                        $('#main').css('margin-left', '250px');
                        $('#AppSidenav').data()['xp'] = 'open';    
                        break;
                    case 'open':
                        $('#AppSidenav').css('width', '0px');
                        $('#main').css('margin-left', '0px');
                        $('#AppSidenav').data()['xp'] = 'close';                            
                        break;
                }
            }
        </script>
    </head>
    <body>
      <div id="AppSidenav" class="sidenav" data-xp="close">        
<!--        <a href="RegistroPasajeros.php?habitacion=0" class="sidenav-menu-item">Registro Pasajeros</a>-->
        <a href="Habitaciones.php" class="sidenav-menu-item">Admin. Habitaciones</a>
<!--        <a href="PaginaNoDisponible" class="sidenav-menu-item">Administraci&oacute;n</a>-->
        <a href="Informes/IngresosDia.php" class="sidenav-menu-item">Informes</a>
      </div>

      <div id="main">
        <div style="width:100%; background-color: #206C9E;padding: 5px; color:#f1f1f1;">
            <span style="font-size:30px;cursor:pointer;" onclick="OpenCloseSideNav();">&#9776; Hotel Millahue</span>
            <span style="font-size:20px;cursor:pointer;float:right;" ><?php print 'Usuario : ' .$_SESSION['_UserName'] ?></span>
        </div>
          <div class="content"></div>
      </div>
    </body>    
</html>

