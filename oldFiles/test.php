<?php
echo'
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="description" content="API que transmite dados sobre os niveis de um rio">
        <meta name="keywords" content="Moppe, monitoramento de sensores, arduino, webservice, php, banco de dados, mysql, ionic, slim, apache, onesignal">
        <meta name="author" content="Edson Boldrini">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Moppe - Webservice</title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>';

echo "
    <h3>Dispositivo 1:</h3>
    <table>
    <tr>
        <th>id_leitura</th>
        <th>Id_Dispositivo</th> 
        <th>Icos Fundo</th>
        <th>Icos Superfície</th>
        <th>Ultrassonico</th>
        <th>Latitude</th>
        <th>Longitude</th>
        <th>Elevação</th>
        <th>Data/Hora</th>
    </tr>
    
    <tr>
        <td>row->id_leitura</td>
        <td>row->id_dispositivo</td> 
        <td>row->valor_icos_fundo</td>
        <td>row->valor_icos_superficie</td>
        <td>row->valor_ultrassonico</td>
        <td>row->latitude</td>
        <td>row->longitude</td>
        <td>row->elevacao</td>
        <td>row->data_hora</td>
    </tr>	   
    </table>";

    
echo '
    <h3>Dispositivo 2:</h3>
    <table>
    <tr>
        <th>id_leitura</th>
        <th>Id_Dispositivo</th> 
        <th>Icos Fundo</th>
        <th>Icos Superfície</th>
        <th>Ultrassonico</th>
        <th>Latitude</th>
        <th>Longitude</th>
        <th>Elevação</th>
        <th>Data/Hora</th>
    </tr>
    
    <tr>
        <td>$row->id_leitura</td>
        <td>$row->id_dispositivo</td> 
        <td>$row->valor_icos_fundo</td>
        <td>$row->valor_icos_superficie</td>
        <td>$row->valor_ultrassonico</td>
        <td>$row->latitude</td>
        <td>$row->longitude</td>
        <td>$row->elevacao</td>
        <td>$row->data_hora</td>
    </tr>';	   


?>