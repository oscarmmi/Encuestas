<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuestas</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <!-- CSS -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <!-- Default theme -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />
    <!-- Semantic UI theme -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/semantic.min.css" />
    <!-- Bootstrap theme -->
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/bootstrap.min.css" />

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.min.js" integrity="sha256-TQq84xX6vkwR0Qs1qH5ADkP+MvH0W+9E7TdHJsoIQiM=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.js" integrity="sha256-nZaxPHA2uAaquixjSDX19TmIlbRNCOrf5HO1oHl5p70=" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.css" integrity="sha256-IvM9nJf/b5l2RoebiFno92E5ONttVyaEEsdemDC6iQA=" crossorigin="anonymous" />


    <script type="text/javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.3.1/css/select.dataTables.min.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css" />

    <script src="funciones.js"></script>
</head>

<body>
    <div class="container">
        <div class="row top-buffer">
            <div class="col-lg-4">
                <h4 class="text-success">Encuesta</h4>
            </div>
        </div>
        <div class="row top-buffer">
            <div class="col-lg-4">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon3" style="cursor:help;" title="Tipo de Documento">T.Doc</span>
                    <select class="form-control tiposdedocumento" id="idtipodedocumento"></select>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon3" style="cursor:help;" title="Número de Documento">#Documento</span>
                    <input type="text" class="form-control" id="numerodocumento" aria-describedby="basic-addon3">
                </div>
            </div>
            <div class="col-lg-4">
                <button id="btnValidarDocumento" class="btn btn-primary mt-3">Validar Documento</button>
            </div>
        </div>
        <br />
        <div class="row top-buffer hidden" id="ingresarDatosUsuarios">
            <div class="col-lg-12">
                <div class="panel-group">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h4 class="panel-title">Datos del Usuario</h4>
                        </div>
                        <div class="panel-body">
                            <input type="hidden" id="usuario_idusuario">
                            <div class="row top-buffer">
                                <div class="col-lg-4">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3" style="cursor:help;" title="Tipo de Documento">T.Doc</span>
                                        <select class="form-control usuario tiposdedocumento" id="usuario_idtipodedocumento"></select>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3" style="cursor:help;" title="Número de Documento">#Documento</span>
                                        <input type="text" class="form-control usuario" id="usuario_numerodocumento" aria-describedby="basic-addon3">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3" style="cursor:help;" title="Nombres del Usuario">Nombres</span>
                                        <input type="text" class="form-control usuario" id="usuario_nombres" aria-describedby="basic-addon3">
                                    </div>
                                </div>
                            </div>
                            <br />
                            <div class="row top-buffer">
                                <div class="col-lg-4">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon3" style="cursor:help;" title="Apellidos del Usuario">Apellidos</span>
                                        <input type="text" class="form-control usuario" id="usuario_apellidos" aria-describedby="basic-addon3">
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <button id="btnGuardarUsuario" class="btn btn-primary">Guardar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row top-buffer container-fluid hidden" id="graficas">
            <div class="col-lg-12">
                <a id="btnMostrarResultadosGrafica" class="btn btn-primary" href="#graficaResultados" data-toggle="collapse">Mostrar los resultados</a>
            </div>
            <br/><br/>
            <div id="graficaResultados" class="collapse" class="col-lg-12">
                <div class="panel-group">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h4 class="panel-title">Grafica con los resultados de la encuesta</h4>
                        </div>
                        <div class="panel-body">
                            <div class="row top-buffer">
                                <div id="convencionesGrafica" class="col-lg-12">

                                </div>
                            </div>
                            <div class="row top-buffer">
                                <div class="col-lg-12">
                                    <canvas id="marksChart" width="600" height="400"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row top-buffer" id="categorias">

        </div>
        <div class="modal" id="modalrespuestas">
            <div class="modal-dialog modal-medium">
                <div class="modal-content" style="overflow: visible;">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Elija su respuesta</h4>
                    </div>
                    <div class="modal-body" style="font-size: 9pt;">
                        <input type="hidden" id="respuestas_idpreguntarespuesta" class="respuestas">
                        <input type="hidden" id="respuestas_idpregunta" class="respuestas">
                        <input type="hidden" id="respuestas_idcategoria" class="respuestas">
                        <div class="row container-fluid">
                            <div class="col-lg-12">
                                <h4 id="respuestas_descripcionpregunta" class="text-success"></h4>
                            </div>
                            <div class="col-lg-12 top-buffer">
                                <div class="input-group">
                                    <span class="input-group-addon input-fix-sm">
                                        <div class="input-group-fix-width" style="cursor:help;" title="Respuesta">
                                            Rta</div>
                                    </span>
                                    <select class="form-control respuestas" id="respuestas_idrespuesta"></select>
                                </div>
                            </div>
                            <div class="col-lg-12" style="margin-top:10px;">
                                <div class="input-group">
                                    <span class="input-group-addon" style="cursor:help;" title="Observación">Observación</span>
                                    <input type="text" class="form-control respuestas" id="respuestas_observacion">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="btnSeleccionarRespuesta" class="btn btn-primary btn-sm">
                            Seleccionar</button>
                        <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
</body>

</html>
