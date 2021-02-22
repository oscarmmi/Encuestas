<?php

$VALIDAR_DOCUMENTO=1; 
$CARGAR_DATOS_INICIALES=2;
$GUARDAR_USUARIO=3;
$SELECCIONAR_RESPUESTA=4;
$BUSCAR_TOTALES=5;

$accion=$_POST['accion'];

switch(intval($accion)){
    case $VALIDAR_DOCUMENTO:
        validarDocumento();
    break;
    case $CARGAR_DATOS_INICIALES:
        cargarDatosIniciales();
    break;    
    case $GUARDAR_USUARIO:
        guardarUsuario();
    break;
    case $SELECCIONAR_RESPUESTA:
        seleccionarRespuesta();
    break;
    case $BUSCAR_TOTALES:
        buscarTotalesxCrearGrafica();
    break;
}

function buscarTotalesxCrearGrafica(){
    $idusuario= $_POST['idusuario'];
    if(!is_numeric($idusuario)){
        retornarDatos('errorvalidacion', '- El usuario seleccionado no es valido');
        return;
    }
    $sql="
        select 
        a.* 
        from usuarios a 
        where 
        a.idusuario=$idusuario 
    ";
    $temp=consultar($sql);
    $categorias=buscarCategoriasconPreguntas();
    $totalesxCategoria=buscarTotalesxCategoria($idusuario);
    retornarArraydeDatos(array(
        'datos'=>$temp[0],
        'categorias'=>$categorias,
        'totales'=>$totalesxCategoria 
    ));
}

function seleccionarRespuesta(){
    $idusuario= $_POST['idusuario'];    
    $idpreguntarespuesta= $_POST['idpreguntarespuesta'];
    $idpregunta= $_POST['idpregunta'];
    if(!is_numeric($idusuario)){
        retornarDatos('errorvalidacion', '- El usuario seleccionado no es valido');
        return;
    }
    if(!is_numeric($idpregunta) && !is_numeric($idpreguntarespuesta)){
        retornarDatos('errorvalidacion', '- La pregunta seleccionada no es valida');
        return;
    }
    $idrespuesta= $_POST['idrespuesta'];
    if(!is_numeric($idrespuesta)){
        retornarDatos('errorvalidacion', '- Seleccione una respuesta valida');
        return;
    }
    $observacion=$_POST['observacion'];
    $agregarObservacion="''";
    if(isset($observacion) && $observacion!=''){
        $agregarObservacion="'$observacion'";
    }
    $idcategoria=$_POST['idcategoria'];
    $sql="
        select 
        a.idrelpreguntarespuesta 
        from relpreguntasrespuestas a 
        where 
        a.idpregunta=$idpregunta 
        and a.idrespuesta=$idrespuesta 
    ";
    $temp=consultar($sql);
    if(!count($temp)){
        retornarDatos('errorvalidacion', '- La respuesta seleccionada no esta relacionada con la pregunta');
        return;
    }
    $idrelpreguntarespuestaActual=$temp[0]['idrelpreguntarespuesta'];
    $sentenciaaEjecutar="";
    if(is_numeric($idpreguntarespuesta)){
        $sentenciaaEjecutar="update preguntasrespuestas set idrelpreguntarespuesta=$idrelpreguntarespuestaActual, observacion='$observacion' where idpreguntarespuesta=$idpreguntarespuesta; ";
    }else{
        $sentenciaaEjecutar="insert into preguntasrespuestas(idusuario, idrelpreguntarespuesta, observacion) 
            values($idusuario, $idrelpreguntarespuestaActual, $agregarObservacion ); ";
    }
    ejecutarSentencia($sentenciaaEjecutar);
    $preguntasCategoria=buscarPreguntasxCategorias($idusuario, $idcategoria);    
    retornarArraydeDatos($preguntasCategoria);
}

function buscarCategoriasconPreguntas(){
    $sql="
        select 
        a.idcategoria as codigo, 
        b.descripcion as nombre
        from (
            select distinct 
            aa.idcategoria 
            from preguntas aa             
        ) a 
        join categorias b on(b.idcategoria=a.idcategoria) 
        order by a.idcategoria asc 
    ";
    $temp=consultar($sql);
    return $temp;
}

function guardarUsuario(){
    $idtipodedocumento=$_POST['idtipodedocumento'];
    if(!is_numeric($idtipodedocumento)){
        retornarDatos('errorvalidacion', '- El tipo de documento seleccionado no es valido');
        return;
    }
    $documento=$_POST['numerodocumento'];
    if(!is_numeric($documento)){
        retornarDatos('errorvalidacion', '- El numero de documento seleccionado no es valido');
        return;
    }    
    $nombres=$_POST['nombres'];
    if($nombres==''){
        retornarDatos('errorvalidacion', '- Debe ingresar un nombre para el usuario');
        return;
    }
    $apellidos=$_POST['apellidos'];
    if($apellidos==''){
        retornarDatos('errorvalidacion', '- Debe ingresar el apellido para el usuario');
        return;
    }
    $datosUsuario=validarDocumento(array(
        'idtipodedocumento'=>$idtipodedocumento, 
        'numerodocumento'=>$documento 
    ));
    $guardarUsuario=" insert into usuarios(nombres, apellidos, idtipodedocumento, documento) values('$nombres', '$apellidos', $idtipodedocumento, $documento); ";
    if(count($datosUsuario)){
        $guardarUsuario="update usuarios set nombres='$nombres', apellidos='$apellidos' where idusuario=$datosUsuario[idusuario]; "; 
    }    
    ejecutarSentencia($guardarUsuario);
    retornarDatos('creado', 1);
}

function ejecutarSentencia($sql){
    $conexion=conexion();
    mysqli_query( $conexion, $sql ) or die ( "Algo ha ido mal en la consulta a la base de datos");    
}

function cargarDatosIniciales(){
    $respuesta=array();
    $sql="
        SELECT 
        a.idtipodedocumento as codigo, 
        a.nombre 
        FROM tipodedocumento a 
    ";
    $respuesta['datos']=consultar($sql);
    $sql="
        SELECT 
        a.idrespuesta as codigo, 
        a.descripcion as nombre  
        FROM respuestas a 
    ";
    $respuesta['respuestas']=consultar($sql);
    retornarArraydeDatos($respuesta);
}

function validarDocumento($datosUsuario=array()){
    if(!count($datosUsuario)){
        $idtipodedocumento=$_POST['idtipodedocumento'];
        $numerodocumento=$_POST['numerodocumento'];
    }else{
        $idtipodedocumento=$datosUsuario['idtipodedocumento'];
        $numerodocumento=$datosUsuario['numerodocumento'];
    }    
    $sql="
        SELECT 
        a.idusuario, 
        a.nombres, 
        a.apellidos, 
        a.idtipodedocumento, 
        a.documento as numerodocumento 
        FROM usuarios a 
        JOIN tipodedocumento b ON(b.idtipodedocumento=a.idtipodedocumento) 
        WHERE 
        b.idtipodedocumento=$idtipodedocumento  
        AND a.documento='$numerodocumento'  
        LIMIT 1 
    ";
    $respuesta=array();
    $resultado=consultar($sql);
    if(count($resultado)){
        $respuesta=$resultado[0];
    }    
    if(!count($datosUsuario)){
        if(!count($respuesta)){
            retornarArraydeDatos(array(
                'mensajeerror'=>"- Registre los datos del usuario", 
                'datos', $respuesta
            ));              
        }else{
            $categorias=buscarCategoriasconPreguntas();
            $categoriasPreguntas=buscarPreguntasxCategorias($respuesta['idusuario']);            
            retornarArraydeDatos(array(
                'datos'=>$respuesta,
                'categorias'=>$categorias,
                'preguntasxcategoria'=>$categoriasPreguntas
            ));
        }
    }else{
        return $respuesta;
    }    
}

function buscarTotalesxCategoria($idusuario){
    $sql="
        select 
        aa.idcategoria, 
        coalesce(ab.total, 0) as total 
        from (
            select distinct 
            a.idcategoria 
            from preguntas a 
            order by a.idcategoria asc 
        ) aa 
        left join (
            select 
            d.idcategoria,
            sum(e.valor) as total 
            from preguntasrespuestas b 
            join relpreguntasrespuestas c on(c.idrelpreguntarespuesta=b.idrelpreguntarespuesta) 
            join preguntas d on(d.idpregunta=c.idpregunta) 
            join respuestas e on(e.idrespuesta=c.idrespuesta) 
            where 
            b.idusuario=$idusuario 
            group by d.idcategoria 
        ) ab on(ab.idcategoria=aa.idcategoria)
    ";
    $temp=consultar($sql);
    return $temp;
}

function retornarArraydeDatos($aDatos){
    echo json_encode($aDatos, true);
}

function buscarPreguntasxCategorias($idusuario, $idcategoria=null){
    $filtroCategoria="";
    if($idcategoria!=null){
        $filtroCategoria="where a.idcategoria=$idcategoria ";
    }
    $sqlPreguntas=" 
        select 
        aa.*, 
        ab.idrespuesta, 
        ab.observacionrespuesta, 
        ab.idpreguntarespuesta 
        from ( 
            select 
            GROUP_CONCAT(b.idrespuesta) AS respuestas, 
            a.*
            from preguntas a 
            join relpreguntasrespuestas b on(b.idpregunta=a.idpregunta)   
            $filtroCategoria       
            group by a.idpregunta 
        ) aa 
        LEFT JOIN (
            SELECT 
            ba.idpreguntarespuesta, 
            ba.observacion as observacionrespuesta, 
            bb.idpregunta, 
            bb.idrespuesta
            FROM preguntasrespuestas ba
            JOIN relpreguntasrespuestas bb ON(bb.idrelpreguntarespuesta=ba.idrelpreguntarespuesta) 				 
            WHERE 
            ba.idusuario=$idusuario 
        ) ab ON(ab.idpregunta=aa.idpregunta) 
        order by aa.idcategoria asc, aa.idpregunta asc 
    ";
    $tempPreguntas=consultar($sqlPreguntas);
    $categoriasPreguntas=array();
    foreach($tempPreguntas as $pregunta){
        $categoriasPreguntas[intval($pregunta['idcategoria'])][]=$pregunta;
    }
    return $categoriasPreguntas;
}

function retornarDatos($nombre, $resultado){
    $respuesta=array(
        $nombre=>$resultado
    );
    echo json_encode($respuesta, true);
}

function consultar($sql){
    $conexion=conexion();
    $resultado = mysqli_query( $conexion, $sql ) or die ( "Algo ha ido mal en la consulta a la base de datos");
    $datosF=array();
    $campos=array();
    $camposT=array();
    while ($registros = mysqli_fetch_array( $resultado )){
        if(!count($campos)){
            $camposT=array_keys($registros);    
            foreach($camposT as $campo){
                if(!is_numeric($campo)){
                    $campos[]=$campo;
                }                
            }
        }
        $datosR=array();
        foreach($campos as $campo){
            $datosR[$campo]=$registros[$campo];
        }
        $datosF[]=$datosR;        
    }
    return $datosF;
}

function conexion(){
    $usuario = "root";
    $contrasena = "";  // en mi caso tengo contraseña pero en casa caso introducidla aquí.
    $servidor = "localhost";
    $basededatos = "encuestas";
    $conexion = mysqli_connect( $servidor, $usuario, $contrasena ) or die ("No se ha podido conectar al servidor de Base de datos");
    $db = mysqli_select_db( $conexion, $basededatos ) or die ( "Upps! Pues va a ser que no se ha podido conectar a la base de datos" );
    return $conexion;
}

?>