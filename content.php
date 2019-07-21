<?php

use Util\Config\Conn;

function headerContent($dir = "/")
{
    echo '<!DOCTYPE html>
                <html>
                <head>
                    <meta charset="utf-8">
                    <meta http-equiv="X-UA-Compatible" content="IE=edge">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <title>Sistema de Derivação Semiautom&aacute;tico de Produtos e Implanta&ccedil;&atilde;o</title>
                    
                    <link rel="stylesheet" href="lib/font-awesome/css/font-awesome.min.css">

                 
                     <!--Import Google Icon Font-->
                      <link href="' . $dir . 'lib/icons/MaterialIcons.css" rel="stylesheet">
                      <!--Import materialize.css-->
                      <link type="text/css" rel="stylesheet" href="' . $dir . 'lib/materialize/css/materialize.min.css"  media="screen,projection"/>
                
                      <!--Let browser know website is optimized for mobile-->
                      <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
                 <style>
                    .icon_style{
                        position: absolute;
                        right: 10px;
                        top: 10px;
                        font-size: 20px;
                        color: white;
                        cursor:pointer; 
                    }
                                    
                      /* label focus color */
                     .input-field input:focus + label {
                       color: #0091ea !important;
                     }
                      blockquote {
                          border-left: 5px solid #0091ea; /* Just change the color value and that\'s it*/
                     }
                      /* label underline focus color */
                     .row .input-field input:focus {
                        border-bottom: 1px solid #0091ea !important;
                        box-shadow: 0 1px 0 0 #0091ea !important
                      }
                      [type="radio"]:checked + label:after,
                      [type="radio"].with-gap:checked + label:after {
                          border: 2px solid #0277bd;
                          background-color: #0277bd;
                          z-index: 0;
                      }
                      [type="checkbox"]:checked + label:after,
                      [type="checkbox"].filled-in:checked + label:after  {
                          border: 2px solid #0277bd !important;
                          background-color: #0277bd;
                          z-index: 0;
                       }
                    </style>
                </head>
                
                <body>
                     <nav class="#1976d2 blue darken-2" style="height: 70px" role="navigation">
                        <div class="nav-wrapper container">
                          <a href="index.php" class="brand-logo" style="margin: 20px 0px 20px 0px;"> <h5>Easy Software Product Derivation</h5></a>
                        </div> <!-- SAPDSD--> 
                      </nav>
                    <div class="row">';
}

function footerContent($dir = "/")
{
    echo '<!-- end content -->
             <!-- jQuery (necessary for Bootstrap\'s JavaScript plugins) 
             <script src=' . $dir . 'lib/jquery1.12.4.js"></script>-->
                
              <!--Import jQuery before materialize.js-->
              <script src="' . $dir . 'lib/jquery3.3.1.min.js"></script>
              <script type="text/javascript" src="' . $dir . 'lib/materialize/js/materialize.min.js"></script>
              
              <!--Import jQuery Init Template-->
              <script type="text/javascript" src="' . $dir . 'lib/materialize/js/init.js"></script>
    
        </div><!-- and row content --> 
         <footer class="page-footer #e6e6e6 grey lighten-3">
          <div class="footer-copyright #d8d8d8 grey lighten-2">
            <div class="container">
            <b style="color: black">' . date('Y') . ' </b> 
            <a class="black-text text-lighten-4 right" href="#!"><b>Dev</b> </a>
            </div>
          </div>
        </footer>
    </body>
    </html>';

}

function breadcrumb($step = 1)
{

    $nav = ' <nav class="#90a4ae blue-grey lighten-2">
    <div class="nav-wrapper">
      <div class="col s12" style="margin: auto 190px;">';

    for ($i = 1; $i <= 4; $i++) {
        if ($step == $i) {
            $nav .= '<a  class="breadcrumb" href = "#" ><b style="color:#fff"> Etapa 0' . $i . ' </b></a ></li > ';
        } else {
            $nav .= '<a  class="breadcrumb" href = "#" > Etapa 0' . $i . ' </a ></li > ';
        }
    }
    if ($step == 5) {
        $nav .= '<a  class="breadcrumb" href = "#" ><b style="color:#fff"> Etapa 05</a ></b>';
    } else {
        $nav .= '<a  class="breadcrumb" href = "#" style="color: rgba(255, 255, 255, 0.7);">Etapa 05</a >';
    }
    $nav .= '</div>
            </div>
          </nav>';
    echo $nav;
}

