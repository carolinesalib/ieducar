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
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Avaliacao
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'lib/Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Business/Professor.php';

/**
 * ComponenteCurricularController class.
 *
 * @author      Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category    i-Educar
 * @license     @@license@@
 * @package     Avaliacao
 * @subpackage  Modules
 * @since       Classe disponível desde a versão 1.1.0
 * @version     @@package_version@@
 */
class ComponenteCurricularController extends ApiCoreController
{

  protected function canGetComponentesCurriculares() {
    return $this->validatesId('turma') &&
           $this->validatesPresenceOf('ano');
  }

  protected function getComponentesCurriculares() {
    if ($this->canGetComponentesCurriculares()) {

      $userId        = $this->getSession()->id_pessoa;
      $instituicaoId = $this->getRequest()->instituicao_id;
      $turmaId       = $this->getRequest()->turma_id;
      $ano           = $this->getRequest()->ano;

      $isProfessor   = Portabilis_Business_Professor::isProfessor($instituicaoId, $userId);

      if ($isProfessor) {
        $componentesCurriculares = Portabilis_Business_Professor::componentesCurricularesAlocado(
          $turmaId, $ano, $userId
        );
      }

      else {
        $sql = "SELECT cc.id,
                       cc.nome,
                       ac.nome AS area_conhecimento,
                       ac.secao AS secao_area_conhecimento
                FROM pmieducar.turma,
                     modules.componente_curricular_turma AS cct,
                     modules.componente_curricular AS cc,
                     modules.area_conhecimento AS ac,
                     pmieducar.escola_ano_letivo AS al
                WHERE turma.cod_turma = $1
                  AND cct.turma_id = turma.cod_turma
                  AND cct.escola_id = turma.ref_ref_cod_escola
                  AND cct.componente_curricular_id = cc.id
                  AND al.ano = $2
                  AND cct.escola_id = al.ref_cod_escola
                  AND cc.area_conhecimento_id = ac.id
                ORDER BY ac.secao,
                         ac.nome,
                         cc.nome";

        $componentesCurriculares = $this->fetchPreparedQuery($sql, array($turmaId, $ano));

        if (count($componentesCurriculares) < 1) {
          $sql = "SELECT DISTINCT cc.id,
                         cc.nome,
                         ac.nome AS area_conhecimento,
                         ac.secao AS secao_area_conhecimento
                  FROM pmieducar.turma AS t,
                       pmieducar.turma_serie AS ts,
                       pmieducar.escola_serie_disciplina AS esd,
                       modules.componente_curricular AS cc,
                       modules.area_conhecimento AS ac,
                       pmieducar.escola_ano_letivo AS al
                  WHERE t.cod_turma = $1
                    AND ts.ref_cod_turma = t.cod_turma
                    AND esd.ref_ref_cod_escola = t.ref_ref_cod_escola
                    AND (esd.ref_ref_cod_serie = t.ref_ref_cod_serie OR esd.ref_ref_cod_serie = ts.ref_cod_serie)
                    AND esd.ref_cod_disciplina = cc.id
                    AND al.ano = $2
                    AND esd.ref_ref_cod_escola = al.ref_cod_escola
                    AND t.ativo = 1
                    AND esd.ativo = 1
                    AND al.ativo = 1
                    AND cc.area_conhecimento_id = ac.id
                  ORDER BY ac.secao,
                           ac.nome,
                           cc.nome";

          $componentesCurriculares = $this->fetchPreparedQuery($sql, array($turmaId, $ano));
        }
      }

      $options = array();
      foreach ($componentesCurriculares as $componenteCurricular) {
      	$areaConhecimento = (($componenteCurricular['secao_area_conhecimento'] != '') ? $componenteCurricular['secao_area_conhecimento'] . ' - ' : '') . $componenteCurricular['area_conhecimento'];
      	$options['__' . $componenteCurricular['id']] = array(
      		'value' => $this->toUtf8($componenteCurricular['nome']),
      		'group' => $this->toUtf8($areaConhecimento)
      	);
      }

      return array('options' => $options);
    }
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'componentesCurriculares'))
      $this->appendResponse($this->getComponentesCurriculares());
    else
      $this->notImplementedOperationError();
  }
}
