var VALIDAR_DOCUMENTO = 1;
var CARGAR_DATOS_INICIALES = 2;
var GUARDAR_USUARIO = 3;
var SELECCIONAR_RESPUESTA = 4;
var BUSCAR_TOTALES=5;

var aRespuestas = [];
var radarChart;

$(document).ready(function () {
    cargarDatosIniciales();
    funcionesClick();
});

function crearTablaCorrespondiente(id, datos) {
    if (parseInt(datos.length) === 0) {
        datos = [];
    }
    let tabla = $('#' + id).DataTable({
        paging: 'numbers',
        bFilter: false,
        destroy: true,
        select: true,
        dom: 'T<"clear">lfrtip', // Permite cargar la herramienta tableTools
        tableTools: {
            aButtons: [],
            sRowSelect: "single"
        },
        data: datos,
        columns: [{
                data: 'descripcion',
                title: 'Descripcion de la Pregunta',
                className: 'text-capitalize'
            },
            {
                data: 'observacion',
                title: 'Observacion',
                className: 'text-capitalize'
            },
            {
                data: 'idpreguntarespuesta',
                title: 'Respondida',
                className: 'text-capitalize dt-body-center',
                render: function (data, type, full, meta) {
                    let html = 'No';
                    if (data) {
                        html = 'Si';
                    }
                    return html;
                }
            }
        ],
        fnRowCallback: function (nRow, aData) {
            if (aData.idpreguntarespuesta) {
                $(nRow).prop('title', 'Pregunta resuelta');
                $(nRow).css({
                    "color": "#5cb85c"
                });
            } else {
                $(nRow).prop('title', 'Sin responder');
                $(nRow).css({
                    "color": "#337ab7"
                });
            }
            return nRow;
        }
    });
    $('#' + id + ' tbody').on('click', 'tr', function () {
        abrirModalRespuestas(tabla.row(this).data());
    });
}

function abrirModalRespuestas(data) {
    $(".respuestas").val('');
    $(".respuestas").html('');
    $("#modalrespuestas").modal('show');
    $("#respuestas_idpreguntarespuesta").val(data.idpreguntarespuesta);
    $("#respuestas_idpregunta").val(data.idpregunta);
    $("#respuestas_descripcionpregunta").html(data.descripcion);
    $("#respuestas_idcategoria").val(data.idcategoria);
    $("#respuestas_observacion").val(data.observacionrespuesta);
    let respuestasCadena = data.respuestas.split(",");
    respuestasCadena.sort();
    let respuestasxPregunta = [];
    for (let a in respuestasCadena) {
        for (let b in aRespuestas) {
            if (parseInt(respuestasCadena[a]) === parseInt(aRespuestas[b]['codigo'])) {
                respuestasxPregunta.push(aRespuestas[b]);
            }
        }
    }
    cargarOpcionesenUnSelect('#respuestas_idrespuesta', respuestasxPregunta);
    if (data.idrespuesta) {
        $("#respuestas_idrespuesta").val(data.idrespuesta);
    }
}

function cargarDatosIniciales() {
    $.ajax({
        url: 'acciones.php',
        data: {
            accion: CARGAR_DATOS_INICIALES
        },
        type: 'POST',
        dataType: 'json',
        success: function (respuesta) {
            cargarOpcionesenUnSelect(".tiposdedocumento", respuesta.datos);
            aRespuestas = respuesta.respuestas;
        }
    });
}

function cargarOpcionesenUnSelect(identificador, datos) {
    let html = '';
    for (let a in datos) {
        html += '<option value="' + datos[a]['codigo'] + '" class="text-capitalize">' + datos[a]['nombre'] + '</option>';
    }
    $(identificador).html(html);
}

function funcionesClick() {
    $("#btnValidarDocumento").click(validarDocumento);
    $("#btnGuardarUsuario").click(guardarUsuario);
    $("#btnSeleccionarRespuesta").click(confirmarSeleccionarRespuesta);
    $("#btnMostrarResultadosGrafica").click(buscarTotalesxCrearGrafica);
}

function buscarTotalesxCrearGrafica(){
    if($("#graficaResultados").hasClass('in')){
        return;
    }
    let idusuario = $("#usuario_idusuario").val();
    if (!$.isNumeric(idusuario)) {
        alertify.error("- El usuario seleccionado no es valido");
        return;
    }
    $.ajax({
        url: 'acciones.php',
        data: {
            accion: BUSCAR_TOTALES,
            idusuario: idusuario
        },
        type: 'POST',
        dataType: 'json',
        success: function (respuesta) {
            if (respuesta.errorvalidacion) {
                alertify.error(respuesta.errorvalidacion);
            } else {
                cargarImagenconlosResultados(respuesta.datos, respuesta.categorias, respuesta.totales);
            }
        }
    });
}

function cargarImagenconlosResultados(datos, categorias, totales){
    let aLabels=[];
    for (let a in categorias) {
        aLabels.push(categorias[a]['nombre']);
    }
    let aTotales=[];
    let aColores=[];
    for (let a in categorias) {
        for (let b in totales) {
            if(parseInt(categorias[a]['codigo'])===parseInt(totales[b]['idcategoria'])){
                aTotales.push(parseInt(totales[b]['total']));
                categorias[a]['total']=parseInt(totales[b]['total']);
                let colorAleatorio="rgb("+Math.floor((Math.random() * 255) + 1)+","+Math.floor((Math.random() * 255) + 1)+","+Math.floor((Math.random() * 255) + 1)+")";
                categorias[a]['color']=colorAleatorio;
                aColores.push(colorAleatorio);
                break;
            }
        }
    }
    let convenciones='';
    for (let a in categorias) {
      convenciones+='<h5><span style="background:'+categorias[a]['color']+';border:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;&nbsp;'+categorias[a]['nombre']+'</h5>';
    }
    $("#convencionesGrafica").html(convenciones);
    var marksCanvas = document.getElementById("marksChart");
    var marksData = {
    labels: aLabels,
    datasets: [{
        label: datos.nombres+' '+datos.apellidos,
        radius: 6,
        pointRadius: 6,
        pointHoverRadius: 10,
        pointBorderWidth: 3,
        pointBackgroundColor: aColores, 
        backgroundColor: "rgba(76, 255, 51, 0.2)",
        data: aTotales
    }]
    };
    var radarChart = new Chart(marksCanvas, {
        type: 'radar',
        data: marksData
    });
}

function confirmarSeleccionarRespuesta() {
    let idusuario = $("#usuario_idusuario").val();
    if (!$.isNumeric(idusuario)) {
        alertify.error("- El usuario seleccionado no es valido");
        return;
    }
    let idpreguntarespuesta = $("#respuestas_idpreguntarespuesta").val();
    let idpregunta = $("#respuestas_idpregunta").val();
    if (!$.isNumeric(idpregunta) && !$.isNumeric(idpregunta)) {
        alertify.error("- La pregunta seleccionada no es valida");
        return;
    }
    let idrespuesta = $("#respuestas_idrespuesta").val();
    if (!$.isNumeric(idrespuesta)) {
        alertify.error("- Seleccione una respuesta valida");
        return;
    }
    $("#modalrespuestas").modal('hide');
    setTimeout(
        function () {
            alertify.confirm(
                'Esta seguro de seleccionar esta respuesta? ',
                function () {
                    seleccionarRespuesta(idusuario, idpreguntarespuesta, idpregunta, idrespuesta);
                },
                function () {
                    setTimeout(
                        function () {
                            $("#modalrespuestas").modal('show');
                        }, 100
                    );
                }
            );
        }, 100
    );

}

function seleccionarRespuesta(idusuario, idpreguntarespuesta, idpregunta, idrespuesta) {
    let observacion = $("#respuestas_observacion").val();
    let idcategoria = $("#respuestas_idcategoria").val();
    $.ajax({
        url: 'acciones.php',
        data: {
            accion: SELECCIONAR_RESPUESTA,
            idusuario: idusuario,
            idpreguntarespuesta: idpreguntarespuesta,
            idpregunta: idpregunta,
            idrespuesta: idrespuesta,
            idcategoria: idcategoria,
            observacion: observacion
        },
        type: 'POST',
        dataType: 'json',
        success: function (respuesta) {
            if (respuesta.errorvalidacion) {
                alertify.error(respuesta.errorvalidacion);
            } else {
                cargarTablaCorrespondiente('#tabla_' + idcategoria, respuesta[idcategoria]);
            }
        }
    });
}

function cargarTablaCorrespondiente(idtabla, datos) {
    var tabla = $(idtabla).dataTable();
    tabla.fnClearTable();
    if (datos.length > 0) {
        tabla.fnAddData(datos);
    }
}

function guardarUsuario() {
    let idtipodedocumento = $("#usuario_idtipodedocumento").val();
    if (!$.isNumeric(idtipodedocumento)) {
        alertify.error("- El tipo de documento seleccionado no es valido");
        return;
    }
    let numerodocumento = $("#usuario_numerodocumento").val();
    if (!$.isNumeric(numerodocumento)) {
        alertify.error("- El numero documento seleccionado no es valido");
        return;
    }
    let nombres = $("#usuario_nombres").val();
    if (nombres == '') {
        alertify.error("- Debe ingresar los nombres del usuario");
        return;
    }
    let apellidos = $("#usuario_apellidos").val();
    if (apellidos == '') {
        alertify.error("- Debe ingresar los apellidos del usuario");
        return;
    }
    $.ajax({
        url: 'acciones.php',
        data: {
            accion: GUARDAR_USUARIO,
            idtipodedocumento: idtipodedocumento,
            numerodocumento: numerodocumento,
            nombres: nombres,
            apellidos: apellidos
        },
        type: 'POST',
        dataType: 'json',
        success: function (respuesta) {
            if (respuesta.errorvalidacion) {
                alertify.error(respuesta.errorvalidacion);
            }
            if (respuesta.creado) {
                $("#usuario_idtipodedocumento").val(idtipodedocumento);
                $("#usuario_numerodocumento").val(numerodocumento);
                $("#btnValidarDocumento").click();
            }
        }
    });
}

function limpiarCamposUsuario() {
    $(".usuario").val('');
}

function validarDocumento() {
    let idtipodedocumento = $("#idtipodedocumento").val();
    if (!$.isNumeric(idtipodedocumento)) {
        alertify.error("- El tipo de documento seleccionado no es valido");
        return;
    }
    let numerodocumento = $("#numerodocumento").val();
    if (!$.isNumeric(numerodocumento)) {
        alertify.error("- El numero documento seleccionado no es valido");
        return;
    }
    $.ajax({
        url: 'acciones.php',
        data: {
            accion: VALIDAR_DOCUMENTO,
            idtipodedocumento: idtipodedocumento,
            numerodocumento: numerodocumento
        },
        type: 'POST',
        dataType: 'json',
        success: function (respuesta) {
            $("#ingresarDatosUsuarios").removeClass('hidden');
            $("#graficas").removeClass('hidden');
            limpiarCamposUsuario();
            if (!respuesta.mensajeerror) {
                cargarDatosenCamposCorrespondientes("#usuario_", respuesta.datos);
                crearPestannasxCategorias(respuesta.categorias);
                crearTablasCorrespondientesalasCategorias(respuesta.categorias, respuesta.preguntasxcategoria);
                alertify.success("- El usuario ya se encuentra registrado");
            } else {
                $("#usuario_idtipodedocumento").val(idtipodedocumento);
                $("#usuario_numerodocumento").val(numerodocumento);
                alertify.error(respuesta.mensajeerror);
            }
        }
    });
}

function crearTablasCorrespondientesalasCategorias(categorias, preguntasxcategoria) {
    for (let a in categorias) {
        crearTablaCorrespondiente('tabla_' + categorias[a]['codigo'], preguntasxcategoria[categorias[a]['codigo']]);
    }
    $(".tabcategorias").removeClass('in');
    $(".tabcategorias").removeClass('active');
    $($(".licategorias")[0]).click();
}

function crearPestannasxCategorias(categorias) {
    let html = '';
    let ulhtml = '<ul class="nav nav-pills">';
    let tablhtml = '<div class="tab-content">';
    for (let a in categorias) {
        ulhtml += '<li><a data-toggle="pill" class="licategorias" href="#categoria_' + categorias[a]['codigo'] + '">' + categorias[a]['nombre'] + '</a></li>';
        tablhtml +=
            '<div id="categoria_' + categorias[a]['codigo'] + '" class="tabcategorias tab-pane fade in active" >' +
            '<div class="row top-buffer">' +
            '<div class="col-lg-12">' +
            '<h5 class="text-success">Lista de Preguntas de la Categoria ' + categorias[a]['nombre'] + '</h5>' +
            '</div>' +
            '</div>' +
            '<div class="row top-buffer">' +
            '<div class="col-lg-2">' +
            '<h5 class="text-primary"><span style="background:#337ab7;border:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;&nbsp;Sin responder</h5>' +
            '</div>' +
            '<div class="col-lg-2">' +
            '<h5 class="text-success"><span style="background:#5cb85c;border:1px solid black;">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;&nbsp;Pregunta resuelta</h5>' +
            '</div>' +
            '</div>' +
            '<div class="row top-buffer">' +
            '<div class="col-lg-12">' +
            '<table id="tabla_' + categorias[a]['codigo'] + '" class="table table-bordered table-striped display"></table>' +
            '</div>' +
            '</div>' +
            '</div>';
    }
    if (html == '') {
        html = ulhtml + '</ul>' + tablhtml + '</div>';
    }
    $("#categorias").html(html);
}

function cargarDatosenCamposCorrespondientes(prefijo, datos) {
    for (let a in datos) {
        $(prefijo + a).val(datos[a]);
    }
}
