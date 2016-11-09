<?php

#error_reporting(E_ALL);
#ini_set("display_errors", 1);

/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Caroline Salib <caroline@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Api
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'lib/Portabilis/Array/Utils.php';
require_once 'lib/Portabilis/String/Utils.php';
require_once "App/Model/IedFinder.php";

class MultiSeriadoController extends ApiCoreController
{
  // search options

  protected function searchOptions() {
    return array('namespace' => 'cadastro', 'labelAttr' => 'nm_serie', 'idAttr' => 'cod_serie');
  }

  protected function formatResourceValue($resource) {
    return $this->toUtf8($resource['name'], array('transform' => true));
  }

  protected function canGetMultiSeriado() {
    return ($this->validatesId('curso') && $this->validatesId('serie'));
  }

  protected function getSeriesOfCurso() {
    if (!$this->canGetMultiSeriado()) return;

    $curso = $this->getRequest()->curso_id;
    $serie = $this->getRequest()->serie_id;

    $sql = "SELECT DISTINCT
                s.cod_serie AS id,
                s.nm_serie AS name
               FROM pmieducar.serie s
              INNER JOIN pmieducar.escola_serie es on (es.ref_cod_serie = s.cod_serie and es.ativo = 1)
              WHERE s.ativo = 1
                AND s.ref_cod_curso = $1
                AND s.cod_serie <> $2
              ORDER BY s.nm_serie ASC";

    $series = $this->fetchPreparedQuery($sql, array($curso, $serie));

    foreach ($series as $key => $value) {
      $series[$key]['name'] = Portabilis_String_Utils::toUtf8($series[$key]['name']);
    }

    $options = array();
    $options = Portabilis_Array_Utils::setAsIdValue($series, 'id', 'name');

    return array('options' => $options);
  }

  protected function getSeriesOfTurma() {
    if (!$this->validatesId('turma')) return;

    $turma = $this->getRequest()->turma_id;

    $sql = "SELECT serie.cod_serie AS id,
                   serie.nm_serie AS name
              FROM pmieducar.turma_serie
             INNER JOIN pmieducar.serie ON (serie.cod_serie = turma_serie.ref_cod_serie) 
             WHERE ref_cod_turma = $1
             ORDER BY serie.nm_serie;";

    $series = $this->fetchPreparedQuery($sql, array($turma));

    foreach ($series as $key => $value) {
      $series[$key]['name'] = Portabilis_String_Utils::toUtf8($series[$key]['name']);
    }

    $options = array();
    $options = Portabilis_Array_Utils::setAsIdValue($series, 'id', 'name');

    return array('series' => $options);
  }


  public function Gerar() {
    if ($this->isRequestFor('get', 'multiseriado-search'))
      $this->appendResponse($this->search());
    else if ($this->isRequestFor('get', 'multiseriado-curso'))
      $this->appendResponse($this->getSeriesOfCurso());
    else if ($this->isRequestFor('get', 'multiseriado-turma'))
      $this->appendResponse($this->getSeriesOfTurma());
    else
      $this->notImplementedOperationError();
  }
}
