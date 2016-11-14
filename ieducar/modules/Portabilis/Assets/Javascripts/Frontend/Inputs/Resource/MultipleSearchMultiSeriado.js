var $cursoField = getElementFor('curso');
var $serieField = getElementFor('serie');
var $turmaField = $j('#cod_turma');
var $multiseriadoField = $j('#multiseriado');

$multiseriadoField.css('width', '307px');

var handleGetMultiseriado = function(dataResponse) {
  updateChozen($multiseriadoField, dataResponse['options']);

  // Seleciona as séries já cadastradas para a turma
  if ($turmaField.val()) {

    var additionalVars = {
      turma_id : $turmaField.val()
    };

    var options = {
      url      : getResourceUrlBuilder.buildUrl('/module/Api/MultiSeriado', 'multiseriado-turma', additionalVars),
      dataType : 'json',
      data     : {},
      success  : handleGetMultiseriadoSelected,
    };

    getResource(options);
  }
}

var handleGetMultiseriadoSelected = function(dataResponse) {
  $j.each(dataResponse.series, function(id, value) {
    $multiseriadoField.children("[value='" + id + "']").attr('selected', '');
  });

  $multiseriadoField.trigger("liszt:updated");
}

var updateMultiseriado = function() {
  clearValues($multiseriadoField);

  // Adiciona no componente todas séries para o curso
  if ($cursoField.val() && $serieField.val()) {

    var additionalVars = {
      curso_id : $cursoField.val(),
      serie_id  : $serieField.val()
    };

    var options = {
      url      : getResourceUrlBuilder.buildUrl('/module/Api/MultiSeriado', 'multiseriado-curso', additionalVars),
      dataType : 'json',
      data     : {},
      success  : handleGetMultiseriado,
    };

    getResource(options);
  }
}

$serieField.change(updateMultiseriado);

updateMultiseriado();